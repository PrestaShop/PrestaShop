<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter\Product;

use Hook;
use Language;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use ReflectionException;

class ProductListingPresenter extends ProductPresenter
{
    /**
     * @param ProductPresentationSettings $settings
     * @param array $product
     * @param Language $language
     *
     * @return ProductLazyArray|ProductListingLazyArray
     *
     * @throws ReflectionException
     */
    public function present(
        ProductPresentationSettings $settings,
        array $product,
        Language $language
    ) {
        $productListingLazyArray = new ProductListingLazyArray(
            $settings,
            $product,
            $language,
            $this->imageRetriever,
            $this->link,
            $this->priceFormatter,
            $this->productColorsRetriever,
            $this->translator
        );

        Hook::exec('actionPresentProductListing',
            ['presentedProduct' => &$productListingLazyArray]
        );

        return $productListingLazyArray;
    }
}
