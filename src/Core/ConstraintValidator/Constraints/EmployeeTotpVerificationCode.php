<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use PrestaShop\PrestaShop\Core\ConstraintValidator\EmployeeTotpVerificationCodeValidator;
use Symfony\Component\Validator\Constraint;

class EmployeeTotpVerificationCode extends Constraint
{
    public $message = 'This code is not valid.';

    public function getDefaultOption()
    {
        return null;
    }

    public function getRequiredOptions()
    {
        return [];
    }

    public function validatedBy()
    {
        return EmployeeTotpVerificationCodeValidator::class;
    }
}
