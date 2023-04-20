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

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CartRule\Discount;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\DiscountApplicationType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class DiscountValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Discount) {
            throw new UnexpectedTypeException($constraint, Discount::class);
        }

        if (!is_array($value)) {
            throw new UnexpectedValueException($value, 'array');
        }

        if (
            DiscountApplicationType::SPECIFIC_PRODUCT === $value['discount_application']
            && empty($value['specific_product'])
        ) {
            $this->buildViolation($constraint->missingSpecificProductMessage, '[specific_product]');
        }

        if (
            DiscountApplicationType::SELECTED_PRODUCTS === $value['discount_application']
            //@todo: restrictions are not implemented, so this will still adapt,
            //       but the point is to check if any products restrictions are applied
            //       also need to check more in depth if its legit with products only or also with categories/attributes etc.)
            && empty($this->context->getRoot()['conditions']['product_restrictions'])
        ) {
            $this->buildViolation($constraint->missingProductRestrictionsMessage, '[discount_application]');
        }
    }

    private function buildViolation(string $message, string $errorPath): void
    {
        $this->context
            ->buildViolation($message)
            ->setTranslationDomain('Admin.Notifications.Error')
            ->atPath($errorPath)
            ->addViolation()
        ;
    }
}
