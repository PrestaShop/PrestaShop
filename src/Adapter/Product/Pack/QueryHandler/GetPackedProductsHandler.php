<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Pack\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Pack\Repository\ProductPackRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Provider\ProductImageProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Query\GetPackedProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\QueryHandler\GetPackedProductsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\QueryResult\PackedProductDetails;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopAssociationNotFound;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Product\Combination\NameBuilder\CombinationNameBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Handles GetPackedProducts query using legacy object model
 */
#[AsQueryHandler]
class GetPackedProductsHandler implements GetPackedProductsHandlerInterface
{
    /**
     * @var int
     */
    protected $languageId;

    /**
     * @var ProductPackRepository
     */
    protected $productPackRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var AttributeRepository
     */
    protected $attributeRepository;

    /**
     * @var CombinationNameBuilder
     */
    protected $combinationNameBuilder;

    /**
     * @var ProductImageRepository
     */
    protected $productImageRepository;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var ProductImageProviderInterface
     */
    private $productImageProvider;

    public function __construct(
        int $defaultLangId,
        ProductPackRepository $productPackRepository,
        ProductRepository $productRepository,
        CombinationRepository $combinationRepository,
        AttributeRepository $attributeRepository,
        CombinationNameBuilder $combinationNameBuilder,
        ProductImageRepository $productImageRepository,
        TranslatorInterface $translator,
        ProductImageProviderInterface $productImageProvider
    ) {
        $this->languageId = $defaultLangId;
        $this->productPackRepository = $productPackRepository;
        $this->productRepository = $productRepository;
        $this->attributeRepository = $attributeRepository;
        $this->combinationNameBuilder = $combinationNameBuilder;
        $this->productImageRepository = $productImageRepository;
        $this->translator = $translator;
        $this->combinationRepository = $combinationRepository;
        $this->productImageProvider = $productImageProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetPackedProducts $query): array
    {
        $shopConstraint = $query->getShopConstraint();
        $packedItems = $this->productPackRepository->getPackedProducts(
            $query->getPackId(),
            $query->getLanguageId(),
            $shopConstraint
        );
        $packedProducts = [];
        $combinationIds = [];
        foreach ($packedItems as $packedItem) {
            $combinationId = (int) $packedItem['id_product_attribute_item'];

            if ($combinationId > 0) {
                $combinationIds[] = new CombinationId($combinationId);
            }
        }
        $attributesInformations = $this->attributeRepository->getAttributesInfoByCombinationIds(
            $combinationIds,
            new LanguageId($this->languageId)
        );

        foreach ($packedItems as $packedItem) {
            $combinationId = (int) $packedItem['id_product_attribute_item'];
            if ($combinationId === NoCombinationId::NO_COMBINATION_ID) {
                $coverUrl = $this->getPackCoverForStandardProduct(
                    new PackId((int) $packedItem['id_product_item']),
                    $shopConstraint->getShopId()
                );
            } else {
                $coverUrl = $this->getPackCoverForCombination(
                    new CombinationId($combinationId),
                    $shopConstraint->getShopId()
                );
            }
            $name = $packedItem['name'];
            if ($combinationId > 0) {
                $name = $this->combinationNameBuilder->buildFullName($name, $attributesInformations[$combinationId]);
            }
            $reference = '';
            if (!empty($packedItem['combination_reference']) || !empty($packedItem['product_reference'])) {
                $reference = empty($packedItem['combination_reference']) ? $packedItem['product_reference'] : $packedItem['combination_reference'];
                $reference = $this->translator->trans('Ref: %s', ['%s' => $reference], 'Admin.Catalog.Feature');
            }

            $packedProducts[] = new PackedProductDetails(
                (int) $packedItem['id_product_item'],
                (int) $packedItem['quantity'],
                (int) $combinationId,
                (string) $name,
                (string) $reference,
                $coverUrl
            );
        }

        return $packedProducts;
    }

    private function getPackCoverForStandardProduct(PackId $packedItemId, ShopId $shopId): string
    {
        try {
            return $this->productImageProvider->getProductCoverUrl($packedItemId, $shopId);
        } catch (ShopAssociationNotFound) {
            return $this->productImageProvider->getProductCoverUrl(
                $packedItemId,
                $this->productRepository->getProductDefaultShopId($packedItemId)
            );
        }
    }

    private function getPackCoverForCombination(CombinationId $packedCombinationId, ShopId $shopId): string
    {
        try {
            return $this->productImageProvider->getCombinationCoverUrl($packedCombinationId, $shopId);
        } catch (ShopAssociationNotFound) {
            return $this->productImageProvider->getCombinationCoverUrl(
                $packedCombinationId,
                $this->combinationRepository->getDefaultShopIdForCombination($packedCombinationId)
            );
        }
    }
}
