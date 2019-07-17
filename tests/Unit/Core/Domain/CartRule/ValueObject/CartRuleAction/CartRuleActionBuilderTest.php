<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace Tests\Unit\Core\Domain\CartRule\ValueObject\CartRuleAction;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\AmountDiscountAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionBuilder;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\FreeShippingAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\GiftProductAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\PercentageDiscountAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\GiftProduct;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\PercentageDiscount;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\MoneyAmount;

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
            ->setAmountDiscount(new MoneyAmount(0, 1))
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
            ->setAmountDiscount(new MoneyAmount(10, 1))
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
            ->setGiftProduct(new GiftProduct(new ProductId(1)))
            ->build();

        $this->assertInstanceOf(GiftProductAction::class, $action);
    }
}
