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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
