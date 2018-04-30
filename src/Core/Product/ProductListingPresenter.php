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

namespace PrestaShop\PrestaShop\Core\Product;

/**
 * @deprecated since 1.7.4.0
 * @see \PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter
 *
 * Class ProductListingPresenter
 */
class ProductListingPresenter extends \PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter
{
    public function present(
        ProductPresentationSettings $settings,
        array $product,
        \Language $language
    ) {
        $presentedProduct = parent::present(
            $settings,
            $product,
            $language
        );

        if ($product['id_product_attribute'] != 0 && !$settings->allow_add_variant_to_cart_from_listing) {
            $presentedProduct['add_to_cart_url'] = null;
        }

        if ($product['customizable'] == 2 || !empty($product['customization_required'])) {
            $presentedProduct['add_to_cart_url'] = null;
        }

        if (!$this->shouldEnableAddToCartButton($product, $settings)) {
            $presentedProduct['add_to_cart_url'] = null;
        }

        return $presentedProduct;
    }

    protected function shouldEnableAddToCartButton(array $product, ProductPresentationSettings $settings)
    {
        if ($settings->stock_management_enabled && !$product['allow_oosp'] && $product['quantity'] <= 0) {
            return false;
        }

        if (isset($product['attributes']) && count($product['attributes']) > 0 && !$settings->allow_add_variant_to_cart_from_listing) {
            return false;
        }

        return parent::shouldEnableAddToCartButton($product, $settings);
    }
}
