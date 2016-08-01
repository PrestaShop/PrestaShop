<?php

namespace PrestaShop\PrestaShop\Adapter\Cart;

use PrestaShop\PrestaShop\Core\Foundation\Templating\PresenterInterface;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use Context;
use Cart;
use Product;
use Configuration;
use TaxConfiguration;
use CartRule;
use Tools;

class CartPresenter implements PresenterInterface
{
    private $priceFormatter;
    private $link;
    private $translator;
    private $imageRetriever;
    private $taxConfiguration;

    public function __construct()
    {
        $context = Context::getContext();
        $this->priceFormatter = new PriceFormatter();
        $this->link = $context->link;
        $this->translator = $context->getTranslator();
        $this->imageRetriever = new ImageRetriever($this->link);
        $this->taxConfiguration = new TaxConfiguration();
    }

    private function includeTaxes()
    {
        return $this->taxConfiguration->includeTaxes();
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

        $rawProduct['update_quantity_url'] = $this->link->getUpdateQuantityCartURL(
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
        return array_map(function (array $product) use ($cart) {

            $product['customizations'] = array();

            $data = Product::getAllCustomizedDatas($cart->id, null, true, null, (int) $product['id_customization']);

            if (!$data) {
                $data = array();
            }
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

                                foreach ($customization['datas'] as $byType) {
                                    foreach ($byType as $data) {
                                        $field = array();
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
                                        $field['id_module'] = $data['id_module'];
                                        $presentedCustomization['id_customization'] = $data['id_customization'];
                                        $presentedCustomization['fields'][] = $field;
                                    }
                                }

                                $product['up_quantity_url'] = $this->link->getUpQuantityCartURL(
                                    $product['id_product'],
                                    $product['id_product_attribute'],
                                    $presentedCustomization['id_customization']
                                );
                                $product['down_quantity_url'] = $this->link->getDownQuantityCartURL(
                                    $product['id_product'],
                                    $product['id_product_attribute'],
                                    $presentedCustomization['id_customization']
                                );
                                $product['remove_from_cart_url'] = $this->link->getRemoveFromCartURL(
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

                                $presentedCustomization['remove_from_cart_url'] = $this->link->getRemoveFromCartURL(
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

        $subtotals['products'] = array(
            'type' => 'products',
            'label' => $this->translator->trans('Subtotal', array(), 'Shop.Theme.Checkout'),
            'amount' => $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS),
            'value' => $this->priceFormatter->format(($cart->getOrderTotal(true, Cart::ONLY_PRODUCTS))),
        );

        if ($total_discount) {
            $subtotals['discounts'] = array(
                'type' => 'discount',
                'label' => $this->translator->trans('Discount', array(), 'Shop.Theme.Checkout'),
                'amount' => $total_discount,
                'value' => $this->priceFormatter->format($total_discount),
            );
        } else {
            $subtotals['discounts'] = null;
        }

        if ($cart->gift) {
            $giftWrappingPrice = ($cart->getGiftWrappingPrice($this->includeTaxes()) != 0)
                ? $cart->getGiftWrappingPrice($this->includeTaxes())
                : 0;

            $subtotals['gift_wrapping'] = array(
                'type' => 'gift_wrapping',
                'label' => $this->translator->trans('Gift wrapping', array(), 'Shop.Theme.Checkout'),
                'amount' => $giftWrappingPrice,
                'value' => ($giftWrappingPrice > 0)
                    ? $this->priceFormatter->format($giftWrappingPrice)
                    : $this->translator->trans('Free', array(), 'Shop.Theme.Checkout'),
            );
        }

        if (!$cart->isVirtualCart()) {
            $shippingCost = $cart->getTotalShippingCost(null, $this->includeTaxes());
            $subtotals['shipping'] = array(
                'type' => 'shipping',
                'label' => $this->translator->trans('Shipping', array(), 'Shop.Theme.Checkout'),
                'amount' => $shippingCost,
                'value' => $shippingCost != 0
                    ? $this->priceFormatter->format($shippingCost)
                    : $this->translator->trans('Free', array(), 'Shop.Theme.Checkout'),
            );
        }

        $subtotals['tax'] = null;
        if (Configuration::get('PS_TAX_DISPLAY')) {
            $taxAmount = $total_including_tax - $total_excluding_tax;
            $subtotals['tax'] = array(
                'type' => 'tax',
                'label' => ($this->includeTaxes())
                    ? $this->translator->trans('Included taxes', array(), 'Shop.Theme.Checkout')
                    : $this->translator->trans('Taxes', array(), 'Shop.Theme.Checkout'),
                'amount' => $taxAmount,
                'value' => $this->priceFormatter->format($taxAmount),
            );
        }

        $totals = array(
            'total' => array(
                'type' => 'total',
                'label' => $this->translator->trans('Total', array(), 'Shop.Theme.Checkout'),
                'amount' => $this->includeTaxes() ? $total_including_tax : $total_excluding_tax,
                'value' => $this->priceFormatter->format(
                    $this->includeTaxes() ? $total_including_tax : $total_excluding_tax
                ),
            ),
        );

        $products_count = array_reduce($products, function ($count, $product) {
            return $count + $product['quantity'];
        }, 0);

        $summary_string = $products_count === 1 ?
            $this->translator->trans('1 item', array(), 'Shop.Theme.Checkout') :
            sprintf($this->translator->trans('%d items', array(), 'Shop.Theme.Checkout'), $products_count)
        ;

        $minimalPurchase = $this->priceFormatter->convertAmount((float) Configuration::get('PS_PURCHASE_MINIMUM'));

        // TODO: move it to a common parent, since it's copied in OrderPresenter and ProductPresenter
        $labels = array(
            'tax_short' => ($this->includeTaxes())
                ? $this->translator->trans('(tax incl.)', array(), 'Shop.Theme')
                : $this->translator->trans('(tax excl.)', array(), 'Shop.Theme'),
            'tax_long' => ($this->includeTaxes())
                ? $this->translator->trans('(tax included)', array(), 'Shop.Theme')
                : $this->translator->trans('(tax excluded)', array(), 'Shop.Theme'),
        );

        return array(
            'products' => $products,
            'totals' => $totals,
            'subtotals' => $subtotals,
            'products_count' => $products_count,
            'summary_string' => $summary_string,
            'labels' => $labels,
            'id_address_delivery' => $cart->id_address_delivery,
            'id_address_invoice' => $cart->id_address_invoice,
            'is_virtual' => $cart->isVirtualCart(),
            'vouchers' => $this->getTemplateVarVouchers($cart),
            'minimalPurchaseRequired' => ($this->priceFormatter->convertAmount($productsTotalExcludingTax) < $minimalPurchase) ?
                sprintf(
                    $this->translator->trans(
                        'A minimum shopping cart total of %amount% (tax excl.) is required to validate your order. Current cart total is %total% (tax excl.).',
                        array(
                            '%amount%' => $this->priceFormatter->convertAndFormat($minimalPurchase),
                            '%total%' => $this->priceFormatter->convertAndFormat($productsTotalExcludingTax),
                        ),
                        'Shop.Theme.Checkout'
                    )
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

            if (isset($cartVoucher['reduction_percent']) && $cartVoucher['reduction_amount'] == '0.00') {
                $cartVoucher['reduction_formatted'] = $cartVoucher['reduction_percent'].'%';
            } elseif (isset($cartVoucher['reduction_amount']) && $cartVoucher['reduction_amount'] > 0) {
                $cartVoucher['reduction_formatted'] = $this->priceFormatter->format($cartVoucher['reduction_amount']);
            }

            $vouchers[$cartVoucher['id_cart_rule']]['reduction_formatted'] = '-'.$cartVoucher['reduction_formatted'];
            $vouchers[$cartVoucher['id_cart_rule']]['delete_url'] = $this->link->getPageLink(
                'cart',
                true,
                null,
                array(
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
