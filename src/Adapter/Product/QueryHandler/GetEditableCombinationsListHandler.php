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

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Adapter\Product\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetEditableCombinationsList;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler\GetEditableCombinationsListHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationAttributeInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationListForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\EditableCombinationForListing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractor;
use Product;

/**
 * Handles @see GetEditableCombinationsList using legacy object model
 */
final class GetEditableCombinationsListHandler extends AbstractProductHandler implements GetEditableCombinationsListHandlerInterface
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
     * @var NumberExtractor
     */
    private $numberExtractor;

    /**
     * @var StockAvailableRepository
     */
    private $stockAvailableRepository;

    /**
     * @param CombinationRepository $combinationRepository
     * @param ProductRepository $productRepository
     * @param NumberExtractor $numberExtractor
     * @param StockAvailableRepository $stockAvailableRepository
     */
    public function __construct(
        CombinationRepository $combinationRepository,
        ProductRepository $productRepository,
        NumberExtractor $numberExtractor,
        StockAvailableRepository $stockAvailableRepository
    ) {
        $this->combinationRepository = $combinationRepository;
        $this->productRepository = $productRepository;
        $this->numberExtractor = $numberExtractor;
        $this->stockAvailableRepository = $stockAvailableRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetEditableCombinationsList $query): CombinationListForEditing
    {
        $productId = $query->getProductId();
        $product = $this->productRepository->get($productId);
        $combinations = $this->combinationRepository->getProductCombinations($productId, $query->getLimit(), $query->getOffset());

        $combinationIds = array_map(function ($combination): int {
            return (int) $combination['id_product_attribute'];
        }, $combinations);

        $attributesInformation = $this->combinationRepository->getAttributesInfoByCombinationIds(
            $combinationIds,
            $query->getLanguageId()
        );

        return $this->formatEditableCombinationsForListing(
            $product,
            $combinations,
            $attributesInformation,
            $this->combinationRepository->getTotalCombinationsCount($productId)
        );
    }

    /**
     * @param Product $product
     * @param array $combinations
     * @param array<int, array<int, mixed>> $attributesInformationByCombinationId
     * @param int $totalCombinationsCount
     *
     * @return CombinationListForEditing
     */
    private function formatEditableCombinationsForListing(
        Product $product,
        array $combinations,
        array $attributesInformationByCombinationId,
        int $totalCombinationsCount
    ): CombinationListForEditing {
        $productPrice = $this->numberExtractor->extract($product, 'price');
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

            $impactOnPrice = new DecimalNumber($combination['price']);
            $combinationsForEditing[] = new EditableCombinationForListing(
                $combinationId,
                $this->buildCombinationName($combinationAttributesInformation),
                $combinationAttributesInformation,
                (bool) $combination['default_on'],
                $impactOnPrice,
                $productPrice->plus($impactOnPrice),
                (int) $this->stockAvailableRepository->getForCombination(new CombinationId($combinationId))->quantity
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
