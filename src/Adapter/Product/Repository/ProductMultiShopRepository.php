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
use PrestaShop\PrestaShop\Adapter\Manufacturer\Repository\ManufacturerRepository;
use PrestaShop\PrestaShop\Adapter\Product\Validate\ProductValidator;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRulesGroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierReferenceId;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\NoManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotAddProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductTaxRulesGroupSettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
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
     * @param ShopConstraint $shopConstraint
     *
     * @return Product
     *
     * @throws CoreException
     */
    public function getByShopConstraint(ProductId $productId, ShopConstraint $shopConstraint): Product
    {
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Product has no features related with shop group use single shop and all shops constraints');
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
        $product = new Product(null, false, null, $shopId->getValue());
        $product->active = false;
        $product->id_category_default = $this->defaultCategoryId;
        $product->is_virtual = ProductType::TYPE_VIRTUAL === $productType;
        $product->cache_is_pack = ProductType::TYPE_PACK === $productType;
        $product->product_type = $productType;
        $product->id_shop_default = $shopId->getValue();
        $product->name = $localizedNames;
        $product->link_rewrite = $localizedLinkRewrites;

        $this->productValidator->validateCreation($product);
        $this->addObjectModelToShop($product, $shopId->getValue(), CannotAddProductException::class);
        $product->addToCategories([$product->id_category_default]);

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
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Product has no features related with shop group use single shop and all shops constraints');
        }

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
        $shopIds = $this->getShopIdsByConstraint($productId, $shopConstraint);
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
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Product has no features related with shop group use single shop and all shops constraints');
        }

        $this->validateProduct($product);
        $shopIds = $this->getShopIdsByConstraint(new ProductId((int) $product->id), $shopConstraint);

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
     * @param ShopConstraint $shopConstraint
     *
     * @return int[]
     */
    private function getShopIdsByConstraint(ProductId $productId, ShopConstraint $shopConstraint): array
    {
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Product has no features related with shop group use single shop and all shops constraints');
        }

        $shopIds = [];
        if ($shopConstraint->forAllShops()) {
            $shops = $this->getAssociatedShopIds($productId);
            foreach ($shops as $shopId) {
                $shopIds[] = $shopId->getValue();
            }
        } else {
            $shopIds = [$shopConstraint->getShopId()->getValue()];
        }

        return $shopIds;
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
