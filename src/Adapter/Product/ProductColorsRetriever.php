<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Product;

use Context;
use Product;

/**
 * Retrieve colors of a Product, if any.
 */
class ProductColorsRetriever
{
    /**
     * Request-scoped cache of colored variants, keyed by "idProduct-idLang-idShop".
     *
     * @var array<string, mixed>
     */
    private static $prefetchedColoredVariants = [];

    /**
     * @param int $id_product
     *
     * @return mixed|null
     */
    public function getColoredVariants($id_product)
    {
        $cacheKey = $this->cacheKey((int) $id_product);
        if (array_key_exists($cacheKey, self::$prefetchedColoredVariants)) {
            return self::$prefetchedColoredVariants[$cacheKey];
        }

        $attributesColorList = Product::getAttributesColorList([(int) $id_product]);

        return self::$prefetchedColoredVariants[$cacheKey] = is_array($attributesColorList) ? current($attributesColorList) : null;
    }

    /**
     * Prefetches the colored variants of a whole set of products in a single query, so
     * getColoredVariants() is not resolved once per product during listing presentation. The result
     * is stored request-scoped and consumed by getColoredVariants().
     *
     * @param int[] $idProducts
     */
    public function prefetchColoredVariants(array $idProducts)
    {
        $idProducts = array_unique(array_filter(array_map('intval', $idProducts)));
        if (empty($idProducts)) {
            return;
        }

        $attributesColorList = Product::getAttributesColorList($idProducts);
        $attributesColorList = is_array($attributesColorList) ? $attributesColorList : [];

        foreach ($idProducts as $idProduct) {
            self::$prefetchedColoredVariants[$this->cacheKey($idProduct)] = $attributesColorList[$idProduct] ?? null;
        }
    }

    private function cacheKey(int $idProduct): string
    {
        $context = Context::getContext();

        return $idProduct . '-' . (int) $context->language->id . '-' . (int) $context->shop->id;
    }
}
