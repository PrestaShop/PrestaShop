<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShopBundle\Command\CspAdminReportOnlyCommand;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * The lockout-recovery command forces the back-office CSP back to report-only without touching the
 * rest of the admin settings.
 */
class CspAdminReportOnlyCommandTest extends TestCase
{
    public function testItForcesReportOnlyWhileKeepingTheOtherSettings(): void
    {
        $configuration = $this->createMock(DataConfigurationInterface::class);
        $configuration->method('getConfiguration')->willReturn([
            'enabled' => true,
            'report_only' => false,
            'retention_days' => 30,
        ]);
        $configuration->expects($this->once())
            ->method('updateConfiguration')
            ->with([
                'enabled' => true,
                'report_only' => true,
                'retention_days' => 30,
            ])
            ->willReturn([]);

        $tester = new CommandTester(new CspAdminReportOnlyCommand($configuration));

        $this->assertSame(0, $tester->execute([]));
        $this->assertStringContainsString('report-only', $tester->getDisplay());
    }

    public function testItIsIdempotentWhenAlreadyReportOnly(): void
    {
        $configuration = $this->createMock(DataConfigurationInterface::class);
        $configuration->method('getConfiguration')->willReturn([
            'enabled' => true,
            'report_only' => true,
            'retention_days' => 0,
        ]);
        $configuration->expects($this->once())
            ->method('updateConfiguration')
            ->with($this->callback(static fn (array $config): bool => true === $config['report_only']))
            ->willReturn([]);

        $tester = new CommandTester(new CspAdminReportOnlyCommand($configuration));

        $this->assertSame(0, $tester->execute([]));
    }
}
