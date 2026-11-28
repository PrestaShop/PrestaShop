<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Security\Admin;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Security\Admin\TwoFactorSetupRequirementChecker;
use Scheb\TwoFactorBundle\Model\Totp\TwoFactorInterface;

class TwoFactorSetupRequirementCheckerTest extends TestCase
{
    #[DataProvider('setupStates')]
    public function testGlobalSettingAndEffectiveProvidersDetermineWhetherSetupIsRequired(
        bool $global,
        bool $required,
        bool $enabled,
        bool $email,
        bool $totp,
        bool $expected
    ): void {
        if (!interface_exists(TwoFactorInterface::class)) {
            self::markTestSkipped('The Employee entity requires the locked Scheb dependencies.');
        }
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->expects(self::once())->method('get')->with('PS_BACKOFFICE_2FA')->willReturn($global);
        $employee = $this->createMock(Employee::class);
        $employee->method('isTwoFactorRequired')->willReturn($required);
        $employee->method('getTwoFactorEnabled')->willReturn($enabled);
        $employee->method('isEmailAuthEnabled')->willReturn($email);
        $employee->method('isTotpAuthenticationEnabled')->willReturn($totp);

        self::assertSame($expected, (new TwoFactorSetupRequirementChecker($configuration))->isRequired($employee));
    }

    public static function setupStates(): iterable
    {
        yield 'globally disabled with required flag retained' => [false, true, false, false, false, false];
        yield 'optional' => [true, false, false, false, false, false];
        yield 'required but disabled' => [true, true, false, false, false, true];
        yield 'required without a provider' => [true, true, true, false, false, true];
        yield 'email configured' => [true, true, true, true, false, false];
        yield 'TOTP configured' => [true, true, true, false, true, false];
    }
}
