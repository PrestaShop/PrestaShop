<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\QueryHandler;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CombinationAttributeInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetEditableCombinationsList;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler\GetEditableCombinationsListHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationListForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\EditableCombinationForListing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineQueryBuilderInterface;
use PrestaShop\PrestaShop\Core\Product\Combination\NameBuilder\CombinationNameBuilderInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductCombinationFilters;

/**
 * Handles @see GetEditableCombinationsList using legacy object model
 */
#[AsQueryHandler]
final class GetEditableCombinationsListHandler implements GetEditableCombinationsListHandlerInterface
{
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
     * @var CombinationNameBuilderInterface
     */
    private $combinationNameBuilder;

    /**
     * @param DoctrineQueryBuilderInterface $combinationQueryBuilder
     * @param AttributeRepository $attributeRepository
     * @param ProductImageRepository $productImageRepository
     * @param ProductImagePathFactory $productImagePathFactory
     * @param CombinationNameBuilderInterface $combinationNameBuilder
     */
    public function __construct(
        DoctrineQueryBuilderInterface $combinationQueryBuilder,
        AttributeRepository $attributeRepository,
        ProductImageRepository $productImageRepository,
        ProductImagePathFactory $productImagePathFactory,
        CombinationNameBuilderInterface $combinationNameBuilder
    ) {
        $this->combinationQueryBuilder = $combinationQueryBuilder;
        $this->attributeRepository = $attributeRepository;
        $this->productImageRepository = $productImageRepository;
        $this->productImagePathFactory = $productImagePathFactory;
        $this->combinationNameBuilder = $combinationNameBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetEditableCombinationsList $query): CombinationListForEditing
    {
        $shopId = $query->getShopConstraint()->getShopId();

        if (!$shopId) {
            throw new CombinationException(sprintf(
                'Only single shop constraint is supported for query %s',
                GetEditableCombinationsList::class
            ));
        }

        $filters = $query->getFilters();
        $filters['product_id'] = $query->getProductId()->getValue();

        $searchCriteria = new ProductCombinationFilters(
            ShopConstraint::shop($shopId->getValue()),
            [
                'limit' => $query->getLimit(),
                'offset' => $query->getOffset(),
                'orderBy' => $query->getOrderBy(),
                'sortOrder' => $query->getOrderWay(),
                'filters' => $filters,
            ]
        );

        $combinations = $this->combinationQueryBuilder->getSearchQueryBuilder($searchCriteria)->executeQuery()->fetchAllAssociative();
        $total = (int) $this->combinationQueryBuilder->getCountQueryBuilder($searchCriteria)->executeQuery()->fetchOne();

        $combinationIds = array_map(function (array $combination): CombinationId {
            return new CombinationId((int) $combination['id_product_attribute']);
        }, $combinations);

        $attributesInformation = $this->attributeRepository->getAttributesInfoByCombinationIds(
            $combinationIds,
            $query->getLanguageId()
        );

        $productImageIds = $this->productImageRepository->getImageIds($query->getProductId(), $query->getShopConstraint());
        $imageIdsByCombinationIds = $this->productImageRepository->getImageIdsForCombinations($combinationIds);

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
     * @param array<int, CombinationAttributeInformation[]> $attributesInformationByCombinationId
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

            $imageId = null;
            if (!empty($imageIdsByCombinationIds[$combinationId])) {
                $imageId = reset($imageIdsByCombinationIds[$combinationId]);
            } elseif (!empty($defaultImageIds)) {
                $imageId = reset($defaultImageIds);
            }

            if (null === $imageId) {
                $imagePath = $this->productImagePathFactory->getNoImagePath(ProductImagePathFactory::IMAGE_TYPE_SMALL_DEFAULT);
            } else {
                $imagePath = $this->productImagePathFactory->getPathByType(
                    $imageId,
                    ProductImagePathFactory::IMAGE_TYPE_SMALL_DEFAULT
                );
            }

            $combinationsForEditing[] = new EditableCombinationForListing(
                $combinationId,
                $this->combinationNameBuilder->buildName($attributesInformationByCombinationId[$combinationId]),
                $combination['reference'],
                $attributesInformationByCombinationId[$combinationId],
                (bool) $combination['default_on'],
                new DecimalNumber($combination['price']),
                (int) $combination['quantity'],
                $imagePath,
                new DecimalNumber($combination['ecotax'])
            );
        }

        return new CombinationListForEditing($totalCombinationsCount, $combinationsForEditing);
    }
}
