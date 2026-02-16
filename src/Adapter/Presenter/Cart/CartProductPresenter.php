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

use Hook;
use Language;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductPresenter;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use ReflectionException;

class CartProductPresenter extends ProductPresenter
{
    /**
     * @param ProductPresentationSettings $settings
     * @param array $product
     * @param Language $language
     *
     * @return CartProductLazyArray
     *
     * @throws ReflectionException
     */
    public function present(
        ProductPresentationSettings $settings,
        array $product,
        Language $language
    ) {
        $cartProductLazyArray = new CartProductLazyArray(
            $settings,
            $product,
            $language,
            $this->imageRetriever,
            $this->link,
            $this->priceFormatter,
            $this->productColorsRetriever,
            $this->translator
        );

        /*
         * @deprecated since 9.1.0 - please use actionPresentCartProduct instead. This hook is here so
         * that modules using the old hook do not break.
         */
        Hook::exec('actionPresentProductListing',
            ['presentedProduct' => &$cartProductLazyArray]
        );
        Hook::exec('actionPresentCartProduct',
            ['presentedProduct' => &$cartProductLazyArray]
        );

        return $cartProductLazyArray;
    }
}
