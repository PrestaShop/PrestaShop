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

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\QueryHandler;

use PDO;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetEditableCombinationsList;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler\GetEditableCombinationsListHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationAttributeInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationListForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\EditableCombinationForListing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineQueryBuilderInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductCombinationFilters;

/**
 * Handles @see GetEditableCombinationsList using legacy object model
 */
final class GetEditableCombinationsListHandler implements GetEditableCombinationsListHandlerInterface
{
    /**
     * @var StockAvailableRepository
     */
    private $stockAvailableRepository;

    /**
     * @var DoctrineQueryBuilderInterface
     */
    private $combinationQueryBuilder;

    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @var ProductImagePathFactory
     */
    private $productImagePathFactory;

    /**
     * @param StockAvailableRepository $stockAvailableRepository
     * @param DoctrineQueryBuilderInterface $combinationQueryBuilder
     * @param AttributeRepository $attributeRepository
     * @param ProductImageRepository $productImageRepository
     * @param ProductImagePathFactory $productImagePathFactory
     */
    public function __construct(
        StockAvailableRepository $stockAvailableRepository,
        DoctrineQueryBuilderInterface $combinationQueryBuilder,
        AttributeRepository $attributeRepository,
        ProductImageRepository $productImageRepository,
        ProductImagePathFactory $productImagePathFactory
    ) {
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->combinationQueryBuilder = $combinationQueryBuilder;
        $this->attributeRepository = $attributeRepository;
        $this->productImageRepository = $productImageRepository;
        $this->productImagePathFactory = $productImagePathFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetEditableCombinationsList $query): CombinationListForEditing
    {
        $filters = $query->getFilters();
        $filters['product_id'] = $query->getProductId()->getValue();
        $searchCriteria = new ProductCombinationFilters([
            'limit' => $query->getLimit(),
            'offset' => $query->getOffset(),
            'orderBy' => $query->getOrderBy(),
            'sortOrder' => $query->getOrderWay(),
            'filters' => $filters,
        ]);

        $combinations = $this->combinationQueryBuilder->getSearchQueryBuilder($searchCriteria)->execute()->fetchAll();
        $total = (int) $this->combinationQueryBuilder->getCountQueryBuilder($searchCriteria)->execute()->fetch(PDO::FETCH_COLUMN);

        $combinationIds = array_map(function ($combination): int {
            return (int) $combination['id_product_attribute'];
        }, $combinations);

        $attributesInformation = $this->attributeRepository->getAttributesInfoByCombinationIds(
            $combinationIds,
            $query->getLanguageId()
        );

        $productImageIds = $this->productImageRepository->getImagesIds($query->getProductId());
        $imageIdsByCombinationIds = $this->productImageRepository->getImagesIdsForCombinations($combinationIds);

        return $this->formatEditableCombinationsForListing(
            $combinations,
            $attributesInformation,
            $total,
            $imageIdsByCombinationIds,
            $productImageIds
        );
    }

    /**
     * @param array $combinations
     * @param array<int, array<int, mixed>> $attributesInformationByCombinationId
     * @param int $totalCombinationsCount
     * @param array $imageIdsByCombinationIds
     * @param array $defaultImageIds
     *
     * @return CombinationListForEditing
     */
    private function formatEditableCombinationsForListing(
        array $combinations,
        array $attributesInformationByCombinationId,
        int $totalCombinationsCount,
        array $imageIdsByCombinationIds,
        array $defaultImageIds
    ): CombinationListForEditing {
        $combinationsForEditing = [];

        foreach ($combinations as $combination) {
            $combinationId = (int) $combination['id_product_attribute'];
            $combinationAttributesInformation = [];

            foreach ($attributesInformationByCombinationId[$combinationId] as $attributeInfo) {
                $combinationAttributesInformation[] = new CombinationAttributeInformation(
                    (int) $attributeInfo['id_attribute_group'],
                    $attributeInfo['attribute_group_name'],
                    (int) $attributeInfo['id_attribute'],
                    $attributeInfo['attribute_name']
                );
            }

            $imageId = null;
            if (!empty($imageIdsByCombinationIds[$combinationId])) {
                $imageId = reset($imageIdsByCombinationIds[$combinationId]);
            } elseif (!empty($defaultImageIds)) {
                $imageId = reset($defaultImageIds);
            }

            $impactOnPrice = new DecimalNumber($combination['price']);
            $combinationsForEditing[] = new EditableCombinationForListing(
                $combinationId,
                $this->buildCombinationName($combinationAttributesInformation),
                $combination['reference'],
                $combinationAttributesInformation,
                (bool) $combination['default_on'],
                $impactOnPrice,
                (int) $this->stockAvailableRepository->getForCombination(new CombinationId($combinationId))->quantity,
                $imageId ? $this->productImagePathFactory->getPathByType($imageId, ProductImagePathFactory::IMAGE_TYPE_SMALL_DEFAULT) : null
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
