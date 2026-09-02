<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates @see PositiveOrZero constraint
 */
class PositiveOrZeroValidator extends ConstraintValidator
{
    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof PositiveOrZero) {
            throw new UnexpectedTypeException($constraint, PositiveOrZero::class);
        }

        if (!is_numeric($value) || !(new DecimalNumber((string) $value))->isGreaterOrEqualThanZero()) {
            $this->context->buildViolation($constraint->message)
                ->setTranslationDomain('Admin.Notifications.Error')
                ->addViolation()
            ;
        }
    }
}
