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

namespace PrestaShop\PrestaShop\Adapter\Discount\Validate;

use CartRule;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;

/**
 * This validator is used for the new Discount domain, but it still relies on the legacy CartRule ObjectModel.
 */
class DiscountValidator extends AbstractObjectModelValidator
{
    public function validate(CartRule $cartRule): void
    {
        $this->validateCartRuleProperty($cartRule, 'id_customer', DiscountConstraintException::INVALID_CUSTOMER_ID);
        $this->validateCartRuleProperty($cartRule, 'date_from', DiscountConstraintException::INVALID_DATE_FROM);
        $this->validateCartRuleProperty($cartRule, 'date_to', DiscountConstraintException::INVALID_DATE_TO);
        $this->validateCartRuleProperty($cartRule, 'description', DiscountConstraintException::INVALID_DESCRIPTION);
        $this->validateCartRuleProperty($cartRule, 'quantity', DiscountConstraintException::INVALID_QUANTITY);
        $this->validateCartRuleProperty($cartRule, 'quantity_per_user', DiscountConstraintException::INVALID_QUANTITY_PER_USER);
        $this->validateCartRuleProperty($cartRule, 'priority', DiscountConstraintException::INVALID_PRIORITY);
        $this->validateCartRuleProperty($cartRule, 'partial_use', DiscountConstraintException::INVALID_PARTIAL_USE);
        $this->validateCartRuleProperty($cartRule, 'code', DiscountConstraintException::INVALID_CODE);
        $this->validateCartRuleProperty($cartRule, 'minimum_amount', DiscountConstraintException::INVALID_MINIMUM_AMOUNT);
        $this->validateCartRuleProperty($cartRule, 'minimum_amount_tax', DiscountConstraintException::INVALID_MINIMUM_AMOUNT_TAX);
        $this->validateCartRuleProperty($cartRule, 'minimum_amount_currency', DiscountConstraintException::INVALID_MINIMUM_AMOUNT_CURRENCY);
        $this->validateCartRuleProperty($cartRule, 'minimum_amount_shipping', DiscountConstraintException::INVALID_MINIMUM_AMOUNT_SHIPPING);
        $this->validateCartRuleProperty($cartRule, 'country_restriction', DiscountConstraintException::INVALID_COUNTRY_RESTRICTION);
        $this->validateCartRuleProperty($cartRule, 'carrier_restriction', DiscountConstraintException::INVALID_CARRIER_RESTRICTION);
        $this->validateCartRuleProperty($cartRule, 'group_restriction', DiscountConstraintException::INVALID_GROUP_RESTRICTION);
        $this->validateCartRuleProperty($cartRule, 'cart_rule_restriction', DiscountConstraintException::INVALID_CART_RULE_RESTRICTION);
        $this->validateCartRuleProperty($cartRule, 'product_restriction', DiscountConstraintException::INVALID_PRODUCT_RESTRICTION);
        $this->validateCartRuleProperty($cartRule, 'shop_restriction', DiscountConstraintException::INVALID_SHOP_RESTRICTION);
        $this->validateCartRuleProperty($cartRule, 'free_shipping', DiscountConstraintException::INVALID_FREE_SHIPPING);
        $this->validateCartRuleProperty($cartRule, 'reduction_percent', DiscountConstraintException::INVALID_REDUCTION_PERCENT);
        $this->validateCartRuleProperty($cartRule, 'reduction_amount', DiscountConstraintException::INVALID_REDUCTION_AMOUNT);
        $this->validateCartRuleProperty($cartRule, 'reduction_tax', DiscountConstraintException::INVALID_REDUCTION_TAX);
        $this->validateCartRuleProperty($cartRule, 'reduction_currency', DiscountConstraintException::INVALID_REDUCTION_CURRENCY);
        $this->validateCartRuleProperty($cartRule, 'reduction_product', DiscountConstraintException::INVALID_REDUCTION_PRODUCT);
        $this->validateCartRuleProperty($cartRule, 'reduction_exclude_special', DiscountConstraintException::INVALID_REDUCTION_EXCLUDE_SPECIAL);
        $this->validateCartRuleProperty($cartRule, 'gift_product', DiscountConstraintException::INVALID_GIFT_PRODUCT);
        $this->validateCartRuleProperty($cartRule, 'gift_product_attribute', DiscountConstraintException::INVALID_GIFT_PRODUCT_ATTRIBUTE);
        $this->validateCartRuleProperty($cartRule, 'highlight', DiscountConstraintException::INVALID_HIGHLIGHT);
        $this->validateCartRuleProperty($cartRule, 'active', DiscountConstraintException::INVALID_ACTIVE);

        $this->validateObjectModelLocalizedProperty(
            $cartRule,
            'name',
            DiscountConstraintException::class,
            DiscountConstraintException::INVALID_NAME
        );

        $this->assertCodeIsUnique($cartRule);
    }

