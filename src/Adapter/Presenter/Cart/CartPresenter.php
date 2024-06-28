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

use Cache;
use Cart;
use Configuration;
use Context;
use Exception;
use Hook;
use Link;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\PresenterInterface;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use Product;
use ProductAssembler;
use TaxConfiguration;

class CartPresenter implements PresenterInterface
{
    /**
     * @var Link
     */
    private $link;

    /**
     * @var ImageRetriever
     */
    private $imageRetriever;

    /**
     * @var ProductPresentationSettings
     */
    protected $settings;

    /**
     * @var TaxConfiguration
     */
    private $taxConfiguration;

    /**
     * @var ProductAssembler
     */
    protected $productAssembler;

    public function __construct()
    {
        $this->link = Context::getContext()->link;
        $this->imageRetriever = new ImageRetriever($this->link);
        $this->taxConfiguration = new TaxConfiguration();
    }

    /**
     * @param array $products
     * @param Cart $cart
     *
     * @return array
     */
    public function addCustomizedData(array $products, Cart $cart)
    {
        return array_map(function ($product) use ($cart) {
            $customizations = [];

            $data = Product::getAllCustomizedDatas($cart->id, null, true, null, (int) $product['id_customization']);

            if (!$data) {
                $data = [];
            }
            $id_product = (int) $product['id_product'];
            $id_product_attribute = (int) $product['id_product_attribute'];
            if (array_key_exists($id_product, $data)) {
                if (array_key_exists($id_product_attribute, $data[$id_product])) {
                    foreach ($data[$id_product] as $byAddress) {
                        foreach ($byAddress as $byAddressCustomizations) {
                            foreach ($byAddressCustomizations as $customization) {
                                $presentedCustomization = [
                                    'quantity' => $customization['quantity'],
                                    'fields' => [],
                                    'id_customization' => null,
                                ];

                                foreach ($customization['datas'] as $byType) {
                                    foreach ($byType as $data) {
                                        $field = [];
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
     *
     * @throws Exception
     */
    public function present($cart, bool $shouldSeparateGifts = false): CartLazyArray
    {
        $cache_id = 'presentedCart_' . (int) $shouldSeparateGifts . $cart->id;
        if (Cache::isStored($cache_id)) {
            return Cache::retrieve($cache_id);
        }

        $cartLazyArray = new CartLazyArray($cart, $this, $shouldSeparateGifts);

        Hook::exec('actionPresentCart',
            ['presentedCart' => &$cartLazyArray]
        );

        Cache::store($cache_id, $cartLazyArray);

        return $cartLazyArray;
    }

    /**
     * Receives a string containing a list of attributes affected to the product and returns them as an array.
     *
     * @param string $attributes
     *
     * @return array Converted attributes in an array
     */
    public function getAttributesArrayFromString($attributes)
    {
        $separator = Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR');
        $pattern = '/(?>(?P<attribute>[^:]+:[^:]+)' . $separator . '+(?!' . $separator . '([^:' . $separator . '])+:))/';
        $attributesArray = [];
        $matches = [];
        if (!preg_match_all($pattern, $attributes . $separator, $matches)) {
            return $attributesArray;
        }

        foreach ($matches['attribute'] as $attribute) {
            [$key, $value] = explode(':', $attribute);
            $attributesArray[trim($key)] = ltrim($value);
        }

        return $attributesArray;
    }

    public function getSettings(): ProductPresentationSettings
    {
        if ($this->settings === null) {
            $this->settings = new ProductPresentationSettings();

            $this->settings->catalog_mode = Configuration::isCatalogMode();
            $this->settings->catalog_mode_with_prices = (int) Configuration::get('PS_CATALOG_MODE_WITH_PRICES');
            $this->settings->include_taxes = $this->includeTaxes();
            $this->settings->allow_add_variant_to_cart_from_listing = (int) Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY');
            $this->settings->stock_management_enabled = Configuration::get('PS_STOCK_MANAGEMENT');
            $this->settings->showPrices = Configuration::showPrices();
            $this->settings->showLabelOOSListingPages = (bool) Configuration::get('PS_SHOW_LABEL_OOS_LISTING_PAGES');
            $this->settings->lastRemainingItems = (int) Configuration::get('PS_LAST_QTIES');
        }

        return $this->settings;
    }

    public function getProductAssembler(): ProductAssembler
    {
        if ($this->productAssembler === null) {
            $this->productAssembler = new ProductAssembler(Context::getContext());
        }

        return $this->productAssembler;
    }

    public function includeTaxes(): bool
    {
        return $this->taxConfiguration->includeTaxes();
    }
}
