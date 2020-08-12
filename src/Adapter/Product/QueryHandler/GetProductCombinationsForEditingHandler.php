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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetProductCombinationsForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler\GetProductCombinationsForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationAttributeInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\ProductCombinationForEditing;

/**
 * Handles @see GetProductCombinationsForEditing using legacy object model
 */
final class GetProductCombinationsForEditingHandler extends AbstractProductHandler implements GetProductCombinationsForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetProductCombinationsForEditing $query): array
    {
        $product = $this->getProduct($query->getProductId());
        //@todo: allow pagination?
        $combinations = $product->getAttributeCombinations();

        return $this->formatCombinationsForEditing($combinations);
    }

    /**
     * @param array $combinations
     * @todo: 2 loops for massive combination arrays ? RIP
     *
     * @return ProductCombinationForEditing[]
     */
    private function formatCombinationsForEditing(array $combinations): array
    {
        $combinationsForEditing = [];
        $attributesInformationByCombinationId = [];

        foreach ($combinations as $combination) {
            $combinationId = (int) $combination['id_product_attribute'];
            $attributesInformationByCombinationId[$combinationId][] = new CombinationAttributeInformation(
                (int) $combination['id_attribute_group'],
                $combination['group_name'],
                (int) $combination['id_attribute'],
                $combination['attribute_name']
            );
        }

        foreach ($attributesInformationByCombinationId as $combinationId => $attributesInformation) {
            $combinationsForEditing[] = new ProductCombinationForEditing(
                $combinationId,
                $this->buildCombinationName($attributesInformation),
                $attributesInformation
            );
        }

        return $combinationsForEditing;
    }

    /**
     * @param array $attributesInformation
     *
     * @return string
     */
    private function buildCombinationName(array $attributesInformation): string
    {
        $combinedNameParts = [];
        foreach ($attributesInformation as $combinationAttributeInformation) {
            $combinedNameParts[] = sprintf(
                '%s - %s',
                $combinationAttributeInformation->getAttributeGroupName(),
                $combinationAttributeInformation->getAttributeName()
            );
        }

        return implode(', ', $combinedNameParts);
    }
}
