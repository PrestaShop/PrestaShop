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

namespace PrestaShop\PrestaShop\Adapter\Product\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use ObjectModel;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Adapter\Manufacturer\Repository\ManufacturerRepository;
use PrestaShop\PrestaShop\Adapter\Product\Validate\ProductValidator;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRulesGroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\NoManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotAddProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotBulkDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDuplicateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductTaxRulesGroupSettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;
use Product;

/**
 * Methods to access data storage for Product
 */
class ProductRepository extends AbstractObjectModelRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var ProductValidator
     */
    private $productValidator;

    /**
     * @var int
     */
    private $defaultCategoryId;

    /**
     * @var TaxRulesGroupRepository
     */
    private $taxRulesGroupRepository;

    /**
     * @var ManufacturerRepository
     */
    private $manufacturerRepository;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param ProductValidator $productValidator
     * @param int $defaultCategoryId
     * @param TaxRulesGroupRepository $taxRulesGroupRepository
     * @param ManufacturerRepository $manufacturerRepository
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        ProductValidator $productValidator,
        int $defaultCategoryId,
        TaxRulesGroupRepository $taxRulesGroupRepository,
        ManufacturerRepository $manufacturerRepository
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->productValidator = $productValidator;
        $this->defaultCategoryId = $defaultCategoryId;
        $this->taxRulesGroupRepository = $taxRulesGroupRepository;
        $this->manufacturerRepository = $manufacturerRepository;
    }

    /**
     * Duplicates product entity without relations
     *
     * @param Product $product
     *
     * @return Product
     *
     * @throws CoreException
     * @throws CannotDuplicateProductException
     * @throws ProductConstraintException
     * @throws ProductException
     */
    public function duplicate(Product $product): Product
    {
        unset($product->id, $product->id_product);

        $this->productValidator->validateCreation($product);
        $this->productValidator->validate($product);
        $this->addObjectModel($product, CannotDuplicateProductException::class);

        return $product;
    }

    /**
     * Gets product price by provided shop
     *
     * @param ProductId $productId
     * @param ShopId $shopId
     *
     * @return DecimalNumber|null
     */
    public function getPriceByShop(ProductId $productId, ShopId $shopId): ?DecimalNumber
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('price')
            ->from($this->dbPrefix . 'product_shop')
            ->where('id_product = :productId')
            ->andWhere('id_shop = :shopId')
            ->setParameter('productId', $productId->getValue())
            ->setParameter('shopId', $shopId->getValue())
        ;

        $result = $qb->execute()->fetch();

        if (!$result) {
            return null;
        }

        return new DecimalNumber($result['price']);
    }

    /**
     * @param ProductId $productId
     */
    public function assertProductExists(ProductId $productId): void
    {
        $this->assertObjectModelExists($productId->getValue(), 'product', ProductNotFoundException::class);
    }

    /**
     * @param ProductId[] $productIds
     *
     * @throws ProductNotFoundException
     */
    public function assertAllProductsExists(array $productIds): void
    {
        //@todo: no shop association. Should it be checked here?
        $ids = array_map(function (ProductId $productId): int {
            return $productId->getValue();
        }, $productIds);
        $ids = array_unique($ids);

        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(id_product) as product_count')
            ->from($this->dbPrefix . 'product')
            ->where('id_product IN (:productIds)')
            ->setParameter('productIds', $ids, Connection::PARAM_INT_ARRAY)
        ;

        $results = $qb->execute()->fetch();

        if (!$results || (int) $results['product_count'] !== count($ids)) {
            throw new ProductNotFoundException(
                    sprintf(
                        'Some of these products do not exist: %s',
                        implode(',', $ids)
                    )
                );
        }
    }

    /**
     * @param ProductId $productId
     * @param LanguageId $languageId
     *
     * @return array<array<string, string>>
     *                             e.g [
     *                             ['id_product' => '1', 'name' => 'Product name', 'reference' => 'demo15'],
     *                             ['id_product' => '2', 'name' => 'Product name2', 'reference' => 'demo16'],
     *                             ]
     *
     * @throws CoreException
     */
    public function getRelatedProducts(ProductId $productId, LanguageId $languageId): array
    {
        $this->assertProductExists($productId);
        $productIdValue = $productId->getValue();

        try {
            $accessories = Product::getAccessoriesLight($languageId->getValue(), $productIdValue);
        } catch (PrestaShopException $e) {
            throw new CoreException(sprintf(
                'Error occurred when fetching related products for product #%d',
                $productIdValue
            ));
        }

        return $accessories;
    }

    /**
     * @param ProductId $productId
     *
     * @return Product
     *
     * @throws CoreException
     */
    public function get(ProductId $productId): Product
    {
        return $this->getProductByDefaultShop($productId);
    }

    /**
     * @param ProductId $productId
     *
     * @return ShopId
     *
     * @throws ProductNotFoundException
     */
    public function getProductDefaultShopId(ProductId $productId): ShopId
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('id_shop_default')
            ->from($this->dbPrefix . 'product')
            ->where('id_product = :productId')
            ->setParameter('productId', $productId->getValue())
        ;

        $result = $qb->execute()->fetch();
        if (empty($result['id_shop_default'])) {
            throw new ProductNotFoundException(sprintf(
                'Could not find Product with id %d',
                $productId->getValue()
            ));
        }

        return new ShopId((int) $result['id_shop_default']);
    }

    /**
     * @param ProductId $productId
     * @param ProductShopConstraint $shopConstraint
     *
     * @return Product
     *
     * @throws CoreException
     */
    public function getByShopConstraint(ProductId $productId, ProductShopConstraint $shopConstraint): Product
    {
        if ($shopConstraint->forAllShops() || $shopConstraint->forDefaultProductShop()) {
            return $this->getProductByDefaultShop($productId);
        }

        return $this->getProductByShopId($productId, $shopConstraint->getShopId());
    }

    /**
     * @param array<int, string> $localizedNames
     * @param string $productType
     *
     * @return Product
     *
     * @throws CannotAddProductException
     */
    public function create(array $localizedNames, string $productType, ShopId $shopId): Product
    {
        $product = new Product(null, false, null, $shopId->getValue());
        $product->active = false;
        $product->id_category_default = $this->defaultCategoryId;
        $product->name = $localizedNames;
        $product->is_virtual = ProductType::TYPE_VIRTUAL === $productType;
        $product->cache_is_pack = ProductType::TYPE_PACK === $productType;
        $product->product_type = $productType;
        $product->id_shop_default = $shopId->getValue();

        $this->productValidator->validateCreation($product);
        $this->addObjectModelToShop($product, $shopId->getValue(), CannotAddProductException::class);
        $product->addToCategories([$product->id_category_default]);

        return $product;
    }

    /**
     * @param Product $product
     * @param array $propertiesToUpdate
     * @param int $errorCode
     */
    public function partialUpdate(Product $product, array $propertiesToUpdate, int $errorCode): void
    {
        $this->validateProduct($product, $propertiesToUpdate);

        $this->partiallyUpdateObjectModel(
            $product,
            $propertiesToUpdate,
            CannotUpdateProductException::class,
            $errorCode
        );
    }

    /**
     * @param Product $product
     * @param array $propertiesToUpdate
     * @param ProductShopConstraint $shopConstraint
     * @param int $errorCode
     */
    public function partialUpdateForShopConstraint(Product $product, array $propertiesToUpdate, ProductShopConstraint $shopConstraint, int $errorCode): void
    {
        $this->validateProduct($product, $propertiesToUpdate);
        $shopIds = $this->getShopIdsByConstraint($product, $shopConstraint);

        $this->partiallyUpdateObjectModelForShops(
            $product,
            $propertiesToUpdate,
            $shopIds,
            CannotUpdateProductException::class,
            $errorCode
        );
    }

    /**
     * @param Product $product
     * @param ProductShopConstraint $shopConstraint
     * @param int $errorCode
     */
    public function updateForShopConstraint(Product $product, ProductShopConstraint $shopConstraint, int $errorCode): void
    {
        $this->validateProduct($product);
        $shopIds = $this->getShopIdsByConstraint($product, $shopConstraint);

        $this->updateObjectModelForShops(
            $product,
            $shopIds,
            CannotUpdateProductException::class,
            $errorCode
        );
    }

    /**
     * @param ProductId $productId
     *
     * @throws CoreException
     */
    public function delete(ProductId $productId): void
    {
        $this->deleteObjectModel($this->get($productId), CannotDeleteProductException::class);
    }

    /**
     * @param array $productIds
     *
     * @throws CannotBulkDeleteProductException
     */
    public function bulkDelete(array $productIds): void
    {
        $failedIds = [];
        foreach ($productIds as $productId) {
            try {
                $this->delete($productId);
            } catch (CannotDeleteProductException $e) {
                $failedIds[] = $productId->getValue();
            }
        }

        if (empty($failedIds)) {
            return;
        }

        throw new CannotBulkDeleteProductException(
            $failedIds,
            sprintf('Failed to delete following products: "%s"', implode(', ', $failedIds))
        );
    }

    /**
     * @param string $searchPhrase
     * @param LanguageId $languageId
     * @param ShopId $shopId
     * @param int|null $limit
     *
     * @return array<int, array<string, int|string>>
     */
    public function searchProducts(string $searchPhrase, LanguageId $languageId, ShopId $shopId, ?int $limit = null): array
    {
        $qb = $this->getSearchQueryBuilder($searchPhrase, $languageId, $shopId, $limit);
        $qb
            ->addSelect('p.id_product, pl.name, p.reference, i.id_image')
            ->addGroupBy('p.id_product')
            ->addOrderBy('pl.name', 'ASC')
            ->addOrderBy('p.id_product', 'ASC')
        ;

        return $qb->execute()->fetchAllAssociative();
    }

    /**
     * @param string $searchPhrase
     * @param LanguageId $languageId
     * @param ShopId $shopId
     * @param int|null $limit
     *
     * @return array<int, array<string, int|string>>
     */
    public function searchCombinations(string $searchPhrase, LanguageId $languageId, ShopId $shopId, ?int $limit = null): array
    {
        $qb = $this->getSearchQueryBuilder($searchPhrase, $languageId, $shopId, $limit);
        $qb
            ->addSelect('p.id_product, pa.id_product_attribute, pl.name, i.id_image')
            ->addSelect('p.reference as product_reference')
            ->addSelect('pa.reference as combination_reference')
            ->addSelect('ai.id_image as combination_image_id')
            ->leftJoin('p', $this->dbPrefix . 'product_attribute_image', 'ai', 'ai.id_product_attribute = pa.id_product_attribute')
            ->addGroupBy('p.id_product, pa.id_product_attribute')
            ->addOrderBy('pl.name', 'ASC')
            ->addOrderBy('p.id_product', 'ASC')
            ->addOrderBy('pa.id_product_attribute', 'ASC')
        ;

        return $qb->execute()->fetchAllAssociative();
    }

    /**
     * @param string $searchPhrase
     * @param LanguageId $languageId
     * @param ShopId $shopId
     * @param int|null $limit
     *
     * @return QueryBuilder
     */
    private function getSearchQueryBuilder(string $searchPhrase, LanguageId $languageId, ShopId $shopId, ?int $limit): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->addSelect('p.id_product, pl.name, p.reference, i.id_image')
            ->from($this->dbPrefix . 'product', 'p')
            ->join('p', $this->dbPrefix . 'product_shop', 'ps', 'ps.id_product = p.id_product AND ps.id_shop = :shopId')
            ->leftJoin('p', $this->dbPrefix . 'product_lang', 'pl', 'pl.id_product = p.id_product AND pl.id_lang = :languageId')
            ->leftJoin('p', $this->dbPrefix . 'image_shop', 'i', 'i.id_product = p.id_product AND i.id_shop = :shopId AND i.cover = 1')
            ->leftJoin('p', $this->dbPrefix . 'product_supplier', 'psu', 'psu.id_product = p.id_product')
            ->leftJoin('p', $this->dbPrefix . 'product_attribute', 'pa', 'pa.id_product = p.id_product')
            ->setParameter('shopId', $shopId->getValue())
            ->setParameter('languageId', $languageId->getValue())
            ->addOrderBy('pl.name', 'ASC')
            ->addGroupBy('p.id_product')
        ;

        $dbSearchPhrase = sprintf('"%%%s%%"', $searchPhrase);
        $qb->where($qb->expr()->or(
            $qb->expr()->like('pl.name', $dbSearchPhrase),

            // Product references
            $qb->expr()->like('p.isbn', $dbSearchPhrase),
            $qb->expr()->like('p.upc', $dbSearchPhrase),
            $qb->expr()->like('p.mpn', $dbSearchPhrase),
            $qb->expr()->like('p.reference', $dbSearchPhrase),
            $qb->expr()->like('p.ean13', $dbSearchPhrase),
            $qb->expr()->like('p.supplier_reference', $dbSearchPhrase),

            // Combination attributes
            $qb->expr()->like('pa.isbn', $dbSearchPhrase),
            $qb->expr()->like('pa.upc', $dbSearchPhrase),
            $qb->expr()->like('pa.mpn', $dbSearchPhrase),
            $qb->expr()->like('pa.reference', $dbSearchPhrase),
            $qb->expr()->like('pa.ean13', $dbSearchPhrase),
            $qb->expr()->like('pa.supplier_reference', $dbSearchPhrase)
        ));

        if (!empty($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb;
    }

    /**
     * @param ProductId $productId
     *
     * @return ShopId[]
     */
    public function getAssociatedShopIds(ProductId $productId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('id_shop')
            ->from($this->dbPrefix . 'product_shop')
            ->where('id_product = :productId')
            ->setParameter('productId', $productId->getValue())
        ;

        $result = $qb->execute()->fetchAll();
        if (empty($result)) {
            return [];
        }

        $shops = [];
        foreach ($result as $shop) {
            $shops[] = new ShopId((int) $shop['id_shop']);
        }

        return $shops;
    }

    /**
     * This override was needed because of the extra parameter in product constructor
     *
     * {@inheritDoc}
     */
    protected function constructObjectModel(int $id, string $objectModelClass, ?int $shopId): ObjectModel
    {
        return new Product($id, false, null, $shopId);
    }

    /**
     * @param Product $product
     * @param ProductShopConstraint $shopConstraint
     *
     * @return int[]
     */
    private function getShopIdsByConstraint(Product $product, ProductShopConstraint $shopConstraint): array
    {
        $shopIds = [];
        if ($shopConstraint->forAllShops()) {
            $shops = $this->getAssociatedShopIds(new ProductId((int) $product->id));
            foreach ($shops as $shopId) {
                $shopIds[] = $shopId->getValue();
            }
        } elseif ($shopConstraint->forDefaultProductShop()) {
            $shopIds = [$this->getProductDefaultShopId(new ProductId((int) $product->id))->getValue()];
        } else {
            $shopIds = [$shopConstraint->getShopId()->getValue()];
        }

        return $shopIds;
    }

    /**
     * @param ProductId $productId
     * @param ShopId $shopId
     *
     * @return Product
     *
     * @throws CoreException
     */
    private function getProductByShopId(ProductId $productId, ShopId $shopId): Product
    {
        /** @var Product $product */
        $product = $this->getObjectModelForShop(
            $productId->getValue(),
            Product::class,
            ProductNotFoundException::class,
            $shopId
        );

        return $this->loadProduct($product);
    }

    /**
     * @param ProductId $productId
     *
     * @return Product
     *
     * @throws ProductNotFoundException
     */
    private function getProductByDefaultShop(ProductId $productId): Product
    {
        $defaultShopId = $this->getProductDefaultShopId($productId);

        return $this->getProductByShopId($productId, $defaultShopId);
    }

    /**
     * @param Product $product
     * @param array $propertiesToUpdate
     *
     * @throws CoreException
     * @throws ProductConstraintException
     * @throws ProductException
     * @throws ProductPackConstraintException
     * @throws ProductStockConstraintException
     * @throws ManufacturerException
     * @throws TaxRulesGroupException
     */
    private function validateProduct(Product $product, array $propertiesToUpdate = []): void
    {
        $taxRulesGroupIdIsBeingUpdated = empty($propertiesToUpdate) || in_array('id_tax_rules_group', $propertiesToUpdate, true);
        $taxRulesGroupId = (int) $product->id_tax_rules_group;
        $manufacturerIdIsBeingUpdated = empty($propertiesToUpdate) || in_array('id_manufacturer', $propertiesToUpdate, true);
        $manufacturerId = (int) $product->id_manufacturer;

        if ($taxRulesGroupIdIsBeingUpdated && $taxRulesGroupId !== ProductTaxRulesGroupSettings::NONE_APPLIED) {
            $this->taxRulesGroupRepository->assertTaxRulesGroupExists(new TaxRulesGroupId($taxRulesGroupId));
        }
        if ($manufacturerIdIsBeingUpdated && $manufacturerId !== NoManufacturerId::NO_MANUFACTURER_ID) {
            $this->manufacturerRepository->assertManufacturerExists(new ManufacturerId($manufacturerId));
        }

        $this->productValidator->validate($product);
    }

    /**
     * @param Product $product
     *
     * @return Product
     *
     * @throws CoreException
     */
    private function loadProduct(Product $product): Product
    {
        try {
            $product->loadStockData();
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to load Product stock #%d', $product->id),
                0,
                $e
            );
        }

        return $product;
    }
}
