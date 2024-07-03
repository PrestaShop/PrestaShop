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

namespace PrestaShop\PrestaShop\Core\Cart;

use CartCore;
use Currency;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;
use Tools;

/**
 * provides methods to process cart calculation.
 */
class Calculator
{
    /**
     * @var CartCore
     */
    protected $cart;

    /**
     * @var int
     */
    protected $id_carrier;

    /**
     * @var int|null
     */
    protected $orderId;

    /**
     * @var CartRowCollection collection of cart content row (product+qty)
     */
    protected $cartRows;

    /**
     * @var CartRuleCollection
     */
    protected $cartRules;

    /**
     * @var Fees
     */
    protected $fees;

    /**
     * @var CartRuleCalculator
     */
    protected $cartRuleCalculator;

    /**
     * indicates if cart was already processed.
     *
     * @var bool
     */
    protected $isProcessed = false;

    /**
     * @var int|null
     */
    protected $computePrecision;

    /**
     * @param CartCore $cart
     * @param int $carrierId
     * @param int|null $computePrecision
     * @param int|null $orderId
     */
    public function __construct(CartCore $cart, $carrierId, ?int $computePrecision = null, ?int $orderId = null)
    {
        $this->setCart($cart);
        $this->setCarrierId($carrierId);
        $this->orderId = $orderId;
        $this->cartRows = new CartRowCollection();
        $this->fees = new Fees($this->orderId);
        $this->cartRules = new CartRuleCollection();
        $this->cartRuleCalculator = new CartRuleCalculator();

        if (null === $computePrecision) {
            $currency = new Currency((int) $cart->id_currency);
            $computePrecision = (new ComputingPrecision())->getPrecision($currency->precision);
        }
        $this->computePrecision = $computePrecision;
    }

    /**
     * insert a new cart row in the calculator.
     *
     * @param CartRow $cartRow cart item row (product+qty informations)
     *
     * @return $this
     */
    public function addCartRow(CartRow $cartRow)
    {
        // reset state
        $this->isProcessed = false;

        $this->cartRows->addCartRow($cartRow);

        return $this;
    }

    /**
     * insert a new cart rule in the calculator.
     *
     * @param \PrestaShop\PrestaShop\Core\Cart\CartRuleData $cartRule
     *
     * @return $this
     */
    public function addCartRule(CartRuleData $cartRule)
    {
        // reset state
        $this->isProcessed = false;

        $this->cartRules->addCartRule($cartRule);

        return $this;
    }

    /**
     * run the whole calculation process: calculate rows, discounts, fees.
     *
     * @param int $computePrecision Not used since 1.7.7.0, kept for backward compatibility
     *
     * @return $this
     */
    public function processCalculation($computePrecision = null)
    {
        // calculate product rows
        $this->calculateRows();
        // calculate fees
        $this->calculateFees();
        // calculate discounts
        $this->calculateCartRules();
        // store state
        $this->isProcessed = true;

        return $this;
    }

    /**
     * @param bool $ignoreProcessedFlag force getting total even if calculation was not made internaly
     *
     * @return AmountImmutable
     *
     * @throws \Exception
     */
    public function getTotal($ignoreProcessedFlag = false)
    {
        if (!$this->isProcessed && !$ignoreProcessedFlag) {
            throw new \Exception('Cart must be processed before getting its total');
        }

        /*
         * First, we get the total price of items in the cart with no discounts.
         * We will round everything once again, because some prices in the sum may not be rounded
         * to keep their precision.
         */
        $amount = $this->rounded($this->getRowTotalWithoutDiscount(), $this->computePrecision);

        /*
         * Now, we subtract the amount of discounts from this sum.
         */
        $amount = $amount->sub($this->rounded($this->getDiscountTotal(), $this->computePrecision));

        /*
         * Next thing, we add a rounded value for shipping fees.
         */
        $shippingFees = $this->fees->getInitialShippingFees();
        if (null !== $shippingFees) {
            $amount = $amount->add($this->rounded($shippingFees, $this->computePrecision));
        }

        /*
         * And absolutely last, we add wrapping fees if they are present.
         */
        $wrappingFees = $this->fees->getFinalWrappingFees();
        if (null !== $wrappingFees) {
            $amount = $amount->add($this->rounded($wrappingFees, $this->computePrecision));
        }


        return $amount;
    }

    /**
     * @return AmountImmutable
     *
     * @throws \Exception
     */
    public function getRowTotal()
    {
        $amount = new AmountImmutable();
        foreach ($this->cartRows as $cartRow) {
            $amount = $amount->add($cartRow->getFinalTotalPrice());
        }

        return $amount;
    }

