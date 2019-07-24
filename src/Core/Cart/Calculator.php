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

namespace PrestaShop\PrestaShop\Core\Cart;

use Cart;

/**
 * provides methods to process cart calculation.
 */
class Calculator
{
    /**
     * @var \Cart
     */
    protected $cart;

    /**
     * @var int
     */
    protected $id_carrier;

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

    public function __construct(Cart $cart, $carrierId)
    {
        $this->setCart($cart);
        $this->setCarrierId($carrierId);
        $this->cartRows = new CartRowCollection();
        $this->fees = new Fees();
        $this->cartRules = new CartRuleCollection();
        $this->cartRuleCalculator = new CartRuleCalculator();
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
     * @param int $computePrecision
     *
     * @return $this
     */
    public function processCalculation($computePrecision)
    {
        // calculate product rows
        $this->calculateRows();
        // calculate fees
        $this->calculateFees($computePrecision);
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

        $amount = new AmountImmutable();
        foreach ($this->cartRows as $cartRow) {
            $rowPrice = $cartRow->getFinalTotalPrice();
            $amount = $amount->add($rowPrice);
        }
        $shippingFees = $this->fees->getFinalShippingFees();
        if (null !== $shippingFees) {
            $amount = $amount->add($shippingFees);
        }
        $wrappingFees = $this->fees->getFinalWrappingFees();
        if (null !== $wrappingFees) {
            $amount = $amount->add($wrappingFees);
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
     * @return AmountImmutable
     *
     * @throws \Exception
     */
    public function getDiscountTotal()
    {
        $amount = new AmountImmutable();
        foreach ($this->cartRules as $cartRule) {
            $amount = $amount->add($cartRule->getDiscountApplied());
        }

        return $amount;
    }

    /**
     * @param \CartCore $cart
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
     * @return \Cart
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
     * calculate only product rows.
     */
    public function calculateRows()
    {
        foreach ($this->cartRows as $cartRow) {
            $this->calculateRowTotal($cartRow);
        }
    }

    /**
     * calculate only cart rules (rows and fees have to be calculated first).
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
     * @param int $computePrecision
     */
    public function calculateFees($computePrecision)
    {
        $this->fees->processCalculation($this->cart, $this->cartRows, $computePrecision, $this->id_carrier);
    }

    /**
     * @return CartRuleCollection
     */
    public function getCartRulesData()
    {
        return $this->cartRuleCalculator->getCartRulesData();
    }
}
