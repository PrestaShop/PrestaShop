<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\EmployeeTotpVerificationCode;
use PrestaShop\PrestaShop\Core\Employee\ContextEmployeeProviderInterface;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class EmployeeTotpVerificationCodeValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ?TotpAuthenticatorInterface $totpAuthenticator,
        private readonly ContextEmployeeProviderInterface $contextEmployeeProvider,
        private readonly EmployeeRepository $employeeRepository,
    ) {
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof EmployeeTotpVerificationCode) {
            throw new UnexpectedTypeException($constraint, EmployeeTotpVerificationCode::class);
        }

        // Only validate the verification code when the current form submit enables TOTP.
        if (!$this->isTotpActivationRequested()) {
            return;
        }

        if ($value === null || $value === '') {
            $this->context->buildViolation('This field cannot be empty.')
                ->addViolation();

            return;
        }

        if (null === $this->totpAuthenticator) {
            return;
        }

        /** @var Employee $employee */
        $employee = $this->employeeRepository->findOneBy([
            'id' => $this->contextEmployeeProvider->getId(),
        ]);
        $isValid = $this->totpAuthenticator->checkCode($employee, $value);

        if (!$isValid) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }

    private function isTotpActivationRequested(): bool
    {
        // The constraint is attached to the verification code field, so we inspect the
        // root form to know whether the TOTP option is being enabled in this submit.
        $root = $this->context->getRoot();
        if (!$root instanceof FormInterface || !$root->has('two_factor_totp_enabled')) {
            return false;
        }

        return (bool) $root->get('two_factor_totp_enabled')->getData();
    }
}
