<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
     * @param $message
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
