<?php

namespace PrestaShop\PrestaShop\Adapter\Cart;

use PrestaShop\PrestaShop\Core\Foundation\Templating\PresenterInterface;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Translator;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Context;
use Cart;
use Product;
use Configuration;
use CartRule;
use Tools;

class CartPresenter implements PresenterInterface
{
    private $priceFormatter;
    private $link;
    private $translator;
    private $imageRetriever;

    public function __construct()
    {
        $this->priceFormatter = new PriceFormatter();
        $this->link = Context::getContext()->link;
        $this->translator = new Translator(new LegacyContext());
        $this->imageRetriever = new ImageRetriever($this->link);
    }

    private function includeTaxes()
    {
        return !Product::getTaxCalculationMethod(Context::getContext()->cookie->id_customer);
    }

    private function presentProduct(array $rawProduct)
    {
        $presenter = new ProductListingPresenter(
            $this->imageRetriever,
            $this->link,
            $this->priceFormatter,
            new ProductColorsRetriever(),
            $this->translator
        );

        $settings = new ProductPresentationSettings();

        $settings->catalog_mode = Configuration::get('PS_CATALOG_MODE');
        $settings->include_taxes = $this->includeTaxes();
        $settings->allow_add_variant_to_cart_from_listing = (int) Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY');
        $settings->stock_management_enabled = Configuration::get('PS_STOCK_MANAGEMENT');

        if (isset($rawProduct['attributes']) && is_string($rawProduct['attributes'])) {
            // return an array of attributes
            $rawProduct['attributes'] = explode(',', $rawProduct['attributes']);
            $attributesArray = array();

            foreach ($rawProduct['attributes'] as $attribute) {
                list($key, $value) = explode(':', $attribute);
                $attributesArray[trim($key)] = ltrim($value);
            }

            $rawProduct['attributes'] = $attributesArray;
        }
        $rawProduct['remove_from_cart_url'] = $this->link->getRemoveFromCartURL(
            $rawProduct['id_product'],
            $rawProduct['id_product_attribute']
        );

        $rawProduct['up_quantity_url'] = $this->link->getUpQuantityCartURL(
            $rawProduct['id_product'],
            $rawProduct['id_product_attribute']
        );

        $rawProduct['down_quantity_url'] = $this->link->getDownQuantityCartURL(
            $rawProduct['id_product'],
            $rawProduct['id_product_attribute']
        );

        $rawProduct['ecotax_rate'] = '';
        $rawProduct['specific_prices'] = '';
        $rawProduct['customizable'] = '';
        $rawProduct['online_only'] = '';
        $rawProduct['reduction'] = '';
        $rawProduct['new'] = '';
        $rawProduct['condition'] = '';
        $rawProduct['pack'] = '';

        if ($this->includeTaxes()) {
            $rawProduct['price_amount'] = $rawProduct['price_wt'];
            $rawProduct['price'] = $this->priceFormatter->format($rawProduct['price_wt']);
        } else {
            $rawProduct['price_amount'] = $rawProduct['price'];
            $rawProduct['price'] = $rawProduct['price_tax_exc'] = $this->priceFormatter->format($rawProduct['price']);
        }

        if ($rawProduct['price_amount'] && $rawProduct['unit_price_ratio'] > 0) {
            $rawProduct['unit_price'] = $rawProduct['price_amount'] / $rawProduct['unit_price_ratio'];
        }

        $rawProduct['total'] = $this->priceFormatter->format(
            $this->includeTaxes() ?
            $rawProduct['total_wt'] :
            $rawProduct['total']
        );

        $rawProduct['quantity_wanted'] = $rawProduct['cart_quantity'];

        return $presenter->present(
            $settings,
            $rawProduct,
            Context::getContext()->language
        );
    }

