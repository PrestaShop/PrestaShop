<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use DateTime;
use Exception;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DateRange;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates ranges of date range
 */
class DateRangeValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof DateRange) {
            throw new UnexpectedTypeException($constraint, DateRange::class);
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        if (!empty($value['from']) && !empty($value['to'])) {
            $this->validateRange(new DateTime($value['from']), new DateTime($value['to']), $constraint->message);
        }
    }

    /**
     * Validate that date range is not inverted. (the 'from' value is not higher than 'to')
     *
     * @param DateTime $from
     * @param DateTime $to
     * @param string $message
     */
    private function validateRange(DateTime $from, DateTime $to, $message)
    {
        if ($from->diff($to)->invert) {
            $this->context->buildViolation($message)
                ->atPath('[to]')
                ->setTranslationDomain('Admin.Notifications.Error')
                ->addViolation()
            ;
        }
    }
}
