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

namespace PrestaShop\PrestaShop\Adapter\Presenter\Cart;

use Language;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingLazyArray;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;

class CartProductLazyArray extends ProductListingLazyArray
{
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