    public function addCustomizedData(array $products, Cart $cart)
    {
        $data = Product::getAllCustomizedDatas($cart->id);

        if (!$data) {
            $data = array();
        }

        return array_map(function (array $product) use ($data) {

            $product['customizations'] = array();

            $id_product = (int) $product['id_product'];
            $id_product_attribute = (int) $product['id_product_attribute'];
            if (array_key_exists($id_product, $data)) {
                if (array_key_exists($id_product_attribute, $data[$id_product])) {
                    foreach ($data[$id_product] as $byAddress) {
                        foreach ($byAddress as $customizations) {
                            foreach ($customizations as $customization) {
                                $presentedCustomization = array(
                                    'quantity' => $customization['quantity'],
                                    'fields' => array(),
                                    'id_customization' => null,
                                );
                                $product['up_quantity_url'] = array();
                                $product['down_quantity_url'] = array();
                                foreach ($customization['datas'] as $byType) {
                                    $field = array();
                                    foreach ($byType as $data) {
                                        switch ($data['type']) {
                                            case Product::CUSTOMIZE_FILE:
                                                $field['type'] = 'image';
                                                $field['image'] = $this->imageRetriever->getCustomizationImage(
                                                    $data['value']
                                                );
                                                break;
                                            case Product::CUSTOMIZE_TEXTFIELD:
                                                $field['type'] = 'text';
                                                $field['text'] = $data['value'];
                                                break;
                                            default:
                                                $field['type'] = null;
                                        }
                                        $field['label'] = $data['name'];
                                        $presentedCustomization['id_customization'] = $data['id_customization'];
                                    }
                                    $presentedCustomization['fields'][] = $field;
                                }

                                $presentedCustomization['remove_from_cart_url'] = $this->link->getRemoveFromCartURL(
                                    $product['id_product'],
                                    $product['id_product_attribute'],
                                    $presentedCustomization['id_customization']
                                );

                                $presentedCustomization['up_quantity_url'] = $this->link->getUpQuantityCartURL(
                                    $product['id_product'],
                                    $product['id_product_attribute'],
                                    $presentedCustomization['id_customization']
                                );

                                $presentedCustomization['down_quantity_url'] = $this->link->getDownQuantityCartURL(
                                    $product['id_product'],
                                    $product['id_product_attribute'],
                                    $presentedCustomization['id_customization']
                                );

                                $product['customizations'][] = $presentedCustomization;
                            }
                        }
                    }
                }
            }

            usort($product['customizations'], function (array $a, array $b) {
                if (
                    $a['quantity'] > $b['quantity']
                    || count($a['fields']) > count($b['fields'])
                    || $a['id_customization'] > $b['id_customization']
                ) {
                    return -1;
                } else {
                    return 1;
                }
            });

            return $product;
        }, $products);
    }

    public function present($cart)
    {
        if (!is_a($cart, 'Cart')) {
            throw new \Exception('CartPresenter can only present instance of Cart');
        }
        $rawProducts = $cart->getProducts(true);

        $products = array_map(array($this, 'presentProduct'), $rawProducts);
        $products = $this->addCustomizedData($products, $cart);
        $subtotals = array();

        $productsTotalExcludingTax = $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        $total_excluding_tax = $cart->getOrderTotal(false);
        $total_including_tax = $cart->getOrderTotal(true);
        $total_discount = $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);

