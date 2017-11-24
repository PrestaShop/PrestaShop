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

class Calculator
{

    /**
     * @var \Cart
     */
    protected $cart;

    protected $id_carrier;

    /**
     * @var CartRowCollection
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

    public function __construct()
    {
        $this->cartRows  = new CartRowCollection();
        $this->fees      = new Fees();
        $this->cartRules = new CartRuleCollection();
    }

    public function addCartRow(CartRow $cartRow)
    {
        $this->cartRows->addCartRow($cartRow);

        return $this;
    }

    public function addCartRule(CartRuleData $cartRule)
    {
        $this->cartRules->addCartRule($cartRule);

        return $this;
    }

    public function processCalculation()
    {
        // calculate product rows
        foreach ($this->cartRows as $cartRow) {
            $this->calculateRowTotal($cartRow);
        }
        // calculate fees
        $this->calculateFees();
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
     * @return Amount
     * @throws \Exception
     */
    public function getTotal()
    {
        if (!$this->isProcessed) {
            throw new \Exception('Cart must be processed before getting its total');
        }

        $amount = new Amount;
        foreach ($this->cartRows as $cartRow) {
            $rowPrice = $cartRow->getFinalTotalPrice();
            $amount->add($rowPrice);
        }
        $shippingFees = $this->fees->getFinalShippingFees();
        $amount->add($shippingFees);
        $wrappingFees = $this->fees->getFinalWrappingFees();
        $amount->add($wrappingFees);

        return $amount;
    }

    /**
     * @return Amount
     * @throws \Exception
     */
    public function getRowTotal()
    {
        $amount = new Amount;
        foreach ($this->cartRows as $cartRow) {
            $amount->setTaxIncluded($amount->getTaxIncluded() + $cartRow->getFinalTotalPrice()->getTaxIncluded());
            $amount->setTaxExcluded($amount->getTaxExcluded() + $cartRow->getFinalTotalPrice()->getTaxExcluded());
        }

        return $amount;
    }

    /**
     * @return Amount
     * @throws \Exception
     */
    public function getDiscountTotal()
    {
        $amount = new Amount;
        foreach ($this->cartRules as $cartRule) {
            $amount->setTaxIncluded($amount->getTaxIncluded() + $cartRule->getDiscountApplied()->getTaxIncluded());
            $amount->setTaxExcluded($amount->getTaxExcluded() + $cartRule->getDiscountApplied()->getTaxExcluded());
        }

        return $amount;
    }

    /**
     * Calculate row total
     *
     * @param \PrestaShop\PrestaShop\Core\Cart\CartRow $cartRow
     */
    protected function calculateRowTotal(CartRow $cartRow)
    {
        $cartRow->processCalculation($this->cart);
    }

    /**
     * @param \CartCore $cart
     *
     * @return Calculator
     */
    public function setCart($cart)
    {
        $this->cart = $cart;

        return $this;
    }

    protected function calculateFees()
    {
        $this->fees->processCalculation($this->cart, $this->cartRows, $this->id_carrier);
    }

    /**
     * @param mixed $id_carrier
     *
     * @return Calculator
     */
    public function setCarrierId($id_carrier)
    {
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
}
