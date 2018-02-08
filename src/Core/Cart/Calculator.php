<?php
/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Cart;

use Cart;

/**
 * provides methods to process cart calculation
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
     * indicates if cart was already processed
     *
     * @var bool
     */
    protected $isProcessed = false;

    public function __construct(Cart $cart, $carrierId)
    {
        $this->setCart($cart);
        $this->setCarrierId($carrierId);
        $this->cartRows  = new CartRowCollection();
        $this->fees      = new Fees();
        $this->cartRules = new CartRuleCollection();
    }

    /**
     * insert a new cart row in the calculator
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
     * insert a new cart rule in the calculator
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
     * run the whole calculation process: calculate rows, discounts, fees
     *
     * @param $computePrecision
     *
     * @return $this
     */
    public function processCalculation($computePrecision)
    {
        // calculate product rows
        foreach ($this->cartRows as $cartRow) {
            $this->calculateRowTotal($cartRow);
        }
        // calculate fees
        $this->calculateFees($computePrecision);
        // calculate discounts
        $cartRuleCalculator = new CartRuleCalculator();
        $cartRuleCalculator->setCartRules($this->cartRules)
                           ->setCartRows($this->cartRows)
                           ->setCalculator($this);
        $cartRuleCalculator->applyCartRules();
        // store state
        $this->isProcessed = true;

        return $this;
    }

    /**
     * @param bool $withTaxes
     *
     * @return AmountImmutable
     * @throws \Exception
     */
    public function getTotal()
    {
        if (!$this->isProcessed) {
            throw new \Exception('Cart must be processed before getting its total');
        }

        $amount = new AmountImmutable;
        foreach ($this->cartRows as $cartRow) {
            $rowPrice = $cartRow->getFinalTotalPrice();
            $amount = $amount->add($rowPrice);
        }
        $shippingFees = $this->fees->getFinalShippingFees();
        $amount = $amount->add($shippingFees);
        $wrappingFees = $this->fees->getFinalWrappingFees();
        $amount = $amount->add($wrappingFees);

        return $amount;
    }

    /**
     * @return AmountImmutable
     * @throws \Exception
     */
    public function getRowTotal()
    {
        $amount = new AmountImmutable;
        foreach ($this->cartRows as $cartRow) {
            $amount = $amount->add($cartRow->getFinalTotalPrice());
        }

        return $amount;
    }

    /**
     * @return AmountImmutable
     * @throws \Exception
     */
    public function getDiscountTotal()
    {
        $amount = new AmountImmutable;
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
     * Calculate row total
     *
     * @param CartRow $cartRow
     */
    protected function calculateRowTotal(CartRow $cartRow)
    {
        $cartRow->processCalculation($this->cart);
    }

    /**
     * calculate wrapping and shipping fees
     *
     * @param $computePrecision
     */
    protected function calculateFees($computePrecision)
    {
        $this->fees->processCalculation($this->cart, $this->cartRows, $computePrecision, $this->id_carrier);
    }
}
