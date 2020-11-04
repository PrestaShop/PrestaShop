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
use PrestaShop\PrestaShop\Adapter\Product\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetProductCombinationsForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler\GetProductCombinationsForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationAttributeInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationListForEditing;

/**
 * Handles @see GetProductCombinationsForEditing using legacy object model
 */
final class GetProductCombinationsForEditingHandler extends AbstractProductHandler implements GetProductCombinationsForEditingHandlerInterface
{
    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param CombinationRepository $combinationRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        CombinationRepository $combinationRepository,
        ProductRepository $productRepository
    ) {
        $this->combinationRepository = $combinationRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetProductCombinationsForEditing $query): CombinationListForEditing
    {
        $productId = $query->getProductId();
        $this->productRepository->assertProductExists($productId);
        $combinations = $this->combinationRepository->getProductCombinations($productId, $query->getLimit(), $query->getOffset());

        $combinationIds = array_map(function ($combination): int {
            return (int) $combination['id_product_attribute'];
        }, $combinations);

        $attributesInformation = $this->combinationRepository->getAttributesInfoByCombinationIds(
            $combinationIds,
            $query->getLanguageId()
        );

        return $this->formatCombinationsForEditing(
            $combinations,
            $attributesInformation,
            $this->combinationRepository->getTotalCombinationsCount($productId)
        );
    }

    /**
     * @param array $combinations
     * @param array<int, array<int, mixed>> $attributesInformationByCombinationId
     * @param int $totalCombinationsCount
     *
     * @return CombinationListForEditing
     */
    private function formatCombinationsForEditing(
        array $combinations,
        array $attributesInformationByCombinationId,
        int $totalCombinationsCount
    ): CombinationListForEditing {
        $combinationsForEditing = [];

        foreach ($combinations as $combination) {
            $combinationId = (int) $combination['id_product_attribute'];
            $combinationAttributesInformation = [];

            foreach ($attributesInformationByCombinationId[$combinationId] as $attributesInfo) {
                foreach ($attributesInfo as $attributeInfo) {
                    $combinationAttributesInformation[] = new CombinationAttributeInformation(
                        (int) $attributeInfo['id_attribute_group'],
                        $attributeInfo['attribute_group_name'],
                        (int) $attributeInfo['id_attribute'],
                        $attributeInfo['attribute_name']
                    );
                }
            }

            $combinationsForEditing[] = new CombinationForEditing(
                $combinationId,
                $this->buildCombinationName($combinationAttributesInformation),
                $combinationAttributesInformation
            );
        }

        return new CombinationListForEditing($totalCombinationsCount, $combinationsForEditing);
    }

    /**
     * @param CombinationAttributeInformation[] $attributesInformation
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
