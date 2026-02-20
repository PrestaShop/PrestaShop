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

        if ($value === null || $value === '' || null === $this->totpAuthenticator) {
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
}
