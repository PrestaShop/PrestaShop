<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Util;

use Combination;
use Configuration;
use PrestaShopDatabaseException;
use PrestaShopException;
use ProductAttribute;
use StockAvailable;

class ProductCombinationFactory
{
    /**
     * @param int $productId
     * @param CombinationDetails[] $combinationDetailsList
     *
     * @return Combination[]
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function makeCombinations(int $productId, array $combinationDetailsList): array
    {
        $combinations = [];
        $attributesList = ProductAttribute::getAttributes((int) Configuration::get('PS_LANG_DEFAULT'));

        foreach ($combinationDetailsList as $combinationDetails) {
            $combinationName = $combinationDetails->getReference();
            $combination = new Combination();
            $combination->reference = $combinationName;
            $combination->id_product = $productId;
            if ($combinationDetails->getPrice()) {
                $combination->price = $combinationDetails->getPrice();
            }
            $combination->add();

            StockAvailable::setQuantity($productId, $combination->id, (int) $combinationDetails->getQuantity());

            $combinations[] = $combination;
            $combinationAttributesIds = [];
            foreach ($combinationDetails->getAttributes() as $combinationAttribute) {
                list($attributeGroup, $attributeName) = explode(':', $combinationAttribute);
                foreach ($attributesList as $attributeDetail) {
                    if ($attributeDetail['attribute_group'] == $attributeGroup && $attributeDetail['name'] == $attributeName) {
                        $combinationAttributesIds[] = (int) $attributeDetail['id_attribute'];
                        continue 2;
                    }
                }
            }
            $combination->setAttributes($combinationAttributesIds);
        }

        return $combinations;
    }
}
