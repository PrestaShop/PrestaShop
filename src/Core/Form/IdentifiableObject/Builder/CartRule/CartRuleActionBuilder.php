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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\CartRule;

use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\AmountDiscountAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\FreeShippingAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\GiftProductAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\PercentageDiscountAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\DiscountApplicationType;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\GiftProduct;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Money;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use Symfony\Component\PropertyAccess\PropertyAccess;

class CartRuleActionBuilder
{
    /**
     * @param array<string, mixed> $actionsData
     *
     * @return CartRuleActionInterface
     */
    public function build(array $actionsData): CartRuleActionInterface
    {
        $giftProduct = null;
        if (!empty($actionsData['gift_product'][0])) {
            $giftProductData = $actionsData['gift_product'][0];
            $giftProduct = new GiftProduct(
                $giftProductData['product_id'],
                isset($giftProductData['combination_id']) ? (int) $giftProductData['combination_id'] : null
            );
        }

        $freeShipping = false;
        if (!empty($actionsData['free_shipping'])) {
            $freeShipping = true;
        }

        if (!empty($actionsData['discount']['reduction']['value'])) {
            if (!empty($actionsData['discount']['specific_product'][0]['id'])) {
                $specificProductId = (int) $actionsData['discount']['specific_product'][0]['id'];
            } else {
                $specificProductId = null;
            }

            $discountApplicationType = new DiscountApplicationType(
                $actionsData['discount']['discount_application'],
                $specificProductId
            );
            $reductionData = $actionsData['discount']['reduction'];
            // creating this VO mostly just to fire the validation inside its constructor,
            // and we don't need to create DecimalNumbers manually when using in Discount objects
            $reduction = new Reduction($reductionData['type'], (string) $reductionData['value']);

            if ($reduction->getType() === Reduction::TYPE_AMOUNT) {
                return new AmountDiscountAction(
                    new Money(
                        $reduction->getValue(),
                        new CurrencyId($actionsData['currency']),
                        (bool) $reductionData['include_tax']
                    ),
                    $freeShipping,
                    $discountApplicationType,
                    $giftProduct
                );
            } else {
                return new PercentageDiscountAction(
                    $reduction->getValue(),
                    (bool) $actionsData['discount']['apply_to_discounted_products'],
                    $freeShipping,
                    $discountApplicationType,
                    $giftProduct
                );
            }
        } elseif ($freeShipping) {
            return new FreeShippingAction($giftProduct);
        } elseif ($giftProduct) {
            return new GiftProductAction($giftProduct);
        }

        throw new CartRuleConstraintException('Cart rule must have at least one action', CartRuleConstraintException::MISSING_ACTION);
    }

    /**
     * Returns true when there are required keys to build action.
     * It can be used for example in partial update action, when action data is not being updated.
     *
     * @param array<string, mixed> $data
     *
     * @return bool
     */
    public function supports(array $data): bool
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor()
        ;

        if (array_key_exists('free_shipping', $data)) {
            return true;
        }

        if ($propertyAccessor->isReadable($data, '[gift_product][0]')) {
            return true;
        }

        if (
            $propertyAccessor->isReadable($data, '[discount][reduction][value]')
            && $propertyAccessor->isReadable($data, '[discount][reduction][type]')
            && $propertyAccessor->isReadable($data, '[discount][discount_application]')
        ) {
            return true;
        }

        return false;
    }
}
