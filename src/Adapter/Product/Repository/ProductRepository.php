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
use PrestaShop\PrestaShop\Adapter\Product\Validate\ProductValidator;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotAddProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotBulkDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDuplicateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
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
     * @param Connection $connection
     * @param string $dbPrefix
     * @param ProductValidator $productValidator
     * @param int $defaultCategoryId
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        ProductValidator $productValidator,
        int $defaultCategoryId
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->productValidator = $productValidator;
        $this->defaultCategoryId = $defaultCategoryId;
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
            ->setParameter('productIds', $ids, Connection::PARAM_INT_ARRAY);

        $results = $qb->execute()->fetch();

        if (!$results || (int) $results['product_count'] !== count($ids)) {
            throw new ProductNotFoundException(sprintf(
                'Some of these products does not exist: %s',
                implode(',', $ids)
            ));
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
     * @param bool $isVirtual
     *
     * @return Product
     *
     * @throws CannotAddProductException
     */
    public function create(array $localizedNames, bool $isVirtual): Product
    {
        $product = new Product();
        $product->active = false;
        $product->id_category_default = $this->defaultCategoryId;
        $product->name = $localizedNames;
        $product->is_virtual = $isVirtual;

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
     * @param string $query
     * @param LanguageId $languageId
     * @param bool $includePacks include product packs in results
     * @param bool $includeVirtualProducts include virtual products in results
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array<int, array<string, mixed>>
     *
     * @todo: or should dedicated methods be implemented for each case? (related products, for packing, for seo)?
     * @todo: decide how to refactor this. Should this be some "search" method with filters etc (maybe moved out to service then)
     */
    public function searchByNameAndReference(
        string $query,
        LanguageId $languageId,
        bool $includePacks = true,
        //@todo: virtual products seems to be not supported to add in a pack
        bool $includeVirtualProducts = true,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        if ('' === $query) {
            return [];
        }

        //@todo: shop association not handled
        $qb = $this->connection->createQueryBuilder();
        $qb->select('p.id_product, p.reference, pl.name, pl.link_rewrite, i.id_image, p.cache_default_attribute, p.cache_is_pack, p.is_virtual')
            ->from($this->dbPrefix . 'product', 'p')
            ->leftJoin(
                'p',
                $this->dbPrefix . 'product_lang',
                'pl',
                'p.id_product = pl.id_product AND pl.id_lang = :langId'
            )->leftJoin(
                'p',
                $this->dbPrefix . 'image',
                'i',
                'p.id_product = i.id_product AND i.cover = 1'
            )
            ->setParameter('langId', $languageId->getValue())
            ->where('pl.name LIKE :searchQuery')
            ->orWhere('p.reference LIKE :searchQuery')
            ->setParameter('searchQuery', '%' . $query . '%')
        ;

        if (!$includePacks) {
            $qb->andWhere('p.cache_is_pack = 0');
        }

        if (!$includeVirtualProducts) {
            $qb->andWhere('p.is_virtual = 0');
        }

        $results = $qb->setFirstResult($offset)
            ->setMaxResults($limit)
            ->execute()
            ->fetchAll()
        ;

        if (!$results) {
            return [];
        }

        return $results;
    }

    /**
     * @param ProductId $productId
     * @param LanguageId $languageId
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array<string, array<string, mixed>>
     */
    public function getCombinations(ProductId $productId, LanguageId $languageId, ?int $limit = null, ?int $offset = null): array
    {
        //@todo; multistore not considered
        $combinationIds = $this->getCombinationIds($productId, $languageId, $limit, $offset);

        if (!$combinationIds) {
            return [];
        }

        $qb = $this->connection->createQueryBuilder();
        $qb->select('pa.id_product_attribute, pa.reference, ag.id_attribute_group, pai.id_image, agl.name AS group_name, al.name AS attribute_name, a.id_attribute')
            ->from($this->dbPrefix . 'product_attribute', 'pa')
            ->setParameter('langId', $languageId->getValue())
            ->setParameter('productId', $productId->getValue())
            ->leftJoin(
                'pa',
                $this->dbPrefix . 'product_attribute_combination',
                'pac',
                'pac.id_product_attribute = pa.id_product_attribute'
            )->leftJoin(
                'pac',
                $this->dbPrefix . 'attribute',
                'a',
                'a.id_attribute = pac.id_attribute'
            )->leftJoin(
                'a',
                $this->dbPrefix . 'attribute_group',
                'ag',
                'ag.id_attribute_group = a.id_attribute_group'
            )->leftJoin(
                'ag',
                $this->dbPrefix . 'attribute_lang',
                'al',
                'a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = :langId'
            )->leftJoin(
                'al',
                $this->dbPrefix . 'attribute_group_lang',
                'agl',
                'ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = :langId'
            )->leftJoin(
                'pa',
                $this->dbPrefix . 'product_attribute_image',
                'pai',
                'pai.id_product_attribute = pa.id_product_attribute'
            )->where('pa.id_product = :productId')
            ->where('pa.id_product_attribute IN (:combinationIds)')
            ->setParameter('combinationIds', $combinationIds, Connection::PARAM_INT_ARRAY)
            ->groupBy('pa.id_product_attribute', 'ag.id_attribute_group')
            ->orderBy('pa.id_product_attribute')
        ;

        $combinations = [];
        foreach ($qb->execute()->fetchAll() as $result) {
            $combinationId = (int) $result['id_product_attribute'];
            $combinations[$combinationId]['id_product_attribute'] = $combinationId;
            $combinations[$combinationId]['id_image'] = (int) $result['id_image'];
            $combinations[$combinationId]['reference'] = $result['reference'];
            $combinations[$combinationId]['attributes'][] = [
                'id_attribute_group' => (int) $result['id_attribute_group'],
                'group_name' => $result['group_name'],
                'id_attribute' => (int) $result['id_attribute'],
                'attribute_name' => $result['attribute_name'],
            ];
        }

        return $combinations;
    }

    /**
     * @param ProductId $productId
     * @param LanguageId $languageId
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return int[]
     */
    private function getCombinationIds(ProductId $productId, LanguageId $languageId, ?int $limit = null, ?int $offset = null): array
    {
        //@todo: shop association not handled
        $qb = $this->connection->createQueryBuilder();
        $qb->select('pa.id_product_attribute')
            ->from($this->dbPrefix . 'product_attribute', 'pa')
            ->setParameter('langId', $languageId->getValue())
            ->setParameter('productId', $productId->getValue())
            ->where('pa.id_product = :productId')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;
        $combinationIds = $qb->execute()->fetchAll();

        if (empty($combinationIds)) {
            return [];
        }

        return array_map(function (array $result): int {
            return (int) $result['id_product_attribute'];
        }, $combinationIds);
    }
}
