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

class CartRuleCalculator
{
    /**
     * @var Calculator
     */
    protected $calculator;

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

    public function applyCartRules()
    {
        foreach ($this->cartRules as $cartRule) {
            $this->applyCartRule($cartRule);
        }
    }

    /**
     * @param \PrestaShop\PrestaShop\Core\Cart\CartRuleCollection $cartRules
     *
     * @return CartRuleCalculator
     */
    public function setCartRules($cartRules)
    {
        $this->cartRules = $cartRules;

        return $this;
    }

    protected function applyCartRule(CartRuleData $cartRuleData)
    {
        $cartRule = $cartRuleData->getCartRule();
        $cart     = $this->calculator->getCart();

        if (!\CartRule::isFeatureActive()) {
            return;
        }

        // Free shipping on selected carriers
        if ($cartRule->free_shipping) {
            $initialShippingFees = $this->calculator->getFees()->getInitialShippingFees();
            $this->calculator->getFees()->subDiscountValueShipping($initialShippingFees);
            $cartRuleData->addDiscountApplied($initialShippingFees);
        }

        // Free gift
        if ((int) $cartRule->gift_product) {
            foreach ($this->cartRows as $cartRow) {
                $product = $cartRow->getRowData();
                if ($product['id_product'] == $cartRule->gift_product
                    && ($product['id_product_attribute'] == $cartRule->gift_product_attribute
                        || !(int) $cartRule->gift_product_attribute)
                ) {
                    $cartRow->applyFlatDiscount($cartRow->getFinalUnitPrice());
                    $cartRuleData->addDiscountApplied($cartRow->getFinalUnitPrice());
                }
            }
        }

        // Discount (%) on the whole order
        if ($cartRule->reduction_percent && $cartRule->reduction_product == 0) {
            foreach ($this->cartRows as $cartRow) {
                $amount = $cartRow->applyPercentageDiscount($cartRule->reduction_percent);
                $cartRuleData->addDiscountApplied($amount);
            }
        }

        // Discount (%) on a specific product
        if ($cartRule->reduction_percent && $cartRule->reduction_product > 0) {
            foreach ($this->cartRows as $cartRow) {
                if ($cartRow->getRowData()['id_product'] == $cartRule->reduction_product) {
                    $amount = $cartRow->applyPercentageDiscount($cartRule->reduction_percent);
                    $cartRuleData->addDiscountApplied($amount);
                }
            }
        }

        // Discount (%) on the cheapest product
        if ($cartRule->reduction_percent && $cartRule->reduction_product == -1) {
            /** @var CartRow|null $cartRowCheapest */
            $cartRowCheapest = null;
            foreach ($this->cartRows as $cartRow) {
                if ($cartRowCheapest === null
                    || $cartRowCheapest->getInitialUnitPrice()->getTaxIncluded() > $cartRow->getInitialUnitPrice()
                                                                                           ->getTaxIncluded()
                ) {
                    $cartRowCheapest = $cartRow;
                }
            }
            if ($cartRowCheapest !== null) {
                // apply only on one product of the cheapest row
                $discountTaxIncluded = $cartRowCheapest->getInitialUnitPrice()->getTaxIncluded()
                                       * $cartRule->reduction_percent / 100;
                $discountTaxExcluded = $cartRowCheapest->getInitialUnitPrice()->getTaxIncluded()
                                       * $cartRule->reduction_percent / 100;
                $amount              = new AmountImmutable($discountTaxIncluded, $discountTaxExcluded);
                $cartRowCheapest->applyFlatDiscount($amount);
                $cartRuleData->addDiscountApplied($amount);
            }
        }

        // Discount (%) on the selection of products
        if ($cartRule->reduction_percent && $cartRule->reduction_product == -2) {
            $selected_products = $cartRule->checkProductRestrictionsFromCart($cart, true);
            if (is_array($selected_products)) {
                foreach ($this->cartRows as $cartRow) {
                    $product = $cartRow->getRowData();
                    if (in_array($product['id_product'] . '-' . $product['id_product_attribute'], $selected_products)
                        || in_array($product['id_product'] . '-0', $selected_products)
                           && (($cartRule->reduction_exclude_special && !$product['reduction_applies'])
                               || !$cartRule->reduction_exclude_special)) {
                        $amount = $cartRow->applyPercentageDiscount($cartRule->reduction_percent);
                        $cartRuleData->addDiscountApplied($amount);
                    }
                }
            }
        }

        // Discount (¤) : weighted calculation on all concerned rows
        //                weight factor got from price with same tax (incl/excl) as voucher
        if ((float) $cartRule->reduction_amount > 0) {
            // currency conversion
            $discountConverted = $this->convertAmountBetweenCurrencies(
                $cartRule->reduction_amount,
                new \Currency($cartRule->reduction_currency),
                new \Currency($cart->id_currency)
            );

            $concernedRows = new CartRowCollection();
            if ($cartRule->reduction_product > 0) {
                // discount on single product
                foreach ($this->cartRows as $cartRow) {
                    if ($cartRow->getRowData()['id_product'] == $cartRule->reduction_product) {
                        $concernedRows->addCartRow($cartRow);
                    }
                }
            } elseif ($cartRule->reduction_product == 0) {
                // Discount (¤) on the whole order
                $concernedRows = $this->cartRows;
            }
            /*
             * Reduction on the cheapest or on the selection is not really meaningful and has been disabled in the backend
             * Please keep this code, so it won't be considered as a bug
             * elseif ($this->reduction_product == -1)
             * elseif ($this->reduction_product == -2)
             */

            // get total of concerned rows
            $totalTaxIncl = $totalTaxExcl = 0;
            foreach ($concernedRows as $concernedRow) {
                $totalTaxIncl += $concernedRow->getFinalTotalPrice()->getTaxIncluded();
                $totalTaxExcl += $concernedRow->getFinalTotalPrice()->getTaxExcluded();
            }
            // apply weighted discount :
            // on each line we apply a part of the discount corresponding to discount*rowWeight/total
            foreach ($concernedRows as $concernedRow) {
                // get current line tax rate
                $taxRate = 0;
                if ($concernedRow->getFinalTotalPrice()->getTaxExcluded() != 0) {
                    $taxRate = ($concernedRow->getFinalTotalPrice()->getTaxIncluded()
                                - $concernedRow->getFinalTotalPrice()->getTaxExcluded())
                               / $concernedRow->getFinalTotalPrice()->getTaxExcluded();
                }
                $weightFactor = 0;
                if ($cartRule->reduction_tax) {
                    // if cart rule amount is set tax included : calculate weight tax included
                    if ($totalTaxIncl != 0) {
                        $weightFactor = $concernedRow->getFinalTotalPrice()->getTaxIncluded() / $totalTaxIncl;
                    }
                    $discountAmountTaxIncl = $discountConverted * $weightFactor;
                    // recalculate tax included
                    $discountAmountTaxExcl = $discountAmountTaxIncl / (1 + $taxRate);
                } else {
                    // if cart rule amount is set tax excluded : calculate weight tax excluded
                    if ($totalTaxExcl != 0) {
                        $weightFactor = $concernedRow->getFinalTotalPrice()->getTaxExcluded() / $totalTaxExcl;
                    }
                    $discountAmountTaxExcl = $discountConverted * $weightFactor;
                    // recalculate tax excluded
                    $discountAmountTaxIncl = $discountAmountTaxExcl * (1 + $taxRate);
                }
                $amount = new AmountImmutable($discountAmountTaxIncl, $discountAmountTaxExcl);
                $concernedRow->applyFlatDiscount($amount);
                $cartRuleData->addDiscountApplied($amount);
            }
        }
    }

    /**
     * @param \PrestaShop\PrestaShop\Core\Cart\Calculator $calculator
     *
     * @return CartRuleCalculator
     */
    public function setCalculator($calculator)
    {
        $this->calculator = $calculator;

        return $this;
    }

    protected function convertAmountBetweenCurrencies($amount, \Currency $currencyFrom, \Currency $currencyTo)
    {
        if ($amount == 0 || $currencyFrom->conversion_rate == 0) {
            return 0;
        }

        // convert to default currency
        $amount /= $currencyFrom->conversion_rate;
        // convert to destination currency
        $amount *= $currencyTo->conversion_rate;

        return $amount;
    }

    /**
     * @param \PrestaShop\PrestaShop\Core\Cart\CartRowCollection $cartRows
     *
     * @return CartRuleCalculator
     */
    public function setCartRows($cartRows)
    {
        $this->cartRows = $cartRows;

        return $this;
    }
}
