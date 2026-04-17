<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Pack\Repository;

use Doctrine\DBAL\Connection;
use Pack;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackId;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\QuantifiedProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\StockAvailableNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use PrestaShopException;
use Throwable;

class ProductPackRepository extends AbstractObjectModelRepository
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $dbPrefix;

    public function __construct(
        Connection $connection,
        string $dbPrefix,
        private ProductRepository $productRepository,
        private ConfigurationInterface $configuration,
        private StockAvailableRepository $stockAvailableRepository,
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * @param PackId $productId
     * @param LanguageId $languageId
     * @param ShopConstraint $shopConstraint
     *
     * @return array<array<string, string>>
     *                                      e.g [
     *                                      ['id_product_item' => '1', 'id_product_attribute_item' => '1', 'name' => 'Product name', 'reference' => 'demo15', 'quantity' => '1'],
     *                                      ['id_product_item' => '2', 'id_product_attribute_item' => '1', 'name' => 'Product name2', 'reference' => 'demo16', 'quantity' => '1'],
     *                                      ]
     *
     * @throws CoreException
     */
    public function getPackedProducts(PackId $productId, LanguageId $languageId, ShopConstraint $shopConstraint): array
    {
        if (!$shopConstraint->isSingleShopContext()) {
            throw new InvalidShopConstraintException('Product Pack has no features related with shop group or all shops, use single shop constraint');
        }

        $this->assertProductExists($productId);
        $productIdValue = $productId->getValue();

        try {
            $qb = $this->connection->createQueryBuilder();
            $qb->select('pack.id_product_item, pack.id_product_attribute_item, pack.quantity, attribute.reference as combination_reference, product.reference as product_reference, language.name')
                ->from($this->dbPrefix . 'pack', 'pack')
                ->leftJoin('pack', $this->dbPrefix . 'product', 'product', 'pack.id_product_item = product.id_product')
                ->leftJoin('pack', $this->dbPrefix . 'product_attribute', 'attribute', 'pack.id_product_attribute_item = attribute.id_product_attribute')
                ->leftJoin(
                    'pack',
                    $this->dbPrefix . 'product_lang',
                    'language',
                    // We use product default shop as fallback in case the required shop is not associated to the product
                    'product.id_product = language.id_product AND language.id_lang = :idLanguage AND (language.id_shop = :idShop OR language.id_shop = product.id_shop_default)'
                )
                ->where('pack.id_product_pack = :idProduct')
                ->orderBy('pack.id_product_item', 'ASC')
                ->setParameter('idProduct', $productId->getValue())
                ->setParameter('idLanguage', $languageId->getValue())
                ->setParameter('idShop', $shopConstraint->getShopId()->getValue())
                ->addGroupBy('product.id_product')
                ->addGroupBy('attribute.id_product_attribute')
            ;
            $packedProducts = $qb->executeQuery()->fetchAll();
        } catch (Throwable $exception) {
            throw new CoreException(
                sprintf(
                    'Error occurred when fetching packed products for pack #%d',
                    $productIdValue
                ),
                $exception->getCode(),
                $exception
            );
        }

        return $packedProducts;
    }

    /**
     * @param PackId $packId
     * @param QuantifiedProduct $productForPacking
     *
     * @throws CoreException
     * @throws ProductPackException
     */
    public function addProductToPack(PackId $packId, QuantifiedProduct $productForPacking): void
    {
        $packIdValue = $packId->getValue();

        try {
            $packed = Pack::addItem(
                $packIdValue,
                $productForPacking->getProductId()->getValue(),
                $productForPacking->getQuantity(),
                $productForPacking->getCombinationId() ?
                    $productForPacking->getCombinationId()->getValue() :
                    NoCombinationId::NO_COMBINATION_ID
            );
            if (!$packed) {
                throw new ProductPackException(
                    $this->appendIdsToMessage('Failed to add product to pack.', $productForPacking, $packIdValue),
                    ProductPackException::FAILED_ADDING_TO_PACK
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                $this->appendIdsToMessage('Error occurred when trying to add product to pack.', $productForPacking, $packIdValue),
                0,
                $e
            );
        }
    }

    /**
     * @param PackId $packId
     *
     * @throws CoreException
     * @throws ProductPackException
     */
    public function removeAllProductsFromPack(PackId $packId): void
    {
        $packIdValue = $packId->getValue();

        try {
            // We don't reset cache_is_pack for product we want to keep it tru as long as product type doesn't change
            if (!Pack::deleteItems($packIdValue, false)) {
                throw new ProductPackException(
                    sprintf('Failed to remove products from pack #%d', $packIdValue),
                    ProductPackException::FAILED_DELETING_PRODUCTS_FROM_PACK
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to remove pack items from pack #%d', $packIdValue),
                0,
                $e
            );
        }
    }

    /**
     * @param ProductId $productId
     *
     * @return array
     */
    public function getPacksContaining(ProductId $productId): array
    {
        $this->assertProductExists($productId);
        $qb = $this->connection->createQueryBuilder();
        $qb->select('pack.id_product_pack')
            ->from($this->dbPrefix . 'pack', 'pack')
            ->where('pack.id_product_item = :productId')
            ->setParameter('productId', $productId->getValue())
        ;

        $packs = $qb->executeQuery()->fetchAllAssociative();

        return array_map(function (array $packData) {
            return new PackId((int) $packData['id_product_pack']);
        }, $packs);
    }

    public function getDynamicPackQuantity(ProductId $productId, ShopId $shopId): ?int
    {
        $product = $this->productRepository->get($productId, $shopId);
        if ($product->getProductType() !== ProductType::TYPE_PACK) {
            return null;
        }

        $packQuantity = null;

        // First, get pack stock type
        $packStockType = (int) $product->pack_stock_type;
        if ($packStockType === PackStockType::STOCK_TYPE_DEFAULT) {
            $packStockType = (int) $this->configuration->get('PS_PACK_STOCK_TYPE');
        }

        // Now get quantity based on the pack stock type
        if ($packStockType === PackStockType::STOCK_TYPE_PACK_ONLY) {
            try {
                $stockAvailable = $this->stockAvailableRepository->getForProduct($productId, $shopId);
                $packQuantity = $stockAvailable->quantity;
            } catch (StockAvailableNotFoundException) {
                $packQuantity = 0;
            }
        } elseif ($packStockType === PackStockType::STOCK_TYPE_PRODUCTS_ONLY || $packStockType === PackStockType::STOCK_TYPE_BOTH) {
            $packedItems = $this->getPackedProducts(
                new PackId((int) $product->id),
                new LanguageId((int) $this->configuration->get('PS_LANG_DEFAULT')),
                ShopConstraint::shop($shopId->getValue())
            );

            // Compute minimum quantity based on products
            $productsMinQuantity = null;
            foreach ($packedItems as $packedItem) {
                $packedItemProductId = (int) $packedItem['id_product_item'];
                $packedItemCombinationId = (int) $packedItem['id_product_attribute_item'];
                $packedItemQuantity = (int) $packedItem['quantity'];
                try {
                    if (empty($packedItemCombinationId)) {
                        $packedItemStockAvailable = $this->stockAvailableRepository->getForProduct(new ProductId($packedItemProductId), $shopId);
                    } else {
                        $packedItemStockAvailable = $this->stockAvailableRepository->getForCombination(new CombinationId($packedItemCombinationId), $shopId);
                    }

                    $packedItemStock = $packedItemStockAvailable->quantity;
                } catch (StockAvailableNotFoundException) {
                    $packedItemStock = 0;
                }

                // Depending on the quantity required in the packs the amount of possible pack is impacted
                $availablePackUnits = (int) floor($packedItemStock / $packedItemQuantity);
                if ($productsMinQuantity === null) {
                    $productsMinQuantity = $availablePackUnits;
                } else {
                    $productsMinQuantity = min($productsMinQuantity, $availablePackUnits);
                }
            }

            if ($packStockType === PackStockType::STOCK_TYPE_PRODUCTS_ONLY) {
                $packQuantity = $productsMinQuantity;
            } else {
                try {
                    $stockAvailable = $this->stockAvailableRepository->getForProduct($productId, $shopId);
                    $packQuantity = min($productsMinQuantity, (int) $stockAvailable->quantity);
                } catch (StockAvailableNotFoundException) {
                    $packQuantity = 0;
                }
            }
        }

        return $packQuantity;
    }

    /**
     * Builds string with ids, that will help to identify objects that was being updated in case of error
     *
     * @param string $messageBody
     * @param QuantifiedProduct $product
     * @param int $packId
     *
     * @return string
     */
    private function appendIdsToMessage(string $messageBody, QuantifiedProduct $product, int $packId): string
    {
        if ($product->getCombinationId()) {
            $combinationId = sprintf(' combinationId #%d', $product->getCombinationId()->getValue());
        }

        return sprintf(
            "$messageBody. [packId #%d; productId #%d;%s]",
            $packId,
            $product->getProductId()->getValue(),
            isset($combinationId) ? $combinationId : ''
        );
    }

    /**
     * @param ProductId $productId
     */
    protected function assertProductExists(ProductId $productId): void
    {
        $this->assertObjectModelExists($productId->getValue(), 'product', ProductNotFoundException::class);
    }
}
