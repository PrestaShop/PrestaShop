<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Security\GeneralConfiguration;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use RuntimeException;

final class TrustedDeviceRevocationConfigurationTest extends TestCase
{
    public function testGlobalDisableRevokesTrustEvenWithoutRemainingTwoFactorData(): void
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->with('PS_BACKOFFICE_2FA')->willReturn(true);
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->method('hasAnyEmployeeWithTwoFactorData')->willReturn(false);
        $repository->expects(self::once())->method('resetAllTwoFactorData')->willReturn(1);
        self::assertSame([], (new GeneralConfiguration($configuration, $repository))->updateConfiguration(['token' => true, '2fa' => false]));
    }

    #[DataProvider('transitionsWithoutReset')]
    public function testOtherGlobalTransitionsDoNotReset(bool $before, bool $after): void
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturn($before);
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->expects(self::never())->method('resetAllTwoFactorData');
        (new GeneralConfiguration($configuration, $repository))->updateConfiguration(['token' => true, '2fa' => $after]);
    }

    public static function transitionsWithoutReset(): iterable
    {
        yield 'already disabled' => [false, false];
        yield 'enabled' => [false, true];
        yield 'unchanged enabled' => [true, true];
    }

    public function testFailedResetDoesNotDisableTheGlobalSetting(): void
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturn(true);
        $configuration->expects(self::once())->method('set')->with('PS_SECURITY_TOKEN', true);
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->method('hasAnyEmployeeWithTwoFactorData')->willReturn(false);
        $repository->method('resetAllTwoFactorData')->willThrowException(new RuntimeException('Reset failed.'));
        $this->expectException(RuntimeException::class);
        (new GeneralConfiguration($configuration, $repository))->updateConfiguration(['token' => true, '2fa' => false]);
    }

    public function testFailedConfigurationWriteDoesNotAttemptToRestoreTrust(): void
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturn(true);
        $resetCompleted = false;
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->method('hasAnyEmployeeWithTwoFactorData')->willReturn(true);
        $repository->expects(self::once())->method('resetAllTwoFactorData')->willReturnCallback(static function () use (&$resetCompleted): int {
            $resetCompleted = true;

            return 1;
        });
        $configuration->method('set')->willReturnCallback(static function (string $key) use (&$resetCompleted): void {
            if ('PS_BACKOFFICE_2FA' === $key) {
                self::assertTrue($resetCompleted);
                throw new RuntimeException('Configuration write failed.');
            }
        });
        $this->expectException(RuntimeException::class);
        (new GeneralConfiguration($configuration, $repository))->updateConfiguration(['token' => true, '2fa' => false]);
    }
}
