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

namespace Tests\Unit\Adapter\CartRule;

use CartRule;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\CartRule\CartRuleActionFiller;
use PrestaShop\PrestaShop\Adapter\CartRule\LegacyDiscountApplicationType;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\Discount;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\DiscountApplicationType;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\GiftProduct;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Money;

class CartRuleActionFillerTest extends TestCase
{
    /**
     * @dataProvider getDataToTestUpdatablePropertiesFilling
     *
     * @param CartRuleAction $cartRuleAction
     * @param array $expectedUpdatableProperties
     * @param CartRule $expectedCartRule
     *
     * @return void
     */
    public function testFillsUpdatableProperties(
        CartRuleAction $cartRuleAction,
        array $expectedUpdatableProperties,
        CartRule $expectedCartRule
    ) {
        $cartRule = $this->mockDefaultCartRule();
        $updatableProperties = (new CartRuleActionFiller())->fillUpdatableProperties(
            $cartRule,
            $cartRuleAction
        );

        Assert::assertSame($expectedUpdatableProperties, $updatableProperties);
        Assert::assertEquals($expectedCartRule, $cartRule);
    }

    public function getDataToTestUpdatablePropertiesFilling(): iterable
    {
        $expectedCartRule = $this->mockDefaultCartRule();
        $expectedCartRule->free_shipping = true;
        $expectedCartRule->gift_product = null;
        $expectedCartRule->gift_product_attribute = null;
        yield [
            new CartRuleAction(true),
            $this->getExpectedUpdatableProperties(),
            $expectedCartRule,
        ];

        $expectedCartRule = $this->mockDefaultCartRule();
        $expectedCartRule->gift_product = 2;
        $expectedCartRule->gift_product_attribute = null;
        $expectedCartRule->free_shipping = true;
        yield [
            new CartRuleAction(
                true,
                new GiftProduct(2)
            ),
            $this->getExpectedUpdatableProperties(),
            $expectedCartRule,
        ];

        $expectedCartRule = $this->mockDefaultCartRule();
        $expectedCartRule->gift_product = 2;
        $expectedCartRule->gift_product_attribute = 3;
        $expectedCartRule->free_shipping = true;
        yield [
            new CartRuleAction(
                true,
                new GiftProduct(2, 3)
            ),
            $this->getExpectedUpdatableProperties(),
            $expectedCartRule,
        ];

        $expectedCartRule = $this->mockDefaultCartRule();
        $expectedCartRule->free_shipping = true;
        $expectedCartRule->gift_product = 1;
        $expectedCartRule->gift_product_attribute = null;
        yield [
            new CartRuleAction(
                true,
                new GiftProduct(1)
            ),
            $this->getExpectedUpdatableProperties(),
            $expectedCartRule,
        ];

        $expectedCartRule = $this->mockDefaultCartRule();
        $expectedCartRule->free_shipping = true;
        $expectedCartRule->gift_product = 1;
        $expectedCartRule->gift_product_attribute = 2;
        yield [
            new CartRuleAction(
                true,
                new GiftProduct(1, 2)
            ),
            $this->getExpectedUpdatableProperties(),
            $expectedCartRule,
        ];

        $expectedCartRule = $this->mockDefaultCartRule();
        $expectedCartRule->free_shipping = false;
        $expectedCartRule->gift_product = 1;
        $expectedCartRule->gift_product_attribute = null;
        yield [
            new CartRuleAction(
                false,
                new GiftProduct(1)
            ),
            $this->getExpectedUpdatableProperties(),
            $expectedCartRule,
        ];

        $expectedCartRule = $this->mockDefaultCartRule();
        $expectedCartRule->free_shipping = false;
        $expectedCartRule->gift_product = 1;
        $expectedCartRule->gift_product_attribute = 2;
        yield [
            new CartRuleAction(
                false,
                new GiftProduct(1, 2)
            ),
            $this->getExpectedUpdatableProperties(),
            $expectedCartRule,
        ];

        $expectedCartRule = $this->mockDefaultCartRule();
        $expectedCartRule->free_shipping = true;
        $expectedCartRule->gift_product = null;
        $expectedCartRule->gift_product_attribute = null;
        $expectedCartRule->reduction_amount = 10.0;
        $expectedCartRule->reduction_currency = 2;
        $expectedCartRule->reduction_tax = true;
        $expectedCartRule->reduction_percent = 0;
        $expectedCartRule->reduction_exclude_special = false;
        $expectedCartRule->reduction_product = LegacyDiscountApplicationType::ORDER_WITHOUT_SHIPPING;
        yield [
            new CartRuleAction(
                true,
                null,
                Discount::buildAmountDiscount(
                    new Money(new DecimalNumber('10'), new CurrencyId(2), true),
                    new DiscountApplicationType(DiscountApplicationType::ORDER_WITHOUT_SHIPPING)
                )
            ),
            $this->getExpectedUpdatableProperties(),
            $expectedCartRule,
        ];

        $expectedCartRule = $this->mockDefaultCartRule();
        $expectedCartRule->free_shipping = false;
        $expectedCartRule->gift_product = 1;
        $expectedCartRule->gift_product_attribute = 3;
        $expectedCartRule->reduction_amount = 10.0;
        $expectedCartRule->reduction_currency = 2;
        $expectedCartRule->reduction_tax = false;
        $expectedCartRule->reduction_percent = 0;
        $expectedCartRule->reduction_exclude_special = false;
        $expectedCartRule->reduction_product = 5;
        yield [
            new CartRuleAction(
                false,
                new GiftProduct(1, 3),
                Discount::buildAmountDiscount(
                    new Money(new DecimalNumber('10'), new CurrencyId(2), false),
                    new DiscountApplicationType(DiscountApplicationType::SPECIFIC_PRODUCT, 5)
                )
            ),
            $this->getExpectedUpdatableProperties(),
            $expectedCartRule,
        ];

        $expectedCartRule = $this->mockDefaultCartRule();
        $expectedCartRule->free_shipping = true;
        $expectedCartRule->gift_product = null;
        $expectedCartRule->gift_product_attribute = null;
        $expectedCartRule->reduction_percent = 15.0;
        $expectedCartRule->reduction_exclude_special = false;
        $expectedCartRule->reduction_amount = 0;
        $expectedCartRule->reduction_currency = 0;
        $expectedCartRule->reduction_tax = false;
        $expectedCartRule->reduction_product = LegacyDiscountApplicationType::ORDER_WITHOUT_SHIPPING;
        yield [
            new CartRuleAction(
                true,
                null,
                Discount::buildPercentageDiscount(
                    new DecimalNumber('15'),
                    true,
                    new DiscountApplicationType(DiscountApplicationType::ORDER_WITHOUT_SHIPPING)
                )
            ),
            $this->getExpectedUpdatableProperties(),
            $expectedCartRule,
        ];

        $expectedCartRule = $this->mockDefaultCartRule();
        $expectedCartRule->free_shipping = false;
        $expectedCartRule->gift_product = 1;
        $expectedCartRule->gift_product_attribute = 4;
        $expectedCartRule->reduction_percent = 15.0;
        $expectedCartRule->reduction_exclude_special = true;
        $expectedCartRule->reduction_amount = 0;
        $expectedCartRule->reduction_currency = 0;
        $expectedCartRule->reduction_tax = false;
        $expectedCartRule->reduction_product = LegacyDiscountApplicationType::CHEAPEST_PRODUCT;
        yield [
            new CartRuleAction(
                false,
                new GiftProduct(1, 4),
                Discount::buildPercentageDiscount(
                    new DecimalNumber('15'),
                    false,
                    new DiscountApplicationType(DiscountApplicationType::CHEAPEST_PRODUCT)
                )
            ),
            $this->getExpectedUpdatableProperties(),
            $expectedCartRule,
        ];

        $expectedCartRule = $this->mockDefaultCartRule();
        $expectedCartRule->free_shipping = false;
        $expectedCartRule->gift_product = 2;
        $expectedCartRule->gift_product_attribute = null;
        $expectedCartRule->reduction_percent = 15.0;
        $expectedCartRule->reduction_exclude_special = true;
        $expectedCartRule->reduction_amount = 0;
        $expectedCartRule->reduction_currency = 0;
        $expectedCartRule->reduction_tax = false;
        $expectedCartRule->reduction_product = LegacyDiscountApplicationType::SELECTED_PRODUCTS;
        yield [
            new CartRuleAction(
                false,
                new GiftProduct(2),
                Discount::buildPercentageDiscount(
                    new DecimalNumber('15'),
                    false,
                    new DiscountApplicationType(DiscountApplicationType::SELECTED_PRODUCTS)
                )
            ),
            $this->getExpectedUpdatableProperties(),
            $expectedCartRule,
        ];
    }

    /**
     * Filler should always return all the properties which are updated as part of an action
     * (once one action is set, another one must be reset, so it still needs to be updated)
     *
     * @return string[]
     */
    private function getExpectedUpdatableProperties(): array
    {
        return [
            'reduction_amount', 'reduction_percent', 'reduction_currency', 'reduction_tax', 'reduction_exclude_special',
            'reduction_product', 'free_shipping', 'gift_product', 'gift_product_attribute',
        ];
    }

    private function mockDefaultCartRule(): CartRule
    {
        return $this->createMock(CartRule::class);
    }
}
