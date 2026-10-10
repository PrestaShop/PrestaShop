<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Command;

use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Csp\CspFeatureChecker;
use PrestaShop\PrestaShop\Adapter\Csp\CspViolationRecorder;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShopBundle\Command\PruneCspLogCommand;
use PrestaShopBundle\Entity\Repository\CspLogRepository;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * CspViolationRecorder and CspFeatureChecker are final, so real instances are used over mocked
 * dependencies; the recorder's enforceRowCap() calls countByShop(), which the tests assert as the
 * cap-enforcement signal.
 */
class PruneCspLogCommandTest extends TestCase
{
    /** A real (final) CspFeatureChecker reporting the feature flag + per-surface enabled state as $enabled. */
    private function featureChecker(bool $enabled): CspFeatureChecker
    {
        $flagChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $flagChecker->method('isEnabled')->willReturn($enabled);

        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('get')->willReturn($enabled);

        return new CspFeatureChecker($flagChecker, $configuration);
    }

    public function testItAgePrunesEveryShopWithLogsAndEnforcesTheCap(): void
    {
        $logRepository = $this->createMock(CspLogRepository::class);
        // Two storefront shops have logs; the back office has none in this test.
        $logRepository->method('distinctShopIds')->willReturnCallback(
            fn (CspContext $context) => CspContext::FRONT === $context ? [1, 2] : []
        );
        // Shop 1 has a 30-day retention, shop 2 keeps everything (0), so delete runs only for shop 1.
        $logRepository->expects($this->once())
            ->method('deleteOlderThanByShop')
            ->with(CspContext::FRONT, 1, $this->isInstanceOf(DateTimeInterface::class))
            ->willReturn(3);
        // enforceRowCap() runs for both shops (via countByShop).
        $logRepository->expects($this->exactly(2))->method('countByShop')->willReturn(0);

        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('get')->willReturnCallback(
            fn (string $key, $default, $shopConstraint) => 1 === $shopConstraint->getShopId()->getValue() ? 30 : 0
        );

        $tester = new CommandTester(new PruneCspLogCommand(
            $logRepository,
            new CspViolationRecorder($logRepository),
            $configuration,
            $this->featureChecker(true)
        ));
        $exitCode = $tester->execute([]);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Shop 1: deleted 3 report(s) older than 30 day(s)', $tester->getDisplay());
    }

    public function testTheOlderThanOptionOverridesTheRetentionSetting(): void
    {
        $logRepository = $this->createMock(CspLogRepository::class);
        $logRepository->expects($this->once())
            ->method('deleteOlderThanByShop')
            ->with(CspContext::FRONT, 5, $this->isInstanceOf(DateTimeInterface::class))
            ->willReturn(0);
        $logRepository->method('countByShop')->willReturn(0);

        $configuration = $this->createMock(ShopConfigurationInterface::class);
        // The override is used, so the stored retention is never read.
        $configuration->expects($this->never())->method('get');

        $tester = new CommandTester(new PruneCspLogCommand(
            $logRepository,
            new CspViolationRecorder($logRepository),
            $configuration,
            $this->featureChecker(true)
        ));

        $this->assertSame(0, $tester->execute(['--shop' => '5', '--older-than' => '7']));
    }

    public function testItSkipsSurfacesWhereTheFeatureIsDisabledAndDeletesNothing(): void
    {
        $logRepository = $this->createMock(CspLogRepository::class);
        $logRepository->method('distinctShopIds')->willReturnCallback(
            fn (CspContext $context) => CspContext::FRONT === $context ? [1] : []
        );
        // Disabling the feature must stop all background deletion: no age-prune and no row-cap COUNT.
        $logRepository->expects($this->never())->method('deleteOlderThanByShop');
        $logRepository->expects($this->never())->method('countByShop');

        $tester = new CommandTester(new PruneCspLogCommand(
            $logRepository,
            new CspViolationRecorder($logRepository),
            $this->createMock(ShopConfigurationInterface::class),
            $this->featureChecker(false)
        ));

        $exitCode = $tester->execute([]);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('skipped (Content Security Policy is disabled)', $tester->getDisplay());
    }

    public function testItRejectsANonNumericOlderThan(): void
    {
        $logRepository = $this->createMock(CspLogRepository::class);

        $tester = new CommandTester(new PruneCspLogCommand(
            $logRepository,
            new CspViolationRecorder($logRepository),
            $this->createMock(ShopConfigurationInterface::class),
            $this->featureChecker(true)
        ));

        $this->assertSame(2, $tester->execute(['--older-than' => 'soon']));
    }
}
