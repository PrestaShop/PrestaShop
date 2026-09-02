<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\NotBlankWhenRequired;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlankValidator;

class NotBlankWhenRequiredValidator extends NotBlankValidator
{
    public function validate($value, Constraint $constraint)
    {
        if ($constraint instanceof NotBlankWhenRequired && true === $constraint->required) {
            parent::validate($value, $constraint);
        }
    }
}
