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

namespace PrestaShop\PrestaShop\Adapter\Cart\QueryHandler;

use Cart;
use Context;
use Currency;
use Customer;
use Db;
use Gender;
use Group;
use Image;
use ImageManager;
use Order;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartForViewing;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler\GetCartForViewingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartView;
use Product;
use StockAvailable;
use Tools;
use Validate;

/**
 * @internal
 */
final class GetCartForViewingHandler implements GetCartForViewingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetCartForViewing $query)
    {
        $cartId = $query->getCartId()->getValue();
        $cart = new Cart($cartId);

        if ($cart->id !== $cartId) {
            throw new CartNotFoundException(sprintf('Cart with id "%s" were not found', $cartId));
        }

        $customer = new Customer($cart->id_customer);
        $currency = new Currency($cart->id_currency);

        $context = Context::getContext();
        $context->cart = $cart;
        $context->currency = $currency;
        $context->customer = $customer;

        $products = $cart->getProducts();
        $summary = $cart->getSummaryDetails();

        $id_order = (int) Order::getIdByCartId($cart->id);
        $order = new Order($id_order);

        if (Validate::isLoadedObject($order)) {
            $tax_calculation_method = $order->getTaxCalculationMethod();
            $id_shop = (int) $order->id_shop;
        } else {
            $id_shop = (int) $cart->id_shop;
            $tax_calculation_method = Group::getPriceDisplayMethod(Group::getCurrent()->id);
        }

        if ($tax_calculation_method == PS_TAX_EXC) {
            $total_products = $summary['total_products'];
            $total_discounts = $summary['total_discounts_tax_exc'];
            $total_wrapping = $summary['total_wrapping_tax_exc'];
            $total_price = $summary['total_price_without_tax'];
            $total_shipping = $summary['total_shipping_tax_exc'];
        } else {
            $total_products = $summary['total_products_wt'];
            $total_discounts = $summary['total_discounts'];
            $total_wrapping = $summary['total_wrapping'];
            $total_price = $summary['total_price'];
            $total_shipping = $summary['total_shipping'];
        }

        foreach ($products as &$product) {
            if ($tax_calculation_method == PS_TAX_EXC) {
                $product['product_price'] = $product['price'];
                $product['product_total'] = $product['total'];
            } else {
                $product['product_price'] = $product['price_wt'];
                $product['product_total'] = $product['total_wt'];
            }

            $image = [];

            if (isset($product['id_product_attribute']) && (int) $product['id_product_attribute']) {
                $image = Db::getInstance()->getRow('
                    SELECT id_image
                    FROM ' . _DB_PREFIX_ . 'product_attribute_image
                    WHERE id_product_attribute = ' . (int) $product['id_product_attribute']
                );
            }

            if (!isset($image['id_image'])) {
                $image = Db::getInstance()->getRow('
                    SELECT id_image 
                    FROM ' . _DB_PREFIX_ . 'image 
                    WHERE id_product = ' . (int) $product['id_product'] . ' AND cover = 1'
                );
            }

            $product['qty_in_stock'] = StockAvailable::getQuantityAvailableByProduct(
                $product['id_product'],
                isset($product['id_product_attribute']) ? $product['id_product_attribute'] : null,
                (int) $id_shop
            );

            $image_product = new Image($image['id_image']);
            $product['image'] = (isset($image['id_image']) ? ImageManager::thumbnail(_PS_IMG_DIR_ . 'p/' . $image_product->getExistingImgPath() . '.jpg', 'product_mini_' . (int) $product['id_product'] . (isset($product['id_product_attribute']) ? '_' . (int) $product['id_product_attribute'] : '') . '.jpg', 45, 'jpg') : '--');

            $customized_datas = Product::getAllCustomizedDatas(
                $context->cart->id,
                null,
                true,
                null,
                (int) $product['id_customization']
            );
            $context->cart->setProductCustomizedDatas($product, $customized_datas);

            if ($customized_datas) {
                Product::addProductCustomizationPrice($product, $customized_datas);
            }
        }

        $customerStats = $customer->getStats();
        $gender = new Gender($customer->id_gender, $context->language->id);

        $products = $this->prepareProductForView($products, $currency);

        return new CartView(
            $cart->id,
            $cart->id_currency,
            [
                'id' => $customer->id,
                'first_name' => $customer->firstname,
                'last_name' => $customer->lastname,
                'gender' => $gender->name,
                'email' => $customer->email,
                'registration_date' => $customer->date_add,
                'valid_orders_count' => $customerStats['nb_orders'],
                'total_spent_since_registration' => $customerStats['total_orders'],
            ],
            [
                'id' => $order->id,
                'placed_date' => $order->date_add,
            ],
            [
                'products' => $products,
                'total_products' => $total_products,
                'total_products_formatted' => Tools::displayPrice($total_products, $currency),
                'total_discounts' => $total_discounts,
                'total_discounts_formatted' => Tools::displayPrice($total_discounts, $currency),
                'total_wrapping' => $total_wrapping,
                'total_wrapping_formatted' => Tools::displayPrice($total_wrapping, $currency),
                'total_shipping' => $total_shipping,
                'total_shipping_formatted' => Tools::displayPrice($total_shipping, $currency),
                'total' => $total_price,
                'total_formatted' => Tools::displayPrice($total_price, $currency),
                'is_tax_included' => $tax_calculation_method,
            ]
        );
    }

    /**
     * @param array $products
     * @param Currency $currency
     *
     * @return array
     */
    private function prepareProductForView(array $products, Currency $currency)
    {
        $formattedProducts = [];

        foreach ($products as $product) {
            $formattedProduct = [
                'id' => $product['id_product'],
                'name' => $product['name'],
                'attributes' => isset($product['attributes']) ? $product['attributes'] : '',
                'reference' => $product['name'],
                'supplier_reference' => $product['supplier_reference'],
                'stock_quantity' => $product['qty_in_stock'],
                'customization_quantity' => $product['customizationQuantityTotal'],
                'cart_quantity' => $product['cart_quantity'],
                'total_price' => $product['product_total'],
                'unit_price' => $product['product_price'],
                'total_price_formatted' => Tools::displayPrice(
                    $product['product_total'],
                    $currency
                ),
                'unit_price_formatted' => Tools::displayPrice(
                    $product['product_price'],
                    $currency
                ),
            ];

            $productCustomization = [];

            if ($product['customizedDatas']) {
                $formattedProduct['unit_price'] = $product['price_wt'];
                $formattedProduct['unit_price_formatted'] = Tools::displayPrice(
                    $product['price_wt'],
                    $currency
                );
                $formattedProduct['total_price'] = $product['total_customization_wt'];
                $formattedProduct['total_price_formatted'] = Tools::displayPrice(
                    $product['total_customization_wt'],
                    $currency
                );
                $formattedProduct['quantity'] = $product['customizationQuantityTotal'];

                foreach ($product['customizedDatas'] as $customizationPerAddress) {
                    foreach ($customizationPerAddress as $customization) {
                        if (((int) $customization['id_customization'] !== (int) $product['id_customization']) &&
                            count($customizationPerAddress) === 1
                        ) {
                            continue;
                        }

                        $productCustomization = [
                            'quantity' => $customization['quantity'],
                            'fields' => [],
                        ];

                        foreach ($customization['datas'] as $type => $data) {
                            if (Product::CUSTOMIZE_FILE === $type) {
                                foreach ($data as $item) {
                                    $productCustomization['fields'][] = [
                                        'name' => $item['name'],
                                        'value' => $item['value'],
                                        'type' => 'customizable_file',
                                    ];
                                }
                            } elseif (Product::CUSTOMIZE_TEXTFIELD === $type) {
                                foreach ($data as $item) {
                                    $productCustomization['fields'][] = [
                                        'name' => $item['name'],
                                        'value' => $item['value'],
                                        'type' => 'customizable_text_field',
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            $formattedProduct['customization'] = $productCustomization;

            $formattedProducts[] = $formattedProduct;
        }

        return $formattedProducts;
    }
}