    /**
     * @throws DiscountConstraintException
     */
    public function validateDiscountPropertiesForType(AddDiscountCommand $command)
    {
        switch ($command->getDiscountType()->getValue()) {
            case DiscountType::FREE_SHIPPING:
                break;
            case DiscountType::CART_LEVEL:
            case DiscountType::ORDER_LEVEL:
                if ($command->getAmountDiscount() !== null && $command->getPercentDiscount() !== null) {
                    throw new DiscountConstraintException('Discount can not be amount and percent at the same time', DiscountConstraintException::INVALID_DISCOUNT_CANNOT_BE_AMOUNT_AND_PERCENT);
                }
                if ($command->getAmountDiscount() !== null) {
                    if ($command->getAmountDiscount()->getAmount()->isLowerThanZero()) {
                        throw new DiscountConstraintException('Discount value can not be negative', DiscountConstraintException::INVALID_DISCOUNT_VALUE_CANNOT_BE_NEGATIVE);
                    }
                }
                if ($command->getPercentDiscount() !== null) {
                    if ($command->getPercentDiscount()->isLowerThanZero() || $command->getPercentDiscount()->isGreaterThan(new DecimalNumber('100'))) {
                        throw new DiscountConstraintException('Discount value can not be negative or above 100', DiscountConstraintException::INVALID_DISCOUNT_VALUE_CANNOT_BE_NEGATIVE);
                    }
                }
                break;
            case DiscountType::PRODUCT_LEVEL:
                if ($command->getReductionProduct() === 0 || $command->getPercentDiscount() === null) {
                    throw new DiscountConstraintException('Product discount must have his properties set.', DiscountConstraintException::INVALID_PRODUCT_DISCOUNT_PROPERTIES);
                }
                break;
            case DiscountType::FREE_GIFT:
                if ($command->getProductId() === null) {
                    throw new DiscountConstraintException('Free gift discount must have his properties set.', DiscountConstraintException::INVALID_FREE_GIFT_DISCOUNT_PROPERTIES);
                }
                break;
            default:
                throw new DiscountConstraintException(sprintf("Invalid discount type '%s'.", $command->getDiscountType()->getValue()), DiscountConstraintException::INVALID_DISCOUNT_TYPE);
        }
    }

    private function validateCartRuleProperty(CartRule $cartRule, string $propertyName, int $code): void
    {
        $this->validateObjectModelProperty(
            $cartRule,
            $propertyName,
            DiscountConstraintException::class,
            $code
        );
    }

    private function assertCodeIsUnique(CartRule $cartRule): void
    {
        $code = $cartRule->code;

        if (empty($code)) {
            return;
        }

        try {
            $duplicateCodeCartRuleId = (int) CartRule::getIdByCode($code);
        } catch (PrestaShopException $e) {
            throw new CoreException('Error occurred when trying to check if cart rule code is unique', 0, $e);
        }

        if ($duplicateCodeCartRuleId && $duplicateCodeCartRuleId !== (int) $cartRule->id) {
            throw new DiscountConstraintException(
                sprintf('Cart rule with code "%s" already exists', $code),
                DiscountConstraintException::NON_UNIQUE_CODE
            );
        }
    }
}
