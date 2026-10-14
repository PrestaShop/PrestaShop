<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters\EmployeeController;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\SchebTwoFactor\EmployeeBackupCodeManager;
use ReflectionMethod;
use Scheb\TwoFactorBundle\Model\PersisterInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class EmployeeBackupCodeLifecycleTest extends TestCase
{
    /**
     * @return iterable<string, array{bool, bool, bool, bool, bool, bool, string}>
     */
    public static function lifecycleCases(): iterable
    {
        yield 'activate email' => [false, false, true, false, true, true, 'generate'];
        yield 'activate TOTP' => [false, false, true, true, false, true, 'generate'];
        yield 'activate both' => [false, false, true, true, true, true, 'generate'];
        yield 'reactivate with stale codes' => [false, true, true, false, true, true, 'generate'];
        yield 'add TOTP to email' => [true, true, true, true, true, true, 'keep'];
        yield 'switch TOTP to email' => [true, true, true, false, true, true, 'keep'];
        yield 'normal save with exhausted codes' => [true, false, true, true, false, true, 'keep'];
        yield 'disable master switch' => [true, true, false, true, true, true, 'clear'];
        yield 'disable all methods' => [true, true, true, false, false, true, 'clear'];
        yield 'unrelated account edit' => [true, true, false, false, false, false, 'keep'];
    }

    #[DataProvider('lifecycleCases')]
    public function testBackupCodesFollowEffectiveTwoFactorState(
        bool $wasEnabled,
        bool $hadCodes,
        bool $enabled,
        bool $totpEnabled,
        bool $emailEnabled,
        bool $restrictedAccess,
        string $expectedAction,
    ): void {
        $employee = new Employee();
        $employee->setTwoFactorBackupCodes($hadCodes ? ['old-hash'] : null);

        $fields = [];
        foreach ([
            'two_factor_enabled' => $enabled,
            'two_factor_totp_enabled' => $totpEnabled,
            'two_factor_email_enabled' => $emailEnabled,
        ] as $name => $value) {
            $field = $this->createMock(FormInterface::class);
            $field->method('getData')->willReturn($value);
            $fields[$name] = $field;
        }

        $form = $this->createMock(FormInterface::class);
        $form->method('has')->willReturn(true);
        $form->method('get')->willReturnCallback(static fn (string $name): FormInterface => $fields[$name]);

        $hasher = $this->createMock(PasswordHasherInterface::class);
        $hasher->method('hash')->willReturnCallback(static fn (string $code): string => 'hash-' . $code);
        $manager = new EmployeeBackupCodeManager($hasher, $this->createMock(PersisterInterface::class));

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($expectedAction === 'generate' ? self::once() : self::never())->method('persist')->with($employee);
        $entityManager->expects(in_array($expectedAction, ['generate', 'clear'], true) ? self::once() : self::never())->method('flush');

        $controller = new class() extends EmployeeController {
            public array $flashes = [];

            protected function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
            {
                return $id;
            }

            protected function addFlash(string $type, mixed $message): void
            {
                $this->flashes[$type][] = $message;
            }
        };

        $method = new ReflectionMethod(EmployeeController::class, 'updateBackupCodesAfterTwoFactorChange');
        $method->invoke($controller, $form, $employee, $restrictedAccess, $wasEnabled, $manager, $entityManager);

        if ($expectedAction === 'generate') {
            self::assertCount(8, $employee->getTwoFactorBackupCodes());
            self::assertCount(8, $controller->flashes['backup_codes']);
            self::assertNotContains('old-hash', $employee->getTwoFactorBackupCodes());
        } elseif ($expectedAction === 'clear') {
            self::assertNull($employee->getTwoFactorBackupCodes());
            self::assertSame([], $controller->flashes);
        } else {
            self::assertSame($hadCodes ? ['old-hash'] : null, $employee->getTwoFactorBackupCodes());
            self::assertSame([], $controller->flashes);
        }
    }
}
