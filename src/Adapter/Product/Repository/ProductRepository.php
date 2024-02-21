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

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Exception as ExceptionAlias;
use Doctrine\DBAL\Query\QueryBuilder;
use ObjectModel;
use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Manufacturer\Repository\ManufacturerRepository;
use PrestaShop\PrestaShop\Adapter\Product\Validate\ProductValidator;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRulesGroupRepository;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\ValueObject\AttributeId;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierReferenceId;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\NoManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotAddProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductShopAssociationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductTaxRulesGroupSettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopAssociationNotFound;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopGroupAssociationNotFound;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;
use PrestaShopException;
use Product;

class ProductRepository extends AbstractMultiShopObjectModelRepository
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
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param ProductValidator $productValidator
     * @param TaxRulesGroupRepository $taxRulesGroupRepository
     * @param ManufacturerRepository $manufacturerRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        ProductValidator $productValidator,
        TaxRulesGroupRepository $taxRulesGroupRepository,
        ManufacturerRepository $manufacturerRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->productValidator = $productValidator;
        $this->taxRulesGroupRepository = $taxRulesGroupRepository;
        $this->manufacturerRepository = $manufacturerRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param ProductId $productId
     * @param ShopId $shopId
     *
     * @return Product
     *
     * @throws CoreException
     */
    public function get(ProductId $productId, ShopId $shopId): Product
    {
        return $this->getProductByShopId($productId, $shopId);
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

        $result = $qb->executeQuery()->fetchAssociative();
        if (empty($result['id_shop_default'])) {
            throw new ProductNotFoundException(sprintf(
                'Could not find Product with id %d',
                $productId->getValue()
            ));
        }

        return new ShopId((int) $result['id_shop_default']);
    }

    /**
     * Returns the default shop of a product among a group, if the product's default shop is in the group it will
     * naturally be returned. In the other case the first shop associated to the product in the group is returned.
     *
     * @param ProductId $productId
     * @param ShopGroupId $shopGroupId
     *
     * @return ShopId
     */
    public function getProductDefaultShopIdForGroup(ProductId $productId, ShopGroupId $shopGroupId): ShopId
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('p.id_shop_default, s.id_shop')
            ->from($this->dbPrefix . 'product', 'p')
            ->innerJoin(
                'p',
                $this->dbPrefix . 'product_shop',
                'ps',
                'ps.id_product = p.id_product'
            )
            ->innerJoin(
                'ps',
                $this->dbPrefix . 'shop',
                's',
                's.id_shop = ps.id_shop'
            )
            ->where('p.id_product = :productId')
            ->andWhere('s.id_shop_group = :shopGroupId')
            ->addOrderBy('s.id_shop', 'ASC')
            ->setParameter('shopGroupId', $shopGroupId->getValue())
            ->setParameter('productId', $productId->getValue())
        ;

        $result = $qb->executeQuery()->fetchAllAssociative();
        if (empty($result)) {
            throw new ShopGroupAssociationNotFound(sprintf(
                'Could not find association between Product %d and Shop group %d',
                $productId->getValue(),
                $shopGroupId->getValue()
            ));
        }

        // By default, the first shop from the group is considered the default one
        $defaultShopId = (int) $result[0]['id_shop'];
        foreach ($result as $productShop) {
            // If one of the shops from the group is the actual product's default shop it takes priority
            if ((int) $productShop['id_shop_default'] === (int) $productShop['id_shop']) {
                $defaultShopId = (int) $productShop['id_shop_default'];
                break;
            }
        }

        return new ShopId($defaultShopId);
    }

    /**
     * @param ProductId $productId
     * @param ShopConstraint $shopConstraint
     *
     * @return Product
     *
     * @throws CoreException
     */
    public function getByShopConstraint(ProductId $productId, ShopConstraint $shopConstraint): Product
    {
        if ($shopConstraint->getShopGroupId()) {
            return $this->getProductByShopGroup($productId, $shopConstraint->getShopGroupId());
        }

        if ($shopConstraint->forAllShops()) {
            return $this->getProductByDefaultShop($productId);
        }

        return $this->getProductByShopId($productId, $shopConstraint->getShopId());
    }

    /**
     * @param array<int, string> $localizedNames
     * @param array<int, string> $localizedLinkRewrites
     * @param string $productType
     * @param ShopId $shopId
     *
     * @return Product
     *
     * @throws CoreException
     */
    public function create(
        array $localizedNames,
        array $localizedLinkRewrites,
        string $productType,
        ShopId $shopId
    ): Product {
        $defaultCategoryId = $this->categoryRepository->getShopDefaultCategory($shopId);

        $product = new Product(null, false, null, $shopId->getValue());
        $product->active = false;
        $product->id_category_default = $defaultCategoryId->getValue();
        $product->is_virtual = ProductType::TYPE_VIRTUAL === $productType;
        $product->cache_is_pack = ProductType::TYPE_PACK === $productType;
        $product->product_type = $productType;
        $product->id_shop_default = $shopId->getValue();
        $product->name = $localizedNames;
        $product->link_rewrite = $localizedLinkRewrites;
        $product->id_tax_rules_group = $this->taxRulesGroupRepository->getIdTaxRulesGroupMostUsed();
        $currentDate = new DateTime('NOW');
        $product->published_date = $currentDate->format('Y-m-d');

        $this->productValidator->validateCreation($product);
        $this->addObjectModelToShops($product, [$shopId], CannotAddProductException::class);
        $this->categoryRepository->addProductAssociations(
            new ProductId((int) $product->id),
            [$defaultCategoryId]
        );

        return $product;
    }

    /**
     * @param Product $product
     * @param array $propertiesToUpdate
     * @param ShopConstraint $shopConstraint
     * @param int $errorCode
     */
    public function partialUpdate(Product $product, array $propertiesToUpdate, ShopConstraint $shopConstraint, int $errorCode): void
    {
        $this->validateProduct($product, $propertiesToUpdate);
        $shopIds = $this->getShopIdsByConstraint(new ProductId((int) $product->id), $shopConstraint);

        $this->partiallyUpdateObjectModelForShops(
            $product,
            $propertiesToUpdate,
            $shopIds,
            CannotUpdateProductException::class,
            $errorCode
        );
    }

    /**
     * @param ProductId $productId
     * @param CarrierReferenceId[] $carrierReferenceIds
     * @param ShopConstraint $shopConstraint
     */
    public function setCarrierReferences(ProductId $productId, array $carrierReferenceIds, ShopConstraint $shopConstraint): void
    {
        $shopIds = array_map(function (ShopId $shopId): int {
            return $shopId->getValue();
        }, $this->getShopIdsByConstraint($productId, $shopConstraint));

        $productIdValue = $productId->getValue();

        $deleteQb = $this->connection->createQueryBuilder();
        $deleteQb->delete($this->dbPrefix . 'product_carrier')
            ->where('id_product = :productId')
            ->andWhere($deleteQb->expr()->in('id_shop', ':shopIds'))
            ->setParameter('productId', $productIdValue)
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
            ->executeStatement()
        ;

        $insertValues = [];
        foreach ($carrierReferenceIds as $referenceId) {
            foreach ($shopIds as $shopId) {
                $insertValues[] = sprintf(
                    '(%d, %d, %d)',
                    $productIdValue,
                    $referenceId->getValue(),
                    $shopId
                );
            }
        }

        if (empty($insertValues)) {
            return;
        }

        $stmt = '
            INSERT INTO ' . $this->dbPrefix . 'product_carrier (
                id_product,
                id_carrier_reference,
                id_shop
            )
            VALUES ' . implode(',', $insertValues) . '
        ';

        $this->connection->executeStatement($stmt);
    }

    /**
     * @param Product $product
     * @param ShopConstraint $shopConstraint
     * @param int $errorCode
     */
    public function update(Product $product, ShopConstraint $shopConstraint, int $errorCode): void
    {
        $this->validateProduct($product);
        $this->updateObjectModelForShops(
            $product,
            $this->getShopIdsByConstraint(new ProductId((int) $product->id), $shopConstraint),
            CannotUpdateProductException::class,
            $errorCode
        );
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

        return array_map(static function (array $shop) {
            return new ShopId((int) $shop['id_shop']);
        }, $qb->executeQuery()->fetchAllAssociative());
    }

    /**
     * @param ProductId $productId
     * @param ShopGroupId $shopGroupId
     *
     * @return ShopId[]
     */
    public function getAssociatedShopIdsFromGroup(ProductId $productId, ShopGroupId $shopGroupId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('ps.id_shop')
            ->from($this->dbPrefix . 'product_shop', 'ps')
            ->innerJoin(
                'ps',
                $this->dbPrefix . 'shop',
                's',
                's.id_shop = ps.id_shop'
            )
            ->where('ps.id_product = :productId')
            ->andWhere('s.id_shop_group = :shopGroupId')
            ->setParameter('shopGroupId', $shopGroupId->getValue())
            ->setParameter('productId', $productId->getValue())
        ;

        return array_map(static function (array $shop) {
            return new ShopId((int) $shop['id_shop']);
        }, $qb->executeQuery()->fetchAllAssociative());
    }

    /**
     * @param ProductId $productId
     * @param ShopId[] $shopIds
     *
     * @throws CannotDeleteProductException
     * @throws ShopAssociationNotFound
     */
    public function deleteFromShops(ProductId $productId, array $shopIds): void
    {
        if (empty($shopIds)) {
            return;
        }

        foreach ($shopIds as $shopId) {
            $this->checkShopAssociation($productId->getValue(), Product::class, $shopId);
        }

        // We fetch the product from its default shop, the values don't matter anyway we just need a Product instance
        $product = $this->getProductByDefaultShop($productId);

        $this->deleteObjectModelFromShops($product, $shopIds, CannotDeleteProductException::class);
    }

    /**
     * @param ProductId $productId
     * @param ShopConstraint $shopConstraint
     */
    public function deleteByShopConstraint(ProductId $productId, ShopConstraint $shopConstraint): void
    {
        // We fetch the product from its default shop, the values don't matter anyway we just need a Product instance
        $product = $this->getProductByDefaultShop($productId);
        $shopIds = $this->getShopIdsByConstraint($productId, $shopConstraint);
        $this->deleteObjectModelFromShops($product, $shopIds, CannotDeleteProductException::class);
    }

    /**
     * @param ProductId $productId
     *
     * @return bool
     */
    public function hasCombinations(ProductId $productId): bool
    {
        $result = $this->connection->createQueryBuilder()
            ->select('pa.id_product_attribute')
            ->from($this->dbPrefix . 'product_attribute', 'pa')
            ->where('pa.id_product = :productId')
            ->setParameter('productId', $productId->getValue())
            ->executeQuery()
            ->fetchOne()
        ;

        return !empty($result);
    }

    /**
     * @param ProductId $productId
     *
     * @return AttributeGroupId[]
     */
    public function getProductAttributesGroupIds(ProductId $productId, ShopConstraint $shopConstraint): array
    {
        $shopIds = array_map(static function (ShopId $shopId): int {
            return $shopId->getValue();
        }, $this->getShopIdsByConstraint($productId, $shopConstraint));

        $qb = $this->connection->createQueryBuilder();
        $qb->select('a.id_attribute_group')
            ->from($this->dbPrefix . 'attribute', 'a')
            ->innerJoin(
                'a',
                $this->dbPrefix . 'product_attribute_combination',
                'pac',
                'a.id_attribute = pac.id_attribute'
            )
            ->innerJoin(
                'pac',
                $this->dbPrefix . 'product_attribute_shop',
                'pas',
                'pas.id_product_attribute = pac.id_product_attribute'
            )
            ->where('pas.id_product = :productId')
            ->andWhere($qb->expr()->in('pas.id_shop', ':shopIds'))
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
            ->setParameter('productId', $productId->getValue())
            ->groupBy('a.id_attribute_group')
        ;

        $results = $qb->executeQuery()->fetchFirstColumn();

        return array_map(static function (string $id): AttributeGroupId {
            return new AttributeGroupId((int) $id);
        }, $results);
    }

    /**
     * @param ProductId $productId
     *
     * @return AttributeId[]
     */
    public function getProductAttributesIds(ProductId $productId, ShopConstraint $shopConstraint): array
    {
        $shopIds = array_map(static function (ShopId $shopId): int {
            return $shopId->getValue();
        }, $this->getShopIdsByConstraint($productId, $shopConstraint));

        $qb = $this->connection->createQueryBuilder();
        $qb->select('pac.id_attribute')
            ->from($this->dbPrefix . 'product_attribute_combination', 'pac')
            ->innerJoin(
                'pac',
                $this->dbPrefix . 'product_attribute_shop',
                'pas',
                'pac.id_product_attribute = pas.id_product_attribute'
            )
            ->where('pas.id_product = :productId')
            ->andWhere($qb->expr()->in('pas.id_shop', ':shopIds'))
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
            ->setParameter('productId', $productId->getValue())
            ->groupBy('pac.id_attribute')
        ;

        $results = $qb->executeQuery()->fetchFirstColumn();

        return array_map(static function (string $id): AttributeId {
            return new AttributeId((int) $id);
        }, $results);
    }

    /**
     * Updates the Product's cache default attribute by selecting appropriate value from combination tables
     *
     * @param ProductId $productId
     */
    public function updateCachedDefaultCombination(ProductId $productId, ShopConstraint $shopConstraint): void
    {
        $shopIds = array_map(function (ShopId $shopId): int {
            return $shopId->getValue();
        }, $this->getShopIdsByConstraint($productId, $shopConstraint));

        $defaultShopId = $this->getProductDefaultShopId($productId)->getValue();
        $defaultCombinations = $this->connection->fetchAllAssociative(
            sprintf('
                SELECT id_product_attribute, id_shop
                FROM %sproduct_attribute_shop
                WHERE id_product = %d
                AND id_shop IN (%s)
                AND default_on = 1
                ORDER BY id_shop ASC
            ',
                $this->dbPrefix,
                $productId->getValue(),
                implode(',', $shopIds)
            )
        );

        $productShopTable = sprintf('%sproduct_shop', $this->dbPrefix);
        $combinationIdForDefaultShop = null;
        $combinationShopIds = [];

        foreach ($defaultCombinations as $defaultCombination) {
            $combinationId = (int) $defaultCombination['id_product_attribute'];
            $combinationShopId = (int) $defaultCombination['id_shop'];
            $combinationShopIds[] = $combinationShopId;

            if ($defaultShopId === $combinationShopId) {
                $combinationIdForDefaultShop = $combinationId;
            }

            $this->connection->executeStatement(sprintf(
                'UPDATE %s SET cache_default_attribute = %d WHERE id_product = %d AND id_shop = %d',
                $productShopTable,
                $combinationId,
                $productId->getValue(),
                $combinationShopId
            ));
        }

        $this->connection->executeStatement(sprintf(
            'UPDATE %sproduct SET cache_default_attribute = %d WHERE id_product = %d',
            $this->dbPrefix,
            $combinationIdForDefaultShop,
            $productId->getValue()
        ));

        $unhandledShopIds = array_diff($shopIds, $combinationShopIds);
        foreach ($unhandledShopIds as $shopId) {
            // reset default combination to 0 to all shop ids which have no combinations
            $this->connection->executeStatement(sprintf(
                'UPDATE %s SET cache_default_attribute = %d WHERE id_product = %d AND id_shop = %d',
                $productShopTable,
                0,
                $productId->getValue(),
                $shopId
            ));
        }
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
            ->executeQuery()
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
        return new ProductType($this->getProductByDefaultShop($productId)->getDynamicProductType());
    }

    /**
     * @param ProductId $productId
     *
     * @return Product
     *
     * @throws ProductNotFoundException
     */
    public function getProductByDefaultShop(ProductId $productId): Product
    {
        $defaultShopId = $this->getProductDefaultShopId($productId);

        return $this->getProductByShopId($productId, $defaultShopId);
    }

    /**
     * @param ProductId $productId
     * @param ShopId $shopId
     *
     * @throws ShopAssociationNotFound
     */
    public function assertProductIsAssociatedToShop(ProductId $productId, ShopId $shopId): void
    {
        $this->checkShopAssociation(
            $productId->getValue(),
            Product::class,
            $shopId
        );
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

        $position = $qb->executeQuery()->fetchOne();

        if (!$position) {
            return null;
        }

        return (int) $position;
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

        $results = $qb->executeQuery()->fetchAssociative();

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

        return $qb->executeQuery()->fetchAllAssociative();
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

        return $qb->executeQuery()->fetchAllAssociative();
    }

    public function getProductTaxRulesGroupId(ProductId $productId, ShopId $shopId): TaxRulesGroupId
    {
        $result = $this->connection->createQueryBuilder()
            ->addSelect('p_shop.id_tax_rules_group')
            ->from($this->dbPrefix . 'product_shop', 'p_shop')
            ->where('p_shop.id_product = :productId')
            ->andWhere('p_shop.id_shop = :shopId')
            ->setParameter('shopId', $shopId->getValue())
            ->setParameter('productId', $productId->getValue())
            ->executeQuery()
            ->fetchOne()
        ;

        return new TaxRulesGroupId((int) $result);
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

        $qb->where($qb->expr()->or(
            $qb->expr()->like('pl.name', ':dbSearchPhrase'),

            // Product references
            $qb->expr()->like('p.isbn', ':dbSearchPhrase'),
            $qb->expr()->like('p.upc', ':dbSearchPhrase'),
            $qb->expr()->like('p.mpn', ':dbSearchPhrase'),
            $qb->expr()->like('p.reference', ':dbSearchPhrase'),
            $qb->expr()->like('p.ean13', ':dbSearchPhrase'),
            $qb->expr()->like('p.supplier_reference', ':dbSearchPhrase'),

            // Combination attributes
            $qb->expr()->like('pa.isbn', ':dbSearchPhrase'),
            $qb->expr()->like('pa.upc', ':dbSearchPhrase'),
            $qb->expr()->like('pa.mpn', ':dbSearchPhrase'),
            $qb->expr()->like('pa.reference', ':dbSearchPhrase'),
            $qb->expr()->like('pa.ean13', ':dbSearchPhrase'),
            $qb->expr()->like('pa.supplier_reference', ':dbSearchPhrase')
        ));
        $dbSearchPhrase = sprintf('%%%s%%', $searchPhrase);
        $qb->setParameter('dbSearchPhrase', $dbSearchPhrase);

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

    private function getProductByShopGroup(ProductId $productId, ShopGroupId $shopGroupId): Product
    {
        $groupDefaultShopId = $this->getProductDefaultShopIdForGroup($productId, $shopGroupId);

        return $this->getProductByShopId($productId, $groupDefaultShopId);
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
            $shopId,
            ProductShopAssociationNotFoundException::class
        );

        return $this->loadProduct($product);
    }

    /**
     * Returns a single shop ID when the constraint is a single shop, and the list of shops associated to the product
     * when the constraint is for all shops (shop group constraint is forbidden)
     *
     * @param ProductId $productId
     * @param ShopConstraint $shopConstraint
     *
     * @return ShopId[]
     */
    public function getShopIdsByConstraint(ProductId $productId, ShopConstraint $shopConstraint): array
    {
        if ($shopConstraint->getShopGroupId()) {
            return $this->getAssociatedShopIdsFromGroup($productId, $shopConstraint->getShopGroupId());
        }

        if ($shopConstraint->forAllShops()) {
            return $this->getAssociatedShopIds($productId);
        }

        return [$shopConstraint->getShopId()];
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
     * This override was needed because of the extra parameter in product constructor
     *
     * {@inheritDoc}
     */
    protected function constructObjectModel(int $id, string $objectModelClass, ?int $shopId): ObjectModel
    {
        return new Product($id, false, null, $shopId);
    }
}
