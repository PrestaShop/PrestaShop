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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\Utility;

use Cart;
use CartRule;
use Context;
use Order;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use Shop;
use Tax;
use Tools;

/**
 * legacy code here : used to test behaviour between recent and previous version
 */
class CartOld extends Cart
{
    /**
     * This function returns the total cart amount
     *
     * @param bool $with_taxes With or without taxes
     * @param int $type Total type enum
     *                  - Cart::ONLY_PRODUCTS
     *                  - Cart::ONLY_DISCOUNTS
     *                  - Cart::BOTH
     *                  - Cart::BOTH_WITHOUT_SHIPPING
     *                  - Cart::ONLY_SHIPPING
     *                  - Cart::ONLY_WRAPPING
     *                  - Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING
     *                  - Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING
     * @param array $products
     * @param int $id_carrier
     * @param bool $use_cache Allow using cache of the method CartRule::getContextualValue
     *
     * @return float Order total
     */
    public function getOrderTotalV1(
        $with_taxes = true,
        $type = Cart::BOTH,
        $products = null,
        $id_carrier = null,
        $use_cache = true
    ) {
        // Dependencies
        /** @var \PrestaShop\PrestaShop\Adapter\Product\PriceCalculator $price_calculator */
        $price_calculator = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\Product\\PriceCalculator');

        $ps_use_ecotax = $this->configuration->get('PS_USE_ECOTAX');
        $ps_round_type = $this->configuration->get('PS_ROUND_TYPE');
        $ps_ecotax_tax_rules_group_id = $this->configuration->get('PS_ECOTAX_TAX_RULES_GROUP_ID');
        $compute_precision = $this->configuration->get('_PS_PRICE_COMPUTE_PRECISION_');

        if (!$this->id) {
            return 0;
        }

        $type = (int) $type;
        $array_type = [
            Cart::ONLY_PRODUCTS,
            Cart::ONLY_DISCOUNTS,
            Cart::BOTH,
            Cart::BOTH_WITHOUT_SHIPPING,
            Cart::ONLY_SHIPPING,
            Cart::ONLY_WRAPPING,
            Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING,
            Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING,
        ];

        // Define virtual context to prevent case where the cart is not the in the global context
        $virtual_context = Context::getContext()->cloneContext();
        $virtual_context->cart = $this;

        if (!in_array($type, $array_type)) {
            die(Tools::displayError());
        }

        $with_shipping = in_array($type, [Cart::BOTH, Cart::ONLY_SHIPPING]);

        // if cart rules are not used
        if ($type == Cart::ONLY_DISCOUNTS && !CartRule::isFeatureActive()) {
            return 0;
        }

        // no shipping cost if is a cart with only virtuals products
        $virtual = $this->isVirtualCart();
        if ($virtual && $type == Cart::ONLY_SHIPPING) {
            return 0;
        }

        if ($virtual && $type == Cart::BOTH) {
            $type = Cart::BOTH_WITHOUT_SHIPPING;
        }

        if ($with_shipping || $type == Cart::ONLY_DISCOUNTS) {
            if (null === $products && null === $id_carrier) {
                $shipping_fees = $this->getTotalShippingCost(null, (bool) $with_taxes);
            } else {
                $shipping_fees = $this->getPackageShippingCost((int) $id_carrier, (bool) $with_taxes, null, $products);
            }
        } else {
            $shipping_fees = 0;
        }

        if ($type == Cart::ONLY_SHIPPING) {
            return $shipping_fees;
        }

        if ($type == Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING) {
            $type = Cart::ONLY_PRODUCTS;
        }

        $param_product = true;
        if (null === $products) {
            $param_product = false;
            $products = $this->getProducts();
        }

        if ($type == Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING) {
            foreach ($products as $key => $product) {
                if ($product['is_virtual']) {
                    unset($products[$key]);
                }
            }
            $type = Cart::ONLY_PRODUCTS;
        }

        $order_total = 0;
        if (Tax::excludeTaxeOption()) {
            $with_taxes = false;
        }

        $products_total = [];
        $ecotax_total = 0;
        $productLines = $this->countProductLines($products);

        foreach ($products as $product) {
            // products refer to the cart details

            if (array_key_exists('is_gift', $product) && $product['is_gift']) {
                // products given away may appear twice if added manually
                // so we prevent adding their subtotal twice if another line is found
                $productIndex = $product['id_product'] . '-' . $product['id_product_attribute'];
                if ($productLines[$productIndex] > 1) {
                    continue;
                }
            }

            if ($virtual_context->shop->id != $product['id_shop']) {
                $virtual_context->shop = new Shop((int) $product['id_shop']);
            }

            $id_address = $this->getProductAddressId($product);

            // The $null variable below is not used,
            // but it is necessary to pass it to getProductPrice because
            // it expects a reference.
            $null = null;
            $price = $price_calculator->getProductPrice(
                (int) $product['id_product'],
                $with_taxes,
                (int) $product['id_product_attribute'],
                6,
                null,
                false,
                true,
                $product['cart_quantity'],
                false,
                (int) $this->id_customer ? (int) $this->id_customer : null,
                (int) $this->id,
                $id_address,
                $null,
                $ps_use_ecotax,
                true,
                $virtual_context,
                true,
                (int) $product['id_customization']
            );

            $id_tax_rules_group = $this->findTaxRulesGroupId($with_taxes, $product, $virtual_context);

            if (in_array($ps_round_type, [Order::ROUND_ITEM, Order::ROUND_LINE])) {
                if (!isset($products_total[$id_tax_rules_group])) {
                    $products_total[$id_tax_rules_group] = 0;
                }
            } elseif (!isset($products_total[$id_tax_rules_group . '_' . $id_address])) {
                $products_total[$id_tax_rules_group . '_' . $id_address] = 0;
            }

            switch ($ps_round_type) {
                case Order::ROUND_TOTAL:
                    $products_total[$id_tax_rules_group . '_' . $id_address] += $price
                        * (int) $product['cart_quantity'];

                    break;

                case Order::ROUND_LINE:
                    $product_price = $price * $product['cart_quantity'];
                    $products_total[$id_tax_rules_group] += Tools::ps_round($product_price, $compute_precision);

                    break;

                case Order::ROUND_ITEM:
                default:
                    $product_price = /*$with_taxes ? $tax_calculator->addTaxes($price) : */
                        $price;
                    $products_total[$id_tax_rules_group] += Tools::ps_round($product_price, $compute_precision)
                        * (int) $product['cart_quantity'];

                    break;
            }
        }

        foreach ($products_total as $key => $price) {
            $order_total += $price;
        }

        $order_total_products = $order_total;

        if ($type == Cart::ONLY_DISCOUNTS) {
            $order_total = 0;
        }

        $wrappingFees = $this->calculateWrappingFees($with_taxes, $type);
        if ($type == Cart::ONLY_WRAPPING) {
            return $wrappingFees;
        }

        $order_total_discount = 0;
        $order_shipping_discount = 0;
        if (!in_array($type, [Cart::ONLY_SHIPPING, Cart::ONLY_PRODUCTS]) && CartRule::isFeatureActive()) {
            $cart_rules = $this->getTotalCalculationCartRules($type, $with_shipping);

            $package = [
                'id_carrier' => $id_carrier,
                'id_address' => $this->getDeliveryAddressId($products),
                'products' => $products,
            ];

            // Then, calculate the contextual value for each one
            $flag = false;
            foreach ($cart_rules as $item) {
                /** @var \CartRule $cartRule */
                $cartRule = $item['obj'];
                // If the cart rule offers free shipping, add the shipping cost
                if (($with_shipping || $type == Cart::ONLY_DISCOUNTS) && $cartRule->free_shipping && !$flag) {
                    $order_shipping_discount = (float) Tools::ps_round(
                        $cartRule->getContextualValue(
                            $with_taxes,
                            $virtual_context,
                            CartRule::FILTER_ACTION_SHIPPING,
                            ($param_product ? $package : null),
                            $use_cache
                        ),
                        $compute_precision
                    );
                    $flag = true;
                }

                // If the cart rule is a free gift, then add the free gift value only if the gift is in this package
                if (!$this->shouldExcludeGiftsDiscount && (int) $cartRule->gift_product) {
                    $in_order = false;
                    foreach ($products as $product) {
                        if ($cartRule->gift_product == $product['id_product']
                            && $cartRule->gift_product_attribute
                            == $product['id_product_attribute']) {
                            $in_order = true;
                        }
                    }

                    if ($in_order) {
                        $order_total_discount += $cartRule->getContextualValue(
                            $with_taxes,
                            $virtual_context,
                            CartRule::FILTER_ACTION_GIFT,
                            $package,
                            $use_cache
                        );
                    }
                }

                // If the cart rule offers a reduction, the amount is prorated (with the products in the package)
                if ($cartRule->reduction_percent > 0 || $cartRule->reduction_amount > 0) {
                    $order_total_discount += Tools::ps_round(
                        $cartRule->getContextualValue(
                            $with_taxes,
                            $virtual_context,
                            CartRule::FILTER_ACTION_REDUCTION,
                            $package,
                            $use_cache
                        ),
                        $compute_precision
                    );
                }
            }

            $order_total_discount = min(Tools::ps_round($order_total_discount, 2), (float) $order_total_products)
                + (float) $order_shipping_discount;
            $order_total -= $order_total_discount;
        }

        if ($type == Cart::BOTH) {
            $order_total += $shipping_fees + $wrappingFees;
        }

        if ($order_total < 0 && $type != Cart::ONLY_DISCOUNTS) {
            return 0;
        }

        if ($type == Cart::ONLY_DISCOUNTS) {
            return $order_total_discount;
        }

        return Tools::ps_round((float) $order_total, $compute_precision);
    }
}
