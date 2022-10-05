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
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Exception as ExceptionAlias;
use Doctrine\DBAL\Query\QueryBuilder;
use ObjectModel;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Manufacturer\Repository\ManufacturerRepository;
use PrestaShop\PrestaShop\Adapter\Product\Validate\ProductValidator;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRulesGroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\NoManufacturerId;
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
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
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
     * @param TaxRulesGroupRepository $taxRulesGroupRepository
     * @param ManufacturerRepository $manufacturerRepository
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        ProductValidator $productValidator,
        TaxRulesGroupRepository $taxRulesGroupRepository,
        ManufacturerRepository $manufacturerRepository
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->productValidator = $productValidator;
        $this->taxRulesGroupRepository = $taxRulesGroupRepository;
        $this->manufacturerRepository = $manufacturerRepository;
    }

    /**
     * @todo: Not sure this should be in the repository as it gives a false feeling the the repository can duplicate a
     *        product on its own, but you actually need to use the ProductDuplicator service to do it right, this method
     *        should be removed and the duplicator service should rely on repository to add/update but it is the one that
     *        must perform the required modifications on the object instance
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
     * Gets position product position in category
     *
     * @param ProductId $productId
     * @param CategoryId $categoryId
     *
     * @return int|null
     */
    public function getPositionInCategory(ProductId $productId, CategoryId $categoryId): ?int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('position')
            ->from($this->dbPrefix . 'category_product')
            ->where('id_product = :productId')
            ->andWhere('id_category = :categoryId')
            ->setParameter('productId', $productId->getValue())
            ->setParameter('categoryId', $categoryId->getValue())
        ;

        $position = $qb->execute()->fetchOne();

        if (!$position) {
            return null;
        }

        return (int) $position;
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
     *
     * @throws ProductNotFoundException
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
     *                                      e.g [
     *                                      ['id_product' => '1', 'name' => 'Product name', 'reference' => 'demo15'],
     *                                      ['id_product' => '2', 'name' => 'Product name2', 'reference' => 'demo16'],
     *                                      ]
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
        /** @var Product $product */
        $product = $this->getObjectModel(
            $productId->getValue(),
            Product::class,
            ProductNotFoundException::class
        );

        return $this->loadProduct($product);
    }

    /**
     * @param ProductId $productId
     *
     * @return ProductType
     *
     * @throws ProductNotFoundException
     */
    public function getProductType(ProductId $productId): ProductType
    {
        $result = $this->connection->createQueryBuilder()
            ->select('p.product_type')
            ->from($this->dbPrefix . 'product', 'p')
            ->where('p.id_product = :productId')
            ->setParameter('productId', $productId->getValue())
            ->execute()
            ->fetchAssociative()
        ;

        if (empty($result)) {
            throw new ProductNotFoundException(sprintf(
                'Cannot find product type for product %d because it does not exist',
                $productId->getValue()
            ));
        }

        if (!empty($result['product_type'])) {
            return new ProductType($result['product_type']);
        }

        // Older products that were created before product page v2, might have no type, so we determine it dynamically
        return new ProductType($this->get($productId)->getDynamicProductType());
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
     * @param ProductId $productId
     *
     * @throws CoreException
     */
    public function delete(ProductId $productId): void
    {
        $this->deleteObjectModel($this->get($productId), CannotDeleteProductException::class);
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
        $qb = $this->getSearchQueryBuilder(
            $searchPhrase,
            $languageId,
            $shopId,
            [],
            $limit);
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
     * @param array $filters
     * @param int|null $limit
     *
     * @return array<int, array<string, int|string>>
     *
     * @throws Exception
     * @throws ExceptionAlias
     */
    public function searchCombinations(
        string $searchPhrase,
        LanguageId $languageId,
        ShopId $shopId,
        array $filters = [],
        ?int $limit = null
    ): array {
        $qb = $this->getSearchQueryBuilder(
            $searchPhrase,
            $languageId,
            $shopId,
            $filters,
            $limit
        );
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
     * @param array $filters
     * @param int|null $limit
     *
     * @return QueryBuilder
     */
    protected function getSearchQueryBuilder(
        string $searchPhrase,
        LanguageId $languageId,
        ShopId $shopId,
        array $filters = [],
        ?int $limit = null
    ): QueryBuilder {
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

        if (!empty($filters)) {
            foreach ($filters as $type => $filter) {
                switch ($type) {
                    case 'filteredTypes':
                        $qb->andWhere('p.product_type not in(:filter)')
                            ->setParameter('filter', implode(', ', $filter));
                        break;
                    default:
                        break;
                }
            }
        }

        if (!empty($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb;
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
     * @todo: this should be removable soon once the deprecated stock properties have been removed see PR #26682
     *
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

    public function getProductTaxRulesGroupId(ProductId $productId): TaxRulesGroupId
    {
        $result = $this->connection->createQueryBuilder()
            ->addSelect('p.id_tax_rules_group')
            ->from($this->dbPrefix . 'product', 'p')
            ->where('id_product = :productId')
            ->setParameter('productId', $productId->getValue())
            ->execute()
            ->fetchOne()
        ;

        return new TaxRulesGroupId((int) $result);
    }
}