    /**
     * This method returns sum of all cart lines - products.
     * It returns the prices with their original price, without applying any cart rules on them.
     *
     * @return AmountImmutable
     *
     * @throws \Exception
     */
    public function getRowTotalWithoutDiscount()
    {
        $amount = new AmountImmutable();
        foreach ($this->cartRows as $cartRow) {
            /*
             * We round the tax included price only, to keep the calculations using
             * the displayed prices the user sees. If he sees 999 EUR, we will use this
             * rounded values for calculations, no magic 999.5 EUR behind this.
             *
             * For tax excluded price however, we keep the non-rounded value, because we could
             * have a big difference at the end of the calculation. We will round the tax
             * excluded price as a last thing.
             *
             * If we decide one day to support using tax excluded price as the primary one, we
             * can reverse this the other way around.
             */
            $amount = $amount->add($this->roundedTaxIncluded($cartRow->getInitialTotalPrice(), $this->computePrecision));
        }

        return $amount;
    }

    /**
     * @return AmountImmutable
     *
     * @throws \Exception
     */
    public function getDiscountTotal()
    {
        $amount = new AmountImmutable();
        $isFreeShippingAppliedToAmount = false;
        foreach ($this->cartRules as $cartRule) {
            if ((bool) $cartRule->getRuleData()['free_shipping']) {
                if ($isFreeShippingAppliedToAmount) {
                    $initialShippingFees = $this->getFees()->getInitialShippingFees();
                    $amount = $amount->sub($initialShippingFees);
                }
                $isFreeShippingAppliedToAmount = true;
            }
            $amount = $amount->add($cartRule->getDiscountApplied());
        }

        $allowedMaxDiscount = $this->getRowTotalWithoutDiscount();

        if (null !== $this->getFees()->getFinalShippingFees()) {
            $shippingDiscount = (new AmountImmutable())
                ->add($this->getFees()->getInitialShippingFees())
                ->sub($this->getFees()->getFinalShippingFees())
            ;
            $allowedMaxDiscount = $allowedMaxDiscount->add($shippingDiscount);
        }
        // discount cannot be above total cart price
        if ($amount > $allowedMaxDiscount) {
            $amount = $allowedMaxDiscount;
        }

        return $amount;
    }

    /**
     * @param CartCore $cart
     *
     * @return Calculator
     */
    protected function setCart($cart)
    {
        // reset state
        $this->isProcessed = false;

        $this->cart = $cart;

        return $this;
    }

    /**
     * @param mixed $id_carrier
     *
     * @return Calculator
     */
    protected function setCarrierId($id_carrier)
    {
        // reset state
        $this->isProcessed = false;

        $this->id_carrier = $id_carrier;

        return $this;
    }

    /**
     * @return \PrestaShop\PrestaShop\Core\Cart\Fees
     */
    public function getFees()
    {
        return $this->fees;
    }

    /**
     * @return CartCore
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * Calculate row total.
     *
     * @param CartRow $cartRow
     */
    protected function calculateRowTotal(CartRow $cartRow)
    {
        $cartRow->processCalculation($this->cart);
    }

    /**
     * Calculate only product rows.
     */
    public function calculateRows()
    {
        foreach ($this->cartRows as $cartRow) {
            $this->calculateRowTotal($cartRow);
        }
    }

    /**
     * Calculate only cart rules (rows and fees have to be calculated first).
     */
    public function calculateCartRules()
    {
        $this->cartRuleCalculator->setCartRules($this->cartRules)
            ->setCartRows($this->cartRows)
            ->setCalculator($this)
            ->applyCartRules();
    }

    /**
     * calculate only cart rules (rows and fees have to be calculated first), but don't process free-shipping discount
     * (avoid loop on shipping calculation)
     */
    public function calculateCartRulesWithoutFreeShipping()
    {
        $this->cartRuleCalculator->setCartRules($this->cartRules)
            ->setCartRows($this->cartRows)
            ->setCalculator($this)
            ->applyCartRulesWithoutFreeShipping();
    }

    /**
     * calculate wrapping and shipping fees (rows have to be calculated first).
     *
     * @param int|null $computePrecision Not used since 1.7.7.0, kept for backward compatibility
     */
    public function calculateFees($computePrecision = null)
    {
        $this->fees->processCalculation($this->cart, $this->cartRows, $this->computePrecision, $this->id_carrier);
    }

    /**
     * @return CartRuleCollection
     */
    public function getCartRulesData()
    {
        return $this->cartRuleCalculator->getCartRulesData();
    }

    /**
     * Rounds both values in the amount by given precision. Useful for total sums,
     * where no additional precision is required and we want the final price.
     *
     * @param AmountImmutable $amount
     * @param int $computePrecision
     *
     * @return AmountImmutable
     */
    private function rounded(AmountImmutable $amount, int $computePrecision)
    {
        return new AmountImmutable(
            Tools::ps_round($amount->getTaxIncluded(), $computePrecision),
            Tools::ps_round($amount->getTaxExcluded(), $computePrecision)
        );
    }

    /**
     * Rounds only tax included price by given precision and leaves the tax
     * excluded price with original precision. Useful for inter-calculations
     * when we have one primary price to use.
     *
     * @param AmountImmutable $amount
     * @param int $computePrecision
     *
     * @return AmountImmutable
     */
    private function roundedTaxIncluded(AmountImmutable $amount, int $computePrecision)
    {
        return new AmountImmutable(
            Tools::ps_round($amount->getTaxIncluded(), $computePrecision),
            $amount->getTaxExcluded()
        );
    }
}
