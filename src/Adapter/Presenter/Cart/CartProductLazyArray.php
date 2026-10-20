<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter\Cart;

use Context;
use Language;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingLazyArray;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use Tools;

class CartProductLazyArray extends ProductListingLazyArray
{
    /**
     * Re-evaluate the discount-display fields per cart line.
     *
     * WHY: in the cart, `reduction`, `specific_prices` and `price_without_reduction` are filled by
     * Product::getProductProperties() (see Cart::getProducts) and are therefore PER-COMBINATION, not
     * per cart line. When the same combination appears on several lines (e.g. different customizations),
     * a catalog price rule may apply to only some of them, yet the parent marks every line as discounted
     * (`has_discount = 0 != reduction`) and renders a phantom badge ("-0", the rule's %, etc.). The line's
     * actual paid price (`price_amount`, set line-specifically by Cart::getCartPrices) is reliable, so we
     * derive `has_discount` from `price_amount` vs `regular_price_amount` and clear the stale labels on
     * lines that did not actually receive a reduction. Correctly-discounted lines keep the parent's
     * already line-specific amounts. (#41278)
     *
     * Note: `price_amount` and `regular_price_amount` come from two different price computations
     * (getCartPriceFromCatalog vs Product::getProductProperties), so we compare them rounded to the
     * currency precision — float noise between the two paths must not fabricate a discount, while any
     * real reduction (>= the currency's smallest unit) is preserved.
     */
    protected function addPriceInformation(
        ProductPresentationSettings $settings,
        array $product
    ): void {
        parent::addPriceInformation($settings, $product);

        $precision = (int) (Context::getContext()->currency->precision ?? 2);
        $hasLineReduction = Tools::ps_round($this->product['regular_price_amount'], $precision)
            > Tools::ps_round($this->product['price_amount'], $precision);
        $this->product['has_discount'] = $hasLineReduction;

        if (!$hasLineReduction) {
            $this->product['discount_type'] = null;
            $this->product['discount_percentage'] = null;
            $this->product['discount_percentage_absolute'] = null;
            $this->product['discount_amount'] = null;
            $this->product['discount_amount_to_display'] = null;
            $this->product['discount_to_display'] = $this->product['regular_price'];
        }
    }

    /**
     * Custom implementation of quantity information for cart products. In cart, we use the data
     * a bit differently than in product listing. We also have edge cases when some of the ordered
     * items are in stock, and some are not.
     *
     * @param ProductPresentationSettings $settings
     * @param array $product
     * @param Language $language
     */
    public function addQuantityInformation(
        ProductPresentationSettings $settings,
        array $product,
        Language $language
    ) {
        // Define if we should show availability
        $show_price = $this->shouldShowPrice($settings, $product);
        $show_availability = $show_price && $settings->stock_management_enabled;
        $this->product['show_availability'] = $show_availability;

        // Default data
        $this->product['availability_message'] = null;
        $this->product['availability_submessage'] = null;
        $this->product['availability_date'] = null;
        $this->product['availability'] = null;

        // If we don't want to show availability, we return immediately
        if (!$show_availability) {
            return;
        }

        // If the product is disabled, but still displayed, we display a proper message
        if ($this->product['active'] != 1) {
            $this->product['availability'] = 'discontinued';
            $this->product['availability_message'] = $this->translator->trans('This product is no longer available for sale.', [], 'Shop.Notifications.Error');

            return;
        }

        // Get combination labels
        $combinationData = $this->getCombinationSpecificData();

        // All ordered items are in stock
        if ($product['stock_quantity'] >= $product['quantity']) {
            $this->product['availability'] = 'in_stock';
            if (!empty($combinationData['available_later'])) {
                $this->product['availability_message'] = $combinationData['available_now'];
            } elseif (!empty($product['available_later'])) {
                $this->product['availability_message'] = $product['available_now'];
            } else {
                $this->product['availability_message'] = $this->configuration->get('PS_LABEL_IN_STOCK_PRODUCTS')[$language->id] ?? null;
            }
        // Ordered items are partially in stock
        } elseif ($product['stock_quantity'] > 0) {
            // Define both parts of the message
            if (!empty($combinationData['available_later'])) {
                $messageForInStockProducts = $combinationData['available_now'];
            } elseif (!empty($product['available_later'])) {
                $messageForInStockProducts = $product['available_now'];
            } else {
                $messageForInStockProducts = $this->configuration->get('PS_LABEL_IN_STOCK_PRODUCTS')[$language->id] ?? null;
            }

            if ($product['allow_oosp']) {
                $this->product['availability'] = 'available';
                if (!empty($combinationData['available_later'])) {
                    $messageForOutOfStockProducts = $combinationData['available_later'];
                } elseif (!empty($product['available_later'])) {
                    $messageForOutOfStockProducts = $product['available_later'];
                } else {
                    $messageForOutOfStockProducts = $this->configuration->get('PS_LABEL_OOS_PRODUCTS_BOA')[$language->id] ?? null;
                }
            } else {
                $this->product['availability'] = 'unavailable';
                if (!empty($combinationData['available_later'])) {
                    $messageForOutOfStockProducts = $combinationData['available_later'];
                } elseif (!empty($product['available_later'])) {
                    $messageForOutOfStockProducts = $product['available_later'];
                } else {
                    $messageForOutOfStockProducts = $this->configuration->get('PS_LABEL_OOS_PRODUCTS_BOD')[$language->id] ?? null;
                }
            }

            // And construct the final message
            if (!empty($messageForInStockProducts) && !empty($messageForOutOfStockProducts)) {
                $this->product['availability_message'] = $this->translator->trans(
                    '%quantityInStock% - %messageForInStockProducts%, the rest - %messageForOutOfStockProducts%',
                    [
                        '%quantityInStock%' => $product['stock_quantity'],
                        '%messageForInStockProducts%' => $messageForInStockProducts,
                        '%messageForOutOfStockProducts%' => $messageForOutOfStockProducts,
                    ],
                    'Shop.Theme.Checkout'
                );
            }

        // None of the ordered items are in stock
        } else {
            if ($product['allow_oosp']) {
                $this->product['availability'] = 'available';
                if (!empty($combinationData['available_later'])) {
                    $this->product['availability_message'] = $combinationData['available_later'];
                } elseif (!empty($product['available_later'])) {
                    $this->product['availability_message'] = $product['available_later'];
                } else {
                    $this->product['availability_message'] = $this->configuration->get('PS_LABEL_OOS_PRODUCTS_BOA')[$language->id] ?? null;
                }
            } else {
                $this->product['availability'] = 'unavailable';
                if (!empty($combinationData['available_later'])) {
                    $this->product['availability_message'] = $combinationData['available_later'];
                } elseif (!empty($product['available_later'])) {
                    $this->product['availability_message'] = $product['available_later'];
                } else {
                    $this->product['availability_message'] = $this->configuration->get('PS_LABEL_OOS_PRODUCTS_BOD')[$language->id] ?? null;
                }
            }
        }
    }
}
