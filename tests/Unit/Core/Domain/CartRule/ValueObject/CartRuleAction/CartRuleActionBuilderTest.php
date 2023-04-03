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
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\AmountDiscountAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionBuilder;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\FreeShippingAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\GiftProductAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\PercentageDiscountAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\GiftProduct;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\PercentageDiscount;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Money;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;

/**
 * Tests if cart rule actions are built correctly.
 */
class CartRuleActionBuilderTest extends TestCase
{
    /** @var CartRuleActionBuilder */
    protected $cartRuleActionBuilder;

    public function setUp(): void
    {
        parent::setUp();
        $this->cartRuleActionBuilder = new CartRuleActionBuilder();
    }

    public function testItFailsWhenCartRuleIsMissingReductionTypeOrValue(): void
    {
        $this->expectException(DomainConstraintException::class);

        $this->expectExceptionCode(DomainConstraintException::INVALID_REDUCTION_TYPE);
        $this->cartRuleActionBuilder->build(true, null, '5');

        $this->expectExceptionCode(DomainConstraintException::INVALID_REDUCTION_PERCENTAGE);
        $this->cartRuleActionBuilder->build(true, Reduction::TYPE_PERCENTAGE);

        $this->expectExceptionCode(DomainConstraintException::INVALID_REDUCTION_AMOUNT);
        $this->cartRuleActionBuilder->build(true, Reduction::TYPE_AMOUNT);
    }

    /**
     * @dataProvider getDataForReductionConstraintsViolation
     *
     * @return void
     *
     * @throws CartRuleConstraintException
     */
    public function testItFailsWhenReductionConstraintsAreViolated(
        ?string $reductionType,
        ?string $reductionValue,
        int $expectedExceptionCode
    ): void {
        $this->expectException(DomainConstraintException::class);
        $this->expectExceptionCode($expectedExceptionCode);
        $this->cartRuleActionBuilder->build(true, $reductionType, $reductionValue);
    }

    public function getDataForReductionConstraintsViolation(): iterable
    {
        yield ['random', '5', DomainConstraintException::INVALID_REDUCTION_TYPE];
        yield [Reduction::TYPE_PERCENTAGE, '120', DomainConstraintException::INVALID_REDUCTION_PERCENTAGE];
        yield [Reduction::TYPE_AMOUNT, '-20', DomainConstraintException::INVALID_REDUCTION_AMOUNT];
    }

    public function testItSucceedsWhenCartRuleHasNoReductionTypeAndNoReductionValue(): void
    {
        $action = $this->cartRuleActionBuilder->build(true);

        Assert::assertTrue($action->isFreeShipping());
    }

    public function testItFailsWhenCartRuleHasNoAction()
    {
        $this->expectException(CartRuleConstraintException::class);
        $this->expectExceptionCode(CartRuleConstraintException::MISSING_ACTION);

        $this->cartRuleActionBuilder->build(false);
    }

    public function testItFailsWhenReductionTypeIsAmountAndItIsMissingCurrency(): void
    {
        $this->expectException(CartRuleConstraintException::class);

        $this->expectExceptionCode(CartRuleConstraintException::INVALID_REDUCTION_CURRENCY);
        $this->cartRuleActionBuilder->build(true, Reduction::TYPE_AMOUNT, '10', null, true);
    }

    public function testItFailsWhenReductionTypeIsAmountAndItIsMissingTaxInclusion(): void
    {
        $this->expectException(CartRuleConstraintException::class);
        $this->expectExceptionCode(CartRuleConstraintException::INVALID_REDUCTION_TAX);
        $this->cartRuleActionBuilder->build(true, Reduction::TYPE_AMOUNT, '10', 2);
    }

    public function testItCorrectlyBuildsAmountDiscountAction()
    {
        $action = $this->cartRuleActionBuilder->build(true, Reduction::TYPE_AMOUNT, '10', 1, true);
        $this->assertInstanceOf(AmountDiscountAction::class, $action);
    }

    public function testItCorrectlyBuildsPercentageDiscountAction()
    {
        $this->assertInstanceOf(
            PercentageDiscountAction::class,
            $this->cartRuleActionBuilder->build(
                false,
                Reduction::TYPE_PERCENTAGE,
                '10',
                null,
                null,
                null,
                null,
                true
            )
        );
    }

    public function testItFailsToBuildPercentageDiscountWhenApplyToDiscountedProductsIsMissing(): void
    {
        $this->expectException(CartRuleConstraintException::class);
        $this->expectExceptionCode(CartRuleConstraintException::INVALID_REDUCTION_EXCLUDE_SPECIAL);

        $this->cartRuleActionBuilder->build(false, Reduction::TYPE_PERCENTAGE, '10');
    }

    public function testItCorrectlyBuildsFreeShippingAction()
    {
        $this->assertInstanceOf(FreeShippingAction::class, $this->cartRuleActionBuilder->build(true));
    }

    public function testItCorrectlyBuildsGiftProductAction()
    {
        $action = $this->cartRuleActionBuilder->build(
            false,
            null,
            null,
            null,
            null,
            5,
            3
        );

        $this->assertInstanceOf(GiftProductAction::class, $action);
        Assert::assertSame(5, $action->getGiftProduct()->getProductId()->getValue());
        Assert::assertSame(3, $action->getGiftProduct()->getCombinationId()->getValue());
    }

    /**
     * @dataProvider getValidActions
     */
    public function testItCorrectlyBuildsVariousValidActions(
        bool $freeShipping,
        ?string $reductionType,
        ?string $reductionValue,
        ?int $currencyId,
        ?bool $taxIncluded,
        ?int $giftProductId,
        ?int $giftCombinationId,
        ?bool $appliesToDiscountedProducts,
        CartRuleActionInterface $expectedAction
    ) {
        Assert::assertEquals($expectedAction, $this->cartRuleActionBuilder->build(
            $freeShipping,
            $reductionType,
            $reductionValue,
            $currencyId,
            $taxIncluded,
            $giftProductId,
            $giftCombinationId,
            $appliesToDiscountedProducts
        ));
    }

    public function getValidActions(): iterable
    {
        // [freeShipping, reductionTye, reductionValue, currency, taxIncluded, giftProductId, giftCombinationId, appliesToDiscountedProducts, expected result]
        yield [true, null, null, null, null, null, null, null, new FreeShippingAction()];
        yield [true, null, null, null, null, 5, null, null, new FreeShippingAction(new GiftProduct(5))];
        yield [true, null, null, null, null, 5, 6, null, new FreeShippingAction(new GiftProduct(5, 6))];
        yield [true, null, null, null, null, 5, 6, true, new FreeShippingAction(new GiftProduct(5, 6))];
        yield [true, null, null, null, null, 5, 6, false, new FreeShippingAction(new GiftProduct(5, 6))];
        yield [false, null, null, null, null, 5, 6, false, new GiftProductAction(new GiftProduct(5, 6))];
        yield [false, Reduction::TYPE_AMOUNT, '120.6', 1, true, null, null, false, new AmountDiscountAction(new Money('120.6', 1, true), false)];
        yield [false, Reduction::TYPE_PERCENTAGE, '90.5', 1, true, 5, 6, true, new PercentageDiscountAction(new PercentageDiscount('90.5', true), false, new GiftProduct(5, 6))];
        yield [true, Reduction::TYPE_AMOUNT, '90.5', 1, true, 5, 6, true, new AmountDiscountAction(new Money('90.5', 1, true), true, new GiftProduct(5, 6))];
    }
}