        $subtotals['products'] = [
            'type' => 'products',
            'label' => $this->translator->trans('Products', array(), 'Cart'),
            'amount' => $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS),
            'value' =>  $this->priceFormatter->format(($cart->getOrderTotal(true, Cart::ONLY_PRODUCTS)))
        ];

        if ($cart->gift) {
            $giftWrappingPrice = ($cart->getGiftWrappingPrice($this->includeTaxes()) != 0)
                ? $cart->getGiftWrappingPrice($this->includeTaxes())
                : 0;

            $subtotals['gift_wrapping'] = array(
                'type' => 'gift_wrapping',
                'label' => $this->translator->trans('Gift wrapping', array(), 'Cart'),
                'amount' => $giftWrappingPrice,
                'value' => ($giftWrappingPrice > 0)
                    ? $this->priceFormatter->format($giftWrappingPrice)
                    : $this->translator->trans('Free', array(), 'Cart'),
            ];
        }

        $subtotals['discounts'] = [
            'type' => 'discount',
            'label' => $this->translator->trans('Discount', [], 'Cart'),
            'amount' => $total_discount,
            'value' => $this->priceFormatter->format($total_discount),
        ];

        $shipping_cost = $cart->getTotalShippingCost(null, $this->includeTaxes());
        $subtotals['shipping'] = array(
            'type' => 'shipping',
            'label' => $this->translator->trans('Shipping', array(), 'Cart'),
            'amount' => $shipping_cost,
            'value' => $shipping_cost != 0
                ? $this->priceFormatter->format($shipping_cost)
                : $this->translator->trans('Free', array(), 'Cart'),
        );

        if (Configuration::get('PS_TAX_DISPLAY')) {
            $taxAmount = $total_including_tax - $total_excluding_tax;
            $subtotals['tax'] = [
                'type' => 'tax',
                'label' => $this->translator->trans('Tax', [], 'Cart'),
                'amount' => $taxAmount,
                'value' => $this->priceFormatter->format($taxAmount),
            ];
        }

        $total = array(
            'type' => 'total',
            'label' => $this->translator->trans('Total', array(), 'Cart'),
            'amount' => $this->includeTaxes() ? $total_including_tax : $total_excluding_tax,
            'value' => $this->priceFormatter->format($this->includeTaxes() ? $total_including_tax : $total_excluding_tax),
        );

        $products_count = array_reduce($products, function ($count, $product) {
            return $count + $product['quantity'];
        }, 0);

        $summary_string = $products_count === 1 ?
            $this->translator->trans('1 item', array(), 'Cart') :
            sprintf($this->translator->trans('%d items', array(), 'Cart'), $products_count)
        ;

        $minimalPurchase = $this->priceFormatter->convertAmount((float) Configuration::get('PS_PURCHASE_MINIMUM'));

        // TODO: move it to a common parent, since it's copied in OrderPresenter
        $labels = array(
            'tax_short' => ($this->includeTaxes())
                ? $this->translator->trans('(tax incl.)', array(), 'Tax')
                : $this->translator->trans('(tax excl.)', array(), 'Tax'),
            'tax_long' => ($this->includeTaxes())
                ? $this->translator->trans('(tax included)', array(), 'Tax')
                : $this->translator->trans('(tax excluded)', array(), 'Tax'),
        );

        return array(
            'products' => $products,
            'total' => $total,
            'subtotals' => $subtotals,
            'products_count' => $products_count,
            'summary_string' => $summary_string,
            'labels' => $labels,
            'id_address_delivery' => $cart->id_address_delivery,
            'id_address_invoice' => $cart->id_address_invoice,
            'vouchers' => $this->getTemplateVarVouchers($cart),
            'minimalPurchaseRequired' => ($this->priceFormatter->convertAmount($productsTotalExcludingTax) < $minimalPurchase) ?
                sprintf(
                    $this->translator->trans(
                        'A minimum shopping cart total of %s (tax excl.) is required to validate your order.
                        Current cart total is %s (tax excl.).',
                        array(),
                        'Cart'
                    ),
                    $this->priceFormatter->convertAndFormat($minimalPurchase),
                    $this->priceFormatter->convertAndFormat($productsTotalExcludingTax)
                ) :
                '',
        );
    }

    private function getTemplateVarVouchers(Cart $cart)
    {
        $cartVouchers = $cart->getCartRules();
        $vouchers = array();

        foreach ($cartVouchers as $cartVoucher) {
            $vouchers[$cartVoucher['id_cart_rule']]['id_cart_rule'] = $cartVoucher['id_cart_rule'];
            $vouchers[$cartVoucher['id_cart_rule']]['name'] = $cartVoucher['name'];
            $vouchers[$cartVoucher['id_cart_rule']]['reduction_percent'] = $cartVoucher['reduction_percent'];
            $vouchers[$cartVoucher['id_cart_rule']]['reduction_currency'] = $cartVoucher['reduction_currency'];
            $vouchers[$cartVoucher['id_cart_rule']]['reduction_amount'] = $cartVoucher['reduction_amount'];

            if (isset($cartVoucher['reduction_percent']) && $cartVoucher['reduction_percent'] == '0.00') {
                $cartVoucher['reduction_formated'] = $cartVoucher['reduction_percent'].'%';
            } elseif (isset($cartVoucher['reduction_amount'])) {
                $cartVoucher['reduction_formated'] = $this->priceFormatter->format($cartVoucher['reduction_amount']);
            }

            $vouchers[$cartVoucher['id_cart_rule']]['reduction_formatted'] = '-'.$cartVoucher['reduction_formatted'];
            $vouchers[$cartVoucher['id_cart_rule']]['delete_url'] = $this->link->getPageLink(
                'cart', true, null, array(
                    'deleteDiscount' => $cartVoucher['id_cart_rule'],
                    'token' => Tools::getToken(false),
                )
            );
        }

        return array(
            'allowed' => (int) CartRule::isFeatureActive(),
            'added' => $vouchers,
        );
    }
}
