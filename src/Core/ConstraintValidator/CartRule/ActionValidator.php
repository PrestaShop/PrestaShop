<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\CartRule;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CartRule\Action;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Validates that cart rule contains at least one action
 */
class ActionValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Action) {
            throw new UnexpectedTypeException($constraint, Action::class);
        }

        if (!is_array($value)) {
            throw new UnexpectedValueException($value, 'array');
        }

        if (!empty($value['free_shipping'])) {
            return;
        }

        // in theory there are more required properties, but we already assume they are present when reduction value is present
        // (the disabling switch and reduction value can be "broken" by user, therefor other values missing would mean developer error instead of constraint violation)
        if (!empty($value['disabling_switch_discount']) && !empty($value['discount']['reduction']['value'])) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->setTranslationDomain('Admin.Notifications.Error')
            ->addViolation()
        ;
    }
}
