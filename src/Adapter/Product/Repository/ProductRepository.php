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
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Adapter\Manufacturer\Repository\ManufacturerRepository;
use PrestaShop\PrestaShop\Adapter\Product\Validate\ProductValidator;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRulesGroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
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
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
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
        /** @var Product $product */
        $product = $this->getObjectModel(
            $productId->getValue(),
            Product::class,
            ProductNotFoundException::class
        );

        try {
            $product->loadStockData();
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to load Product stock #%d', $productId->getValue()),
                0,
                $e
            );
        }

        return $product;
    }

    /**
     * @param array<int, string> $localizedNames
     * @param string $productType
     *
     * @return Product
     *
     * @throws CannotAddProductException
     */
    public function create(array $localizedNames, string $productType): Product
    {
        $product = new Product();
        $product->active = false;
        $product->id_category_default = $this->defaultCategoryId;
        $product->name = $localizedNames;
        $product->is_virtual = ProductType::TYPE_VIRTUAL === $productType;
        $product->cache_is_pack = ProductType::TYPE_PACK === $productType;
        $product->product_type = $productType;

        $this->productValidator->validateCreation($product);
        $this->addObjectModel($product, CannotAddProductException::class);
        $product->addToCategories([$product->id_category_default]);

        return $product;
    }

    /**
     * @param Product $product
     * @param array $propertiesToUpdate
     * @param int $errorCode
     *
     * @throws CoreException
     * @throws ProductConstraintException
     * @throws ProductPackConstraintException
     * @throws ProductStockConstraintException
     */
    public function partialUpdate(Product $product, array $propertiesToUpdate, int $errorCode): void
    {
        $taxRulesGroupIdIsBeingUpdated = in_array('id_tax_rules_group', $propertiesToUpdate, true);
        $taxRulesGroupId = (int) $product->id_tax_rules_group;
        $manufacturerIdIsBeingUpdated = in_array('id_manufacturer', $propertiesToUpdate, true);
        $manufacturerId = (int) $product->id_manufacturer;

        if ($taxRulesGroupIdIsBeingUpdated && $taxRulesGroupId !== ProductTaxRulesGroupSettings::NONE_APPLIED) {
            $this->taxRulesGroupRepository->assertTaxRulesGroupExists(new TaxRulesGroupId($taxRulesGroupId));
        }
        if ($manufacturerIdIsBeingUpdated && $manufacturerId !== NoManufacturerId::NO_MANUFACTURER_ID) {
            $this->manufacturerRepository->assertManufacturerExists(new ManufacturerId($manufacturerId));
        }

        $this->productValidator->validate($product);
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

        return $qb->execute()->fetchAllAssociative();
    }
}
