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

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\AmountDiscountAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionBuilder;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\FreeShippingAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\GiftProductAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\PercentageDiscountAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\GiftProduct;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

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
                '0',
                1,
                true
            )
            ->setPercentageDiscount('10', true)
            ->build();
    }

    public function testItFailsWhenCartRuleHasNoAction()
    {
        $this->expectException(CartRuleConstraintException::class);
        //@todo: whole class as well as the test should be deleted.
//        $this->expectExceptionCode(CartRuleConstraintException::MISSING_ACTION);

        (new CartRuleActionBuilder())->build();
    }

    /**
     * @dataProvider getDataToBuildAmountDiscountAction
     *
     * @return void
     */
    public function testItBuildsAmountDiscountAction(
        string $reductionValue,
        int $currencyId,
        bool $taxIncluded,
        bool $freeShipping,
        ?int $giftProductId = null,
        ?int $giftCombinationId = null,
        ?string $expectedException = null,
        ?int $expectedExceptionCode = null
    ): void {
        if ($expectedException) {
            $this->expectException($expectedException);
            if ($expectedExceptionCode) {
                $this->expectExceptionCode($expectedExceptionCode);
            }
        }

        $builder = new CartRuleActionBuilder();
        $builder
            ->setAmountDiscount($reductionValue, $currencyId, $taxIncluded)
            ->setFreeShipping($freeShipping)
        ;

        if (null !== $giftProductId) {
            $builder->setGiftProduct($giftProductId, $giftCombinationId);
        }

        $action = $builder->build();
        $this->assertInstanceOf(AmountDiscountAction::class, $action);
        $amountDiscount = $action->getAmountDiscount();

        Assert::assertTrue($amountDiscount->getAmount()->equals(new DecimalNumber($reductionValue)));
        Assert::assertSame($amountDiscount->getCurrencyId()->getValue(), $currencyId);
        Assert::assertSame($amountDiscount->isTaxIncluded(), $taxIncluded);
        Assert::assertSame($action->isFreeShipping(), $freeShipping);
        if (null !== $giftProductId) {
            Assert::assertEquals($action->getGiftProduct(), new GiftProduct($giftProductId, $giftCombinationId));
        } else {
            Assert::assertNull($action->getGiftProduct());
        }
    }

    public function getDataToBuildAmountDiscountAction(): iterable
    {
        yield ['10', 1, true, true];
        yield ['10', 1, false, true];
        yield ['11', 1, true, false];
        yield ['11', 1, true, false, 1];
        yield ['11', 1, true, false, 1, 2];
        yield ['11', 1, true, false, 0, 2, ProductConstraintException::class, ProductConstraintException::INVALID_ID];
        yield ['11', 1, true, false, -1, 2, ProductConstraintException::class, ProductConstraintException::INVALID_ID];
        yield ['11', 1, true, false, 1, -1, CombinationConstraintException::class, CombinationConstraintException::INVALID_ID];
        yield ['15', 0, false, false, null, null, CurrencyConstraintException::class, CurrencyConstraintException::INVALID_ID];
        yield ['-50', 0, false, false, null, null, DomainConstraintException::class, DomainConstraintException::INVALID_REDUCTION_AMOUNT];
    }

    /**
     * @dataProvider getDataToBuildPercentageDiscount
     *
     * @return void
     */
    public function testItBuildsPercentageDiscountAction(
        string $reductionValue,
        bool $freeShipping,
        bool $excludeDiscountedProducts,
        ?int $giftProductId = null,
        ?int $giftCombinationId = null,
        ?string $expectedException = null,
        ?int $expectedExceptionCode = null
    ): void {
        if ($expectedException) {
            $this->expectException($expectedException);
            if ($expectedExceptionCode) {
                $this->expectExceptionCode($expectedExceptionCode);
            }
        }

        $builder = (new CartRuleActionBuilder())
            ->setPercentageDiscount($reductionValue, $excludeDiscountedProducts)
            ->setFreeShipping($freeShipping)
        ;

        if (null !== $giftProductId) {
            $builder->setGiftProduct($giftProductId, $giftCombinationId);
        }

        $action = $builder->build();
        $this->assertInstanceOf(PercentageDiscountAction::class, $action);
        $percentageDiscount = $action->getPercentageDiscount();

        Assert::assertTrue($percentageDiscount->getPercentage()->equals(new DecimalNumber($reductionValue)));
        Assert::assertSame($action->isFreeShipping(), $freeShipping);
        if (null !== $giftProductId) {
            Assert::assertEquals($action->getGiftProduct(), new GiftProduct($giftProductId, $giftCombinationId));
        } else {
            Assert::assertNull($action->getGiftProduct());
        }
    }

    public function getDataToBuildPercentageDiscount(): iterable
    {
        yield ['99', true, true];
        yield ['55.5', true, false, 1];
        yield ['55.5', true, true, 1, 2];
        yield ['55.5', true, false, 0, 2, ProductConstraintException::class, ProductConstraintException::INVALID_ID];
        yield ['55.5', true, false, -1, 2, ProductConstraintException::class, ProductConstraintException::INVALID_ID];
        yield ['55.5', true, false, 1, -1, CombinationConstraintException::class, CombinationConstraintException::INVALID_ID];
        yield ['101', true, false, 1, 2, DomainConstraintException::class, DomainConstraintException::INVALID_REDUCTION_PERCENTAGE];
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
            ->setGiftProduct(1)
            ->build();

        $this->assertInstanceOf(GiftProductAction::class, $action);

        $action = (new CartRuleActionBuilder())
            ->setGiftProduct(1, 2)
            ->build();

        $this->assertInstanceOf(GiftProductAction::class, $action);
    }
}
