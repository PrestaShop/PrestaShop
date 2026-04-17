<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
