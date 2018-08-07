<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShop\PrestaShop\Adapter\Presenter\Cart;

use PrestaShop\PrestaShop\Adapter\Presenter\PresenterInterface;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use Context;
use Cart;
use Product;
use Configuration;
use Symfony\Component\Translation\TranslatorInterface;
use TaxConfiguration;
use CartRule;
use Tools;
use Hook;

class CartPresenter implements PresenterInterface
{
    /**
     * @var PriceFormatter
     */
    private $priceFormatter;

    /**
     * @var \Link
     */
    private $link;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ImageRetriever
     */
    private $imageRetriever;

    /**
     * @var TaxConfiguration
     */
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

    /**
     * @return bool
     */
    private function includeTaxes()
    {
        return $this->taxConfiguration->includeTaxes();
    }

    /**
     * @param array $rawProduct
     * @return \PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray|\PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingLazyArray
     */
    private function presentProduct(array $rawProduct)
    {
        $settings = new ProductPresentationSettings();

        $settings->catalog_mode = Configuration::isCatalogMode();
        $settings->include_taxes = $this->includeTaxes();
        $settings->allow_add_variant_to_cart_from_listing = (int) Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY');
        $settings->stock_management_enabled = Configuration::get('PS_STOCK_MANAGEMENT');
        $settings->showPrices = Configuration::showPrices();

        if (isset($rawProduct['attributes']) && is_string($rawProduct['attributes'])) {
            $rawProduct['attributes'] = $this->getAttributesArrayFromString($rawProduct['attributes']);
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

        $resetFields = array(
            'ecotax_rate',
            'specific_prices',
            'customizable',
            'online_only',
            'reduction',
            'new',
            'condition',
            'pack',
        );
        foreach ($resetFields as $field) {
            if (!array_key_exists($field, $rawProduct)) {
                $rawProduct[$field] = '';
            }
        }

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

        $presenter = new ProductListingPresenter(
            $this->imageRetriever,
            $this->link,
            $this->priceFormatter,
            new ProductColorsRetriever(),
            $this->translator
        );

        return $presenter->present(
            $settings,
            $rawProduct,
            Context::getContext()->language
        );
    }

    /**
     * @param array $products
     * @param Cart  $cart
     *
     * @return array
     */
    public function addCustomizedData(array $products, Cart $cart)
    {
        return array_map(function ($product) use ($cart) {
            $customizations = array();

            $data = Product::getAllCustomizedDatas($cart->id, null, true, null, (int) $product['id_customization']);

            if (!$data) {
                $data = array();
            }
            $id_product = (int) $product['id_product'];
            $id_product_attribute = (int) $product['id_product_attribute'];
            if (array_key_exists($id_product, $data)) {
                if (array_key_exists($id_product_attribute, $data[$id_product])) {
                    foreach ($data[$id_product] as $byAddress) {
                        foreach ($byAddress as $byAddressCustomizations) {
                            foreach ($byAddressCustomizations as $customization) {
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
                                $product['update_quantity_url'] = $this->link->getUpdateQuantityCartURL(
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

                                $presentedCustomization['update_quantity_url'] = $product['update_quantity_url'];

                                $customizations[] = $presentedCustomization;
                            }
                        }
                    }
                }
            }

            usort($customizations, function (array $a, array $b) {
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

            $product['customizations'] = $customizations;

            return $product;
        }, $products);
    }

    /**
     * @param Cart $cart
     * @param bool $shouldSeparateGifts
     * @return array
     * @throws \Exception
     */
    public function present($cart, $shouldSeparateGifts = false)
    {
        if (!is_a($cart, 'Cart')) {
            throw new \Exception('CartPresenter can only present instance of Cart');
        }

        if ($shouldSeparateGifts) {
            $rawProducts = $cart->getProductsWithSeparatedGifts();
        } else {
            $rawProducts = $cart->getProducts(true);
        }

        $products = array_map(array($this, 'presentProduct'), $rawProducts);
        $products = $this->addCustomizedData($products, $cart);
        $subtotals = array();

        $productsTotalExcludingTax = $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        $total_excluding_tax = $cart->getOrderTotal(false);
        $total_including_tax = $cart->getOrderTotal(true);
        $total_discount = $cart->getDiscountSubtotalWithoutGifts();
        $totalCartAmount = $cart->getOrderTotal($this->includeTaxes(), Cart::ONLY_PRODUCTS);

        $subtotals['products'] = array(
            'type' => 'products',
            'label' => $this->translator->trans('Subtotal', array(), 'Shop.Theme.Checkout'),
            'amount' => $totalCartAmount,
            'value' => $this->priceFormatter->format($totalCartAmount),
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
                    ? $this->priceFormatter->convertAndFormat($giftWrappingPrice)
                    : $this->translator->trans('Free', array(), 'Shop.Theme.Checkout'),
            );
        }

        if (!$cart->isVirtualCart()) {
            $shippingCost = $cart->getTotalShippingCost(null, $this->includeTaxes());
        } else {
            $shippingCost = 0;
        }
        $subtotals['shipping'] = array(
            'type' => 'shipping',
            'label' => $this->translator->trans('Shipping', array(), 'Shop.Theme.Checkout'),
            'amount' => $shippingCost,
            'value' => $shippingCost != 0
                ? $this->priceFormatter->format($shippingCost)
                : $this->translator->trans('Free', array(), 'Shop.Theme.Checkout'),
        );

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
            'total_including_tax' => array(
                'type' => 'total',
                'label' => $this->translator->trans('Total (tax incl.)', array(), 'Shop.Theme.Checkout'),
                'amount' => $total_including_tax,
                'value' => $this->priceFormatter->format($total_including_tax),
            ),
            'total_excluding_tax' => array(
                'type' => 'total',
                'label' => $this->translator->trans('Total (tax excl.)', array(), 'Shop.Theme.Checkout'),
                'amount' => $total_excluding_tax,
                'value' => $this->priceFormatter->format($total_excluding_tax),
            ),
        );

        $products_count = array_reduce($products, function ($count, $product) {
            return $count + $product['quantity'];
        }, 0);

        $summary_string = $products_count === 1 ?
            $this->translator->trans('1 item', array(), 'Shop.Theme.Checkout') :
            $this->translator->trans('%count% items', array('%count%' => $products_count), 'Shop.Theme.Checkout')
        ;

        $minimalPurchase = $this->priceFormatter->convertAmount((float) Configuration::get('PS_PURCHASE_MINIMUM'));

        Hook::exec('overrideMinimalPurchasePrice', array(
            'minimalPurchase' => &$minimalPurchase
        ));

        // TODO: move it to a common parent, since it's copied in OrderPresenter and ProductPresenter
        $labels = array(
            'tax_short' => ($this->includeTaxes())
                ? $this->translator->trans('(tax incl.)', array(), 'Shop.Theme.Global')
                : $this->translator->trans('(tax excl.)', array(), 'Shop.Theme.Global'),
            'tax_long' => ($this->includeTaxes())
                ? $this->translator->trans('(tax included)', array(), 'Shop.Theme.Global')
                : $this->translator->trans('(tax excluded)', array(), 'Shop.Theme.Global'),
        );

        $discounts = $cart->getDiscounts();
        $vouchers = $this->getTemplateVarVouchers($cart);

        $cartRulesIds = array_flip(array_map(
            function ($voucher) {
                return $voucher['id_cart_rule'];
            },
            $vouchers['added']
        ));

        $discounts = array_filter($discounts, function ($discount) use ($cartRulesIds) {
            return !array_key_exists($discount['id_cart_rule'], $cartRulesIds);
        });

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
            'vouchers' => $vouchers,
            'discounts' => $discounts,
            'minimalPurchase' => $minimalPurchase,
            'minimalPurchaseRequired' => ($this->priceFormatter->convertAmount($productsTotalExcludingTax) < $minimalPurchase) ?
                $this->translator->trans(
                    'A minimum shopping cart total of %amount% (tax excl.) is required to validate your order. Current cart total is %total% (tax excl.).',
                    array(
                        '%amount%' => $this->priceFormatter->convertAndFormat($minimalPurchase),
                        '%total%' => $this->priceFormatter->convertAndFormat($productsTotalExcludingTax),
                    ),
                    'Shop.Theme.Checkout'
                ) :
                '',
        );
    }

    private function getTemplateVarVouchers(Cart $cart)
    {
        $cartVouchers = $cart->getCartRules();
        $vouchers = array();

        $cartHasTax = is_null($cart->id) ? false : $cart::getTaxesAverageUsed($cart);

        foreach ($cartVouchers as $cartVoucher) {
            $vouchers[$cartVoucher['id_cart_rule']]['id_cart_rule'] = $cartVoucher['id_cart_rule'];
            $vouchers[$cartVoucher['id_cart_rule']]['name'] = $cartVoucher['name'];
            $vouchers[$cartVoucher['id_cart_rule']]['reduction_percent'] = $cartVoucher['reduction_percent'];
            $vouchers[$cartVoucher['id_cart_rule']]['reduction_currency'] = $cartVoucher['reduction_currency'];

            // Voucher reduction depending of the cart tax rule
            // if $cartHasTax & voucher is tax excluded, set amount voucher to tax included
            if ($cartHasTax && $cartVoucher['reduction_tax'] == '0') {
                $cartVoucher['reduction_amount'] = $cartVoucher['reduction_amount'] * (1 + $cartHasTax / 100);
            }

            $vouchers[$cartVoucher['id_cart_rule']]['reduction_amount'] = $cartVoucher['reduction_amount'];

            if (array_key_exists('gift_product', $cartVoucher) && $cartVoucher['gift_product']) {
                $cartVoucher['reduction_amount'] = $cartVoucher['value_real'];
            }

            if (isset($cartVoucher['reduction_percent']) && $cartVoucher['reduction_amount'] == '0.00') {
                $cartVoucher['reduction_formatted'] = $cartVoucher['reduction_percent'].'%';
            } elseif (isset($cartVoucher['reduction_amount']) && $cartVoucher['reduction_amount'] > 0) {
                $cartVoucher['reduction_formatted'] = $this->priceFormatter->convertAndFormat($cartVoucher['reduction_amount']);
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

    /**
     * Receives a string containing a list of attributes affected to the product and returns them as an array
     *
     * @param string $attributes
     * @return array Converted attributes in an array
     */
    protected function getAttributesArrayFromString($attributes)
    {
        $separator = Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR');
        $pattern = '/(?>(?P<attribute>[^:]+:[^:]+)'.$separator.'+(?!'.$separator.'([^:'.$separator.'])+:))/';
        $attributesArray = array();
        $matches = array();
        if (!preg_match_all($pattern, $attributes.$separator, $matches)) {
            return $attributesArray;
        }

        foreach ($matches['attribute'] as $attribute) {
            list($key, $value) = explode(':', $attribute);
            $attributesArray[trim($key)] = ltrim($value);
        }

        return $attributesArray;
    }
}
