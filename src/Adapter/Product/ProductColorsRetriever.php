<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Product;

use Product;

/**
 * Retrieve colors of a Product, if any.
 */
class ProductColorsRetriever
{
    /**
     * @param int $id_product
     *
     * @return mixed|null
     */
    public function getColoredVariants($id_product)
    {
        $attributesColorList = Product::getAttributesColorList([$id_product]);

        return (is_array($attributesColorList)) ? current($attributesColorList) : null;
    }
}
