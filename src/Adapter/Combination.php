<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter;

use Combination as CombinationLegacy;
use Db;
use DbQuery;

/**
 * Adapter for Combination legacy class.
 */
class Combination
{
    /**
     * Check if a combination exists in database.
     *
     * @param int $combinationId
     *
     * @return bool
     */
    public function existsInDatabase(int $combinationId): bool
    {
        return CombinationLegacy::existsInDatabase($combinationId);
    }

    /**
     * Checks that a combination belongs to a product and that the combination and all its attributes
     * are associated with the provided shop.
     */
    public function isCombinationAndAttributesAssociatedToShop(
        int $combinationId,
        int $productId,
        int $shopId,
    ): bool {
        if ($combinationId <= 0 || $productId <= 0 || $shopId <= 0) {
            return false;
        }

        $query = new DbQuery();
        $query->select('pa.id_product_attribute');
        $query->from('product_attribute', 'pa');
        $query->innerJoin(
            'product_attribute_shop',
            'pas',
            'pas.id_product_attribute = pa.id_product_attribute
                AND pas.id_product = pa.id_product
                AND pas.id_shop = ' . (int) $shopId
        );
        $query->innerJoin(
            'product_attribute_combination',
            'pac',
            'pac.id_product_attribute = pa.id_product_attribute'
        );
        $query->leftJoin(
            'attribute_shop',
            'attribute_shop',
            'attribute_shop.id_attribute = pac.id_attribute
                AND attribute_shop.id_shop = ' . (int) $shopId
        );
        $query->where('pa.id_product_attribute = ' . (int) $combinationId);
        $query->where('pa.id_product = ' . (int) $productId);
        $query->groupBy('pa.id_product_attribute');
        $query->having('COUNT(pac.id_attribute) = COUNT(attribute_shop.id_attribute)');

        return (bool) Db::getInstance()->getValue($query);
    }
}
