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

namespace PrestaShop\PrestaShop\Adapter\Presenter\Product;

use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;

class ProductListingLazyArray extends ProductLazyArray
{
    /**
     * @arrayAccess
     *
     * @return string|null
     */
    public function getAddToCartUrl()
    {
        if ($this->product['id_product_attribute'] != 0 && !$this->settings->allow_add_variant_to_cart_from_listing) {
            return null;
        }

        if ($this->product['customizable'] == 2 || !empty($this->product['customization_required'])) {
            return null;
        }

        return parent::getAddToCartUrl();
    }

    /**
     * @param array $product
     * @param ProductPresentationSettings $settings
     *
     * @return bool
     */
    protected function shouldEnableAddToCartButton(array $product, ProductPresentationSettings $settings)
    {
        if (isset($product['attributes'])
            && count($product['attributes']) > 0
            && !$settings->allow_add_variant_to_cart_from_listing) {
            return false;
        }

        return parent::shouldEnableAddToCartButton($product, $settings);
    }
}
