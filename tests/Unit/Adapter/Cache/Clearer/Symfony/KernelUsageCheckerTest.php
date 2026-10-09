<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Cache\Clearer\Symfony;

use AdminAPIKernel;
use AdminKernel;
use FrontKernel;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Cache\Clearer\Symfony\KernelUsageChecker;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShopBundle\Entity\Repository\ApiClientRepository;
use RuntimeException;

class KernelUsageCheckerTest extends TestCase
{
    public function testAdminKernelIsAlwaysUsed(): void
    {
        $checker = $this->createChecker(frontContainerEnabled: false, adminApiEnabled: false, apiClientsCount: 0);

        $this->assertTrue($checker->isUsed(AdminKernel::APP_ID));
    }

    public function testFrontKernelFollowsFrontContainerFeatureFlag(): void
    {
        $this->assertFalse($this->createChecker(frontContainerEnabled: false)->isUsed(FrontKernel::APP_ID));
        $this->assertTrue($this->createChecker(frontContainerEnabled: true)->isUsed(FrontKernel::APP_ID));
    }

    /**
     * @dataProvider provideAdminApiStates
     */
    public function testAdminAPIKernelNeedsAdminApiEnabledAndAnApiClient(bool $adminApiEnabled, int $apiClientsCount, bool $expected): void
    {
        $checker = $this->createChecker(adminApiEnabled: $adminApiEnabled, apiClientsCount: $apiClientsCount);

        $this->assertSame($expected, $checker->isUsed(AdminAPIKernel::APP_ID));
    }

    public static function provideAdminApiStates(): iterable
    {
        yield 'enabled with clients' => [true, 2, true];
        yield 'enabled without client' => [true, 0, false];
        yield 'disabled with clients' => [false, 2, false];
    }

    public function testKernelIsConsideredUsedWhenCheckFails(): void
    {
        $featureFlagStateChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $featureFlagStateChecker->method('isEnabled')->willThrowException(new RuntimeException());
        $apiClientRepository = $this->createMock(ApiClientRepository::class);
        $apiClientRepository->method('count')->willThrowException(new RuntimeException());
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturn('1');
        $checker = new KernelUsageChecker($featureFlagStateChecker, $configuration, $apiClientRepository);

        $this->assertTrue($checker->isUsed(FrontKernel::APP_ID));
        $this->assertTrue($checker->isUsed(AdminAPIKernel::APP_ID));
    }

    private function createChecker(bool $frontContainerEnabled = false, bool $adminApiEnabled = true, int $apiClientsCount = 1): KernelUsageChecker
    {
        $featureFlagStateChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $featureFlagStateChecker->method('isEnabled')
            ->with(FeatureFlagSettings::FEATURE_FLAG_FRONT_CONTAINER_V2)
            ->willReturn($frontContainerEnabled);
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->with('PS_ENABLE_ADMIN_API')->willReturn($adminApiEnabled ? '1' : '0');
        $apiClientRepository = $this->createMock(ApiClientRepository::class);
        $apiClientRepository->method('count')->willReturn($apiClientsCount);

        return new KernelUsageChecker($featureFlagStateChecker, $configuration, $apiClientRepository);
    }
}
