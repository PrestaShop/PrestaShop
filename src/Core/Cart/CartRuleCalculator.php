<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Cart;

use Cart;
use CartCore;
use CartRule;
use Currency;
use Hook;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShopDatabaseException;

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

    public function __construct(private readonly ?FeatureFlagStateCheckerInterface $featureFlagManager = null)
    {
    }

    /**
     * process cartrules calculation
     */
    public function applyCartRules()
    {
        foreach ($this->cartRules as $cartRule) {
            $this->applyCartRule($cartRule);
        }
    }

    /**
     * process cartrules calculation, excluding free-shipping processing
     */
    public function applyCartRulesWithoutFreeShipping()
    {
        foreach ($this->cartRules as $cartRule) {
            $this->applyCartRule($cartRule, false);
        }
    }

    /**
     * @param CartRuleCollection $cartRules
     *
     * @return CartRuleCalculator
     */
    public function setCartRules($cartRules)
    {
        $this->cartRules = $cartRules;

        return $this;
    }

    /**
     * @param CartRuleData $cartRuleData
     * @param bool $withFreeShipping used to calculate free shipping discount (avoid loop on shipping calculation)
     *
     * @throws PrestaShopDatabaseException
     */
    protected function applyCartRule(CartRuleData $cartRuleData, $withFreeShipping = true)
    {
        $cartRule = $cartRuleData->getCartRule();
        $cart = $this->calculator->getCart();

        if (!CartRule::isFeatureActive()) {
            return;
        }
        if ($cartRule->getType() === DiscountType::ORDER_LEVEL && (float) $cartRule->reduction_percent > 0 && $cartRule->reduction_product == 0) {
            if ($this->isDiscountFeatureFlagEnabled()) {
                $initialShippingFees = $this->calculator->getFees()->getInitialShippingFees();
                $productsTotal = $this->calculator->getRowTotal();
                $orderTotal = $productsTotal->add($initialShippingFees);
                $orderDiscountAmount = new AmountImmutable(
                    $orderTotal->getTaxExcluded() * $cartRule->reduction_percent / 100,
                    $orderTotal->getTaxIncluded() * $cartRule->reduction_percent / 100
                );
                $cartRuleData->addDiscountApplied($orderDiscountAmount);

                return;
            }
        }

        /*
         * Custom cart rule application from modules. Allows to create infinite possibilities of rules.
         *
         * If a module wants to apply a cart rule by it's own rules, it can use this hook.
         * You will receive instances and data from this context, so use proper methods to apply the discounts.
         *
         * If any discount was applied by a module, set $isAppliedByModules to avoid further processing of the cart rule.
         */
        $isAppliedByModules = null;
        Hook::exec(
            'actionApplyCartRule',
            [
                'cart_rule_calculator' => $this,
                'cart_rule_data' => $cartRuleData,
                'cart_rule' => $cartRule,
                'cart' => $cart,
                'with_free_shipping' => $withFreeShipping,
                'is_applied_by_modules' => &$isAppliedByModules,
            ]
        );
        // @phpstan-ignore-next-line
        if ($isAppliedByModules) {
            return;
        }

        // Free shipping on selected carriers
        if ($cartRule->free_shipping && $withFreeShipping) {
            $initialShippingFees = $this->calculator->getFees()->getInitialShippingFees();
            $this->calculator->getFees()->subDiscountValueShipping($initialShippingFees);
            $cartRuleData->addDiscountApplied($initialShippingFees);
        }

        /*
         * Free gift
         *
         * If this cart rule adds a free product as a gift, we need to discount the initial price of the product.
         * We loop the cart and we try to find a product with the same product ID, combination ID and no customization.
         * We use getInitialUnitPrice because the product row may have been already discounted by some previously applied
         * cart rule.
         */
        if ((int) $cartRule->gift_product) {
            foreach ($this->cartRows as $cartRow) {
                $product = $cartRow->getRowData();
                if ($product['id_product'] == $cartRule->gift_product
                    && ($product['id_product_attribute'] == $cartRule->gift_product_attribute
                        || !(int) $cartRule->gift_product_attribute)
                    && empty($product['id_customization'])
                ) {
                    $cartRuleData->addDiscountApplied($cartRow->getInitialUnitPrice());
                    $cartRow->applyFlatDiscount($cartRow->getInitialUnitPrice());
                }
            }
        }

        // Percentage discount
        if ((float) $cartRule->reduction_percent > 0) {
            // Discount (%) on the whole order
            if ($cartRule->reduction_product == 0) {
                foreach ($this->cartRows as $cartRow) {
                    $product = $cartRow->getRowData();
                    if (
                        array_key_exists('product_quantity', $product)
                        && 0 === (int) $product['product_quantity']
                    ) {
                        $cartRuleData->addDiscountApplied(new AmountImmutable(0.0, 0.0));
                    } elseif (($cartRule->reduction_exclude_special && !$product['reduction_applies'])
                        || !$cartRule->reduction_exclude_special) {
                        $amount = $cartRow->applyPercentageDiscount($cartRule->reduction_percent);
                        $cartRuleData->addDiscountApplied($amount);
                    }
                }
            }

            // Discount (%) on a specific product
            if ($cartRule->reduction_product > 0) {
                foreach ($this->cartRows as $cartRow) {
                    if ($cartRow->getRowData()['id_product'] == $cartRule->reduction_product) {
                        $amount = $cartRow->applyPercentageDiscount($cartRule->reduction_percent);
                        $cartRuleData->addDiscountApplied($amount);
                    }
                }
            }

            // Discount (%) on the cheapest product
            if ($cartRule->reduction_product == -1) {
                $cartRowCheapest = $this->getCheapestCartRow($cartRuleData);
                if ($cartRowCheapest !== null) {
                    if ($this->isDiscountFeatureFlagEnabled() && $cartRule->getType() === DiscountType::PRODUCT_LEVEL) {
                        // For product level discount the percentage is applied on all quantities not just one
                        $amount = $cartRowCheapest->applyPercentageDiscount($cartRule->reduction_percent);
                        $cartRuleData->addDiscountApplied($amount);
                    } else {
                        // Apply only on one product of the cheapest row
                        $discountTaxIncluded = $cartRowCheapest->getInitialUnitPrice()->getTaxIncluded()
                            * $cartRule->reduction_percent / 100;
                        $discountTaxExcluded = $cartRowCheapest->getInitialUnitPrice()->getTaxExcluded()
                            * $cartRule->reduction_percent / 100;
                        $amount = new AmountImmutable($discountTaxIncluded, $discountTaxExcluded);
                        $cartRowCheapest->applyFlatDiscount($amount);
                        $cartRuleData->addDiscountApplied($amount);
                    }
                }
            }

            // Discount (%) on the selection of products
            if ($cartRule->reduction_product == -2) {
                $concernedRows = $this->getCartRowsMatchingSelection($cartRuleData, $cart);
                foreach ($concernedRows as $cartRow) {
                    $amount = $cartRow->applyPercentageDiscount($cartRule->reduction_percent);
                    $cartRuleData->addDiscountApplied($amount);
                }
            }
        }

        // Amount discount (¤) : weighted calculation on all concerned rows
        //                weight factor got from price with same tax (incl/excl) as voucher
        if ((float) $cartRule->reduction_amount > 0) {
            $concernedRows = new CartRowCollection();
            if ($cartRule->reduction_product > 0) {
                // discount on single product
                foreach ($this->cartRows as $cartRow) {
                    if ($cartRow->getRowData()['id_product'] == $cartRule->reduction_product) {
                        $concernedRows->addCartRow($cartRow);
                    }
                }
            } elseif ($cartRule->reduction_product == 0) {
                // Discount (¤) on the whole order, that will be applied with a weight ratio on all related products
                $concernedRows = $this->cartRows;
            }

            // This new computing is only available for new discount with type product_level
            // The same amount will be applied on all targeted products not based on a weightRatio but on their quantity
            $productLevelDiscount = false;
            if ($this->isDiscountFeatureFlagEnabled() && $cartRule->getType() === DiscountType::PRODUCT_LEVEL) {
                if ($cartRule->reduction_product == -2) {
                    $productLevelDiscount = true;
                    $concernedRows = $this->getCartRowsMatchingSelection($cartRuleData, $cart);
                } elseif ($cartRule->reduction_product == -1) {
                    $productLevelDiscount = true;
                    $cartRowCheapest = $this->getCheapestCartRow($cartRuleData);
                    if ($cartRowCheapest !== null) {
                        $concernedRows->addCartRow($cartRowCheapest);
                    }
                }
            }

            // currency conversion
            $totalDiscountConverted = $discountConverted = $this->convertAmountBetweenCurrencies(
                $cartRule->reduction_amount,
                new Currency($cartRule->reduction_currency),
                new Currency($cart->id_currency)
            );

            // Get total sum of concerned rows
            $totalTaxIncl = $totalTaxExcl = 0;
            foreach ($concernedRows as $concernedRow) {
                $totalTaxIncl += $concernedRow->getFinalTotalPrice()->getTaxIncluded();
                $totalTaxExcl += $concernedRow->getFinalTotalPrice()->getTaxExcluded();
            }

            // The reduction cannot exceed the products total, except when we do not want it to be limited (for the partial use calculation)
            $discountConverted = min($discountConverted, $cartRule->reduction_tax ? $totalTaxIncl : $totalTaxExcl);

            // apply weighted discount:
            // on each line we apply a part of the discount corresponding to discount*rowWeight/total
            $taxRate = 0;
            foreach ($concernedRows as $concernedRow) {
                // Get current line tax rate
                $taxRate = $this->getTaxRateFromRow($concernedRow);
                $weightFactor = 0;
                if ($cartRule->reduction_tax) {
                    if ($productLevelDiscount) {
                        $weightFactor = (int) $concernedRow->getRowData()['cart_quantity'];
                    } elseif ($totalTaxIncl != 0) {
                        // if cart rule amount is set tax included : calculate weight tax included
                        $weightFactor = $concernedRow->getFinalTotalPrice()->getTaxIncluded() / $totalTaxIncl;
                    }
                    $discountAmountTaxIncl = $discountConverted * $weightFactor;
                    $discountAmountTaxIncl = min($discountAmountTaxIncl, $concernedRow->getFinalTotalPrice()->getTaxIncluded());
                    // Recompute tax included
                    $discountAmountTaxExcl = $discountAmountTaxIncl / (1 + $taxRate);
                } else {
                    if ($productLevelDiscount) {
                        $weightFactor = (int) $concernedRow->getRowData()['cart_quantity'];
                    } elseif ($totalTaxExcl != 0) {
                        // if cart rule amount is set tax excluded : calculate weight tax excluded
                        $weightFactor = $concernedRow->getFinalTotalPrice()->getTaxExcluded() / $totalTaxExcl;
                    }
                    $discountAmountTaxExcl = $discountConverted * $weightFactor;
                    $discountAmountTaxExcl = min($discountAmountTaxExcl, $concernedRow->getFinalTotalPrice()->getTaxExcluded());
                    // Recompute tax excluded
                    $discountAmountTaxIncl = $discountAmountTaxExcl * (1 + $taxRate);
                }
                $amount = new AmountImmutable($discountAmountTaxIncl, $discountAmountTaxExcl);

                // Update the unit prices of the items, they will be needed for possible next rules to be calculated
                $concernedRow->applyFlatDiscount($amount);

                // Apply the discount amount
                $cartRuleData->addDiscountApplied($amount);
            }

            if ($this->isDiscountFeatureFlagEnabled() && $cartRule->getType() === DiscountType::ORDER_LEVEL) {
                $totalProducts = $cartRule->reduction_tax ? $totalTaxIncl : $totalTaxExcl;
                // The total discount is superior to the products amount, so we apply the remaining part of the discount globally
                if ($totalDiscountConverted > $totalProducts) {
                    $remainingDiscount = $totalDiscountConverted - $totalProducts;

                    $initialShippingFees = $this->calculator->getFees()->getInitialShippingFees();
                    $shippingAmount = $cartRule->reduction_tax ? $initialShippingFees->getTaxIncluded() : $initialShippingFees->getTaxExcluded();
                    $shippingDiscount = min($remainingDiscount, $shippingAmount);

                    if ($shippingDiscount > 0) {
                        if ($cartRule->reduction_tax) {
                            $shippingDiscountTaxIncluded = $shippingDiscount;
                            $shippingDiscountTaxExcluded = $shippingDiscount / (1 + $taxRate);
                        } else {
                            $shippingDiscountTaxIncluded = $shippingDiscount * (1 + $taxRate);
                            $shippingDiscountTaxExcluded = $shippingDiscount;
                        }
                        $shippingDiscountAmount = new AmountImmutable($shippingDiscountTaxIncluded, $shippingDiscountTaxExcluded);

                        $this->calculator->getFees()->subDiscountValueShipping($shippingDiscountAmount);
                        $cartRuleData->addDiscountApplied($shippingDiscountAmount);
                    }
                }
            }
        }
    }

    protected function getCartRowsMatchingSelection(CartRuleData $cartRuleData, CartCore $cart): CartRowCollection
    {
        $concernedRows = new CartRowCollection();
        $cartRule = $cartRuleData->getCartRule();
        if ($cartRule->reduction_product == -2) {
            $selected_products = $cartRule->checkProductRestrictionsFromCart($cart, true);
            if (is_array($selected_products)) {
                foreach ($this->cartRows as $cartRow) {
                    $product = $cartRow->getRowData();
                    if ((in_array($product['id_product'] . '-' . $product['id_product_attribute'], $selected_products)
                            || in_array($product['id_product'] . '-0', $selected_products))
                        && (($cartRule->reduction_exclude_special && !$product['reduction_applies'])
                            || !$cartRule->reduction_exclude_special)) {
                        $concernedRows->addCartRow($cartRow);
                    }
                }
            }
        }

        return $concernedRows;
    }

    protected function getCheapestCartRow(CartRuleData $cartRuleData): ?CartRow
    {
        /** @var CartRow|null $cartRowCheapest */
        $cartRowCheapest = null;
        $cartRule = $cartRuleData->getCartRule();
        foreach ($this->cartRows as $cartRow) {
            $product = $cartRow->getRowData();
            if (
                (
                    ($cartRule->reduction_exclude_special && !$product['reduction_applies'])
                    || !$cartRule->reduction_exclude_special
                ) && (
                    $cartRowCheapest === null
                    || $cartRowCheapest->getInitialUnitPrice()->getTaxIncluded() > $cartRow->getInitialUnitPrice()->getTaxIncluded()
                )
            ) {
                $cartRowCheapest = $cartRow;
            }
        }

        return $cartRowCheapest;
    }

    /**
     * @param CartRow $row
     *
     * @return float tax rate of the given row
     */
    protected function getTaxRateFromRow($row)
    {
        // If the product was free, we return zero
        if (empty($row->getFinalTotalPrice()->getTaxExcluded())) {
            return 0.0;
        }

        // Calculate the rate
        $taxRate = ($row->getFinalTotalPrice()->getTaxIncluded() - $row->getFinalTotalPrice()->getTaxExcluded())
                    / $row->getFinalTotalPrice()->getTaxExcluded();

        // If we got some nonsense number below zero, we return zero
        if (empty($taxRate) || $taxRate < 0) {
            return 0.0;
        }

        return $taxRate;
    }

    /**
     * @param Calculator $calculator
     *
     * @return CartRuleCalculator
     */
    public function setCalculator($calculator)
    {
        $this->calculator = $calculator;

        return $this;
    }

    protected function convertAmountBetweenCurrencies($amount, Currency $currencyFrom, Currency $currencyTo)
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
     * @param CartRowCollection $cartRows
     *
     * @return CartRuleCalculator
     */
    public function setCartRows($cartRows)
    {
        $this->cartRows = $cartRows;

        return $this;
    }

    /**
     * @return CartRuleCollection
     */
    public function getCartRulesData()
    {
        return $this->cartRules;
    }

    /**
     * @return Calculator
     */
    public function getCalculator()
    {
        return $this->calculator;
    }

    /**
     * @return CartRowCollection
     */
    public function getCartRows()
    {
        return $this->cartRows;
    }

    /**
     * @return Fees
     */
    public function getFees()
    {
        return $this->fees;
    }

    protected function isDiscountFeatureFlagEnabled(): bool
    {
        return $this->featureFlagManager !== null && $this->featureFlagManager->isEnabled(FeatureFlagSettings::FEATURE_FLAG_DISCOUNT);
    }
}
