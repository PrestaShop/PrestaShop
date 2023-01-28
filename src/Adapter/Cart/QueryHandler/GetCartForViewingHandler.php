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

namespace PrestaShop\PrestaShop\Adapter\Cart\QueryHandler;

use Cart;
use Context;
use Currency;
use Customer;
use DateTime;
use Gender;
use Group;
use Order;
use PrestaShop\PrestaShop\Adapter\ImageManager;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartForViewing;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler\GetCartForViewingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartView;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Util\Sorter;
use Product;
use StockAvailable;
use Validate;

/**
 * @internal
 */
final class GetCartForViewingHandler implements GetCartForViewingHandlerInterface
{
    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * @var Locale
     */
    private $locale;

    /**
     * @param ImageManager $imageManager
     * @param Locale $locale
     */
    public function __construct(ImageManager $imageManager, Locale $locale)
    {
        $this->imageManager = $imageManager;
        $this->locale = $locale;
    }

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

        // Sort products by Reference ID (and if equals (like combination) by Supplier Reference)
        $sorter = new Sorter();
        $products = $sorter->natural($products, Sorter::ORDER_DESC, 'reference', 'supplier_reference');

        foreach ($products as &$product) {
            if ($tax_calculation_method == PS_TAX_EXC) {
                $product['product_price'] = $product['price'];
                $product['product_total'] = $product['total'];
            } else {
                $product['product_price'] = $product['price_wt'];
                $product['product_total'] = $product['total_wt'];
            }

            $product['qty_in_stock'] = StockAvailable::getQuantityAvailableByProduct(
                $product['id_product'],
                isset($product['id_product_attribute']) ? $product['id_product_attribute'] : null,
                (int) $id_shop
            );

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
        unset($product);

        $customerStats = $customer->getStats();
        $gender = new Gender($customer->id_gender, $context->language->id);

        $products = $this->prepareProductForView($products, $currency, $context->language->id);

        $customerInformation = [
            'id' => $customer->id,
            'first_name' => $customer->firstname,
            'last_name' => $customer->lastname,
            'gender' => $gender->name,
            'email' => $customer->email,
            'registration_date' => (new DateTime($customer->date_add))->format($context->language->date_format_lite),
            'valid_orders_count' => $customerStats['nb_orders'],
            'total_spent_since_registration' => $this->locale->formatPrice(
                $customerStats['total_orders'] ?: 0,
                $currency->iso_code
            ),
        ];

        $orderInformation = [
            'id' => $order->id,
            'placed_date' => (new DateTime($order->date_add))->format($context->language->date_format_lite),
        ];

        $cartSummary = [
            'products' => $products,
            'cart_rules' => $this->getCartRulesForView($cart),
            'total_products' => $total_products,
            'total_products_formatted' => $this->locale->formatPrice($total_products, $currency->iso_code),
            'total_discounts' => $total_discounts,
            'total_discounts_formatted' => $this->locale->formatPrice($total_discounts, $currency->iso_code),
            'total_wrapping' => $total_wrapping,
            'total_wrapping_formatted' => $this->locale->formatPrice($total_wrapping, $currency->iso_code),
            'total_shipping' => $total_shipping,
            'total_shipping_formatted' => $this->locale->formatPrice($total_shipping, $currency->iso_code),
            'total' => $total_price,
            'total_formatted' => $this->locale->formatPrice($total_price, $currency->iso_code),
            'is_tax_included' => $tax_calculation_method == PS_TAX_INC,
        ];

        return new CartView($cart->id, $cart->id_currency, $customerInformation, $orderInformation, $cartSummary);
    }

    /**
     * @param array $products
     * @param Currency $currency
     * @param int $languageId
     *
     * @return array
     */
    private function prepareProductForView(array $products, Currency $currency, int $languageId)
    {
        $formattedProducts = [];

        foreach ($products as $product) {
            if ($product['id_product_attribute']) {
                $image = Product::getCombinationImageById($product['id_product_attribute'], $languageId);
            } else {
                $image = Product::getCover($product['id_product']);
            }

            $formattedProduct = [
                'id' => $product['id_product'],
                'name' => $product['name'],
                'attributes' => isset($product['attributes']) ? $product['attributes'] : '',
                'reference' => $product['reference'],
                'supplier_reference' => $product['supplier_reference'],
                'stock_quantity' => $product['qty_in_stock'],
                'customization_quantity' => $product['customizationQuantityTotal'],
                'cart_quantity' => $product['cart_quantity'],
                'total_price' => $product['product_total'],
                'unit_price' => $product['product_price'],
                'total_price_formatted' => $this->locale->formatPrice($product['product_total'], $currency->iso_code),
                'unit_price_formatted' => $this->locale->formatPrice($product['product_price'], $currency->iso_code),
                // it is possible that there is no image for product, so we don't show anything, but at least avoid breaking whole page
                'image' => isset($image['id_image']) ? $this->imageManager->getThumbnailForListing($image['id_image']) : '',
            ];

            if (isset($product['customizationQuantityTotal'])) {
                $formattedProduct['cart_quantity'] =
                    $product['cart_quantity'] - $product['customizationQuantityTotal'];
            }

            $productCustomization = [];

            if ($product['customizedDatas']) {
                $formattedProduct['unit_price'] = $product['price_wt'];
                $formattedProduct['unit_price_formatted'] = $this->locale->formatPrice($product['price_wt'], $currency->iso_code);
                $formattedProduct['total_price'] = $product['total_customization_wt'];
                $formattedProduct['total_price_formatted'] = $this->locale->formatPrice(
                    $product['total_customization_wt'],
                    $currency->iso_code
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
                                        'image' => _THEME_PROD_PIC_DIR_ . $item['value'] . '_small',
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

    /**
     * @param Cart $cart
     *
     * @return array
     */
    private function getCartRulesForView(Cart $cart)
    {
        $cartRules = $cart->getCartRules();
        $cartRulesView = [];

        $cartCurrency = new Currency($cart->id_currency);

        foreach ($cartRules as $cartRule) {
            $cartRulesView[] = [
                'id' => $cartRule['id_cart_rule'],
                'name' => $cartRule['name'],
                'is_free_shipping' => !$cartRule['value_real'] && $cartRule['free_shipping'],
                'formatted_value' => $this->locale->formatPrice(
                    $cartRule['value_real'],
                    $cartCurrency->iso_code
                ),
            ];
        }

        return $cartRulesView;
    }
}
