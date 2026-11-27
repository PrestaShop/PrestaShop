<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ConstraintValidator;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\EmployeeTotpVerificationCode;
use PrestaShop\PrestaShop\Core\ConstraintValidator\EmployeeTotpVerificationCodeValidator;
use PrestaShop\PrestaShop\Core\Employee\ContextEmployeeProviderInterface;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class EmployeeTotpVerificationCodeValidatorTest extends TestCase
{
    #[DataProvider('secretStates')]
    public function testActivationRequiresAnExistingSecretAndServerSideProof(bool $exists, bool $hasSecret, bool $valid): void
    {
        $employee = $exists ? new Employee() : null;
        if ($employee && $hasSecret) {
            $employee->setTwoFactorTotpSecretPlain('JBSWY3DPEHPK3PXP');
        }
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->method('findOneBy')->with(['id' => 42])->willReturn($employee);
        $actor = $this->createMock(ContextEmployeeProviderInterface::class);
        $actor->method('getId')->willReturn(42);
        $totp = $this->createMock(TotpAuthenticatorInterface::class);
        $totp->expects($hasSecret ? self::once() : self::never())->method('checkCode')->willReturn($valid);
        $switch = $this->createMock(FormInterface::class);
        $switch->method('getData')->willReturn(true);
        $form = $this->createMock(FormInterface::class);
        $form->method('has')->with('two_factor_totp_enabled')->willReturn(true);
        $form->method('get')->with('two_factor_totp_enabled')->willReturn($switch);
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->method('getRoot')->willReturn($form);
        $constraint = new EmployeeTotpVerificationCode();
        $violation = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violation->method('setParameter')->willReturnSelf();
        $violation->expects($valid ? self::never() : self::once())->method('addViolation');
        $context->expects($valid ? self::never() : self::once())->method('buildViolation')->with($constraint->message)->willReturn($violation);
        $validator = new EmployeeTotpVerificationCodeValidator($totp, $actor, $repository);
        $validator->initialize($context);
        $validator->validate('123456', $constraint);
    }

    public static function secretStates(): iterable
    {
        yield 'missing employee' => [false, false, false];
        yield 'secret not prepared' => [true, false, false];
        yield 'invalid verification code' => [true, true, false];
        yield 'valid verification code' => [true, true, true];
    }
}
