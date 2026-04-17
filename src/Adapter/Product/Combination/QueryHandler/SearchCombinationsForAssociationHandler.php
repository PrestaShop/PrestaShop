<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\SearchCombinationsForAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler\SearchCombinationsForAssociationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Product\Combination\NameBuilder\CombinationNameBuilderInterface;

#[AsQueryHandler]
class SearchCombinationsForAssociationHandler implements SearchCombinationsForAssociationHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductImagePathFactory
     */
    private $productImagePathFactory;

    /**
     * @var AttributeRepository
     */
    protected $attributeRepository;

    /**
     * @var CombinationNameBuilderInterface
     */
    protected $combinationNameBuilder;

    /**
     * @param ProductRepository $productRepository
     * @param AttributeRepository $attributeRepository
     * @param ProductImagePathFactory $productImagePathFactory
     * @param CombinationNameBuilderInterface $combinationNameBuilder
     */
    public function __construct(
        ProductRepository $productRepository,
        AttributeRepository $attributeRepository,
        ProductImagePathFactory $productImagePathFactory,
        CombinationNameBuilderInterface $combinationNameBuilder
    ) {
        $this->productRepository = $productRepository;
        $this->productImagePathFactory = $productImagePathFactory;
        $this->attributeRepository = $attributeRepository;
        $this->combinationNameBuilder = $combinationNameBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(SearchCombinationsForAssociation $query): array
    {
        $foundCombinations = $this->productRepository->searchCombinations(
            $query->getPhrase(),
            $query->getLanguageId(),
            $query->getShopId(),
            $query->getFilters(),
            $query->getLimit()
        );

        $productsForAssociation = [];
        foreach ($foundCombinations as $foundProduct) {
            $productsForAssociation[] = $this->createResult($foundProduct, $query->getLanguageId());
        }

        return $productsForAssociation;
    }

    /**
     * @param array $foundCombination
     *
     * @return CombinationForAssociation
     */
    protected function createResult(array $foundCombination, LanguageId $languageId): CombinationForAssociation
    {
        if (!empty($foundCombination['combination_image_id'])) {
            $imagePath = $this->productImagePathFactory->getPathByType(
                new ImageId((int) $foundCombination['combination_image_id']),
                ProductImagePathFactory::IMAGE_TYPE_HOME_DEFAULT
            );
        } elseif (!empty($foundCombination['id_image'])) {
            $imagePath = $this->productImagePathFactory->getPathByType(
                new ImageId((int) $foundCombination['id_image']),
                ProductImagePathFactory::IMAGE_TYPE_HOME_DEFAULT
            );
        } else {
            $imagePath = $this->productImagePathFactory->getNoImagePath(ProductImagePathFactory::IMAGE_TYPE_HOME_DEFAULT);
        }

        $combinationId = (int) ($foundCombination['id_product_attribute'] ?? NoCombinationId::NO_COMBINATION_ID);

        return new CombinationForAssociation(
            (int) $foundCombination['id_product'],
            $combinationId,
            $this->buildName($foundCombination['name'], $combinationId, $languageId),
            $foundCombination['combination_reference'] ?: ($foundCombination['product_reference'] ?: ''),
            $imagePath
        );
    }

    protected function buildName(string $productName, int $combinationId, LanguageId $languageId): string
    {
        if ($combinationId === NoCombinationId::NO_COMBINATION_ID) {
            return $productName;
        }
        $attributesInformation = $this->attributeRepository->getAttributesInfoByCombinationIds(
            [new CombinationId($combinationId)],
            $languageId
        );

        return $this->combinationNameBuilder->buildFullName($productName, $attributesInformation[$combinationId]);
    }
}
