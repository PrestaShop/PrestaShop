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
use ObjectModel;
use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Manufacturer\Repository\ManufacturerRepository;
use PrestaShop\PrestaShop\Adapter\Product\Validate\ProductValidator;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRulesGroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierReferenceId;
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

/**
 * @todo: This class has been added while we progressively migrate each domain to multishop It contains the new
 *        dedicated function bound with multishop When everything has been migrated they will be moved back to
 *        the initial ProductRepository and single shop methods should be removed But since this will be done
 *        in several PRs for now it's easier to separate them into two services
 *        This is why a lot of code is duplicated between the two classes but don't worry this one is only temporary
 */
class ProductMultiShopRepository extends AbstractMultiShopObjectModelRepository
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

        $result = $qb->execute()->fetchAssociative();
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

        $result = $qb->execute()->fetchAllAssociative();
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
            ->execute()
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
        }, $qb->execute()->fetchAllAssociative());
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
        }, $qb->execute()->fetchAllAssociative());
    }

    /**
     * @param ProductId $productId
     * @param ShopId[] $shopIds
     *
     * @throws ShopAssociationNotFound
     */
    public function deleteFromShops(ProductId $productId, array $shopIds): void
    {
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
            ->execute()
            ->fetchOne()
        ;

        return !empty($result);
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
