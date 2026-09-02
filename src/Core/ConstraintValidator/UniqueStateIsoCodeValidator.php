<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Adapter\State\CountryStateByIsoCodeProvider;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\UniqueStateIsoCode;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validator for checking if state with ISO Code doesn't exist in current shop context
 */
class UniqueStateIsoCodeValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!($constraint instanceof UniqueStateIsoCode)) {
            return;
        }
        $countryStateByIsoCodeProvider = new CountryStateByIsoCodeProvider();
        $stateId = $countryStateByIsoCodeProvider->getStateIdByIsoCode($value, (int) $constraint->countryId);

        if ($stateId && ($constraint->excludeStateId === null || $stateId !== $constraint->excludeStateId)) {
            $this->context
                ->buildViolation($constraint->message)
                ->setTranslationDomain('Admin.International.Notification')
                ->addViolation();
        }
    }
}
