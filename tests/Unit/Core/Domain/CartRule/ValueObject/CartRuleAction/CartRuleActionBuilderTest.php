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

namespace Tests\Unit\Core\Domain\CartRule\ValueObject\CartRuleAction;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\AmountDiscountAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionBuilder;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\FreeShippingAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\GiftProductAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\PercentageDiscountAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\GiftProduct;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\MoneyAmountCondition;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\PercentageDiscount;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Money;

/**
 * Tests if cart rule actions are built correctly.
 */
class CartRuleActionBuilderTest extends TestCase
{
    public function testItFailsWhenCartRuleHasIncompatibleActions()
    {
        $this->expectException(CartRuleConstraintException::class);
        $this->expectExceptionCode(CartRuleConstraintException::INCOMPATIBLE_CART_RULE_ACTIONS);

        (new CartRuleActionBuilder())
            ->setAmountDiscount(
                new MoneyAmountCondition(
                    new Money(new DecimalNumber('0'), new CurrencyId(1)),
                    true
                )
            )
            ->setPercentageDiscount(new PercentageDiscount(10, true))
            ->build();
    }

    public function testItFailsWhenCartRuleHasNoAction()
    {
        $this->expectException(CartRuleConstraintException::class);
        $this->expectExceptionCode(CartRuleConstraintException::MISSING_ACTION);

        (new CartRuleActionBuilder())->build();
    }

    public function testItCorrectlyBuildsAmountDiscountAction()
    {
        $action = (new CartRuleActionBuilder())
            ->setAmountDiscount(
                new MoneyAmountCondition(
                    new Money(new DecimalNumber('10'), new CurrencyId(1)),
                    true
                )
            )
            ->build();

        $this->assertInstanceOf(AmountDiscountAction::class, $action);
    }

    public function testItCorrectlyBuildsPercentageDiscountAction()
    {
        $action = (new CartRuleActionBuilder())
            ->setPercentageDiscount(new PercentageDiscount(10, true))
            ->build();

        $this->assertInstanceOf(PercentageDiscountAction::class, $action);
    }

    public function testItCorrectlyBuildsFreeShippingAction()
    {
        $action = (new CartRuleActionBuilder())
            ->setFreeShipping(true)
            ->build();

        $this->assertInstanceOf(FreeShippingAction::class, $action);
    }

    public function testItCorrectlyBuildsGiftProductAction()
    {
        $action = (new CartRuleActionBuilder())
            ->setGiftProduct(new GiftProduct(1))
            ->build();

        $this->assertInstanceOf(GiftProductAction::class, $action);
    }

    /**
     * @dataProvider validActionsProvider
     */
    public function testItCorrectlyBuildsVariousValidActions(
        ?MoneyAmountCondition $moneyAmount,
        ?PercentageDiscount $percentage,
        bool $isFreeShipping,
        ?GiftProduct $giftProduct,
        CartRuleActionInterface $expectedAction
    ) {
        $builder = (new CartRuleActionBuilder())
            ->setFreeShipping($isFreeShipping);

        if (null !== $moneyAmount) {
            $builder->setAmountDiscount($moneyAmount);
        }

        if (null !== $percentage) {
            $builder->setPercentageDiscount($percentage);
        }

        if (null !== $giftProduct) {
            $builder->setGiftProduct($giftProduct);
        }

        $this->assertEquals($expectedAction, $builder->build());
    }

    public function validActionsProvider()
    {
        $moneyAmount = new MoneyAmountCondition(
            new Money(new DecimalNumber('100'), new CurrencyId(1)),
            true
        );
        $percentage = new PercentageDiscount(30.5, true);
        $giftProduct = new GiftProduct(1);

        // [Amount, Percentage, Is free shipping, Expected result]
        yield [$moneyAmount, null, true, $giftProduct, new AmountDiscountAction($moneyAmount, true, $giftProduct)];
        yield [$moneyAmount, null, false, null, new AmountDiscountAction($moneyAmount, false)];
        yield [null, $percentage, false, $giftProduct, new PercentageDiscountAction($percentage, false, $giftProduct)];
        yield [null, $percentage, true, null, new PercentageDiscountAction($percentage, true)];
        yield [null, null, true, null, new FreeShippingAction()];
        yield [null, null, true, $giftProduct, new FreeShippingAction($giftProduct)];
        yield [null, null, false, $giftProduct, new GiftProductAction($giftProduct)];
    }
}
