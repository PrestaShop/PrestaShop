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
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Adapter\Product\Validate\ProductValidator;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotBulkDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
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
     * @param Connection $connection
     * @param string $dbPrefix
     * @param ProductValidator $productValidator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        ProductValidator $productValidator
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->productValidator = $productValidator;
    }

    /**
     * @param ProductId $productId
     */
    public function assertProductExists(ProductId $productId): void
    {
        $this->assertObjectModelExists($productId->getValue(), 'product', ProductNotFoundException::class);
    }

    /**
     * @param int[] $productIds
     *
     * @throws ProductNotFoundException
     */
    public function assertProductsExists(array $productIds): void
    {
        //@todo: no shop association. Should it be checked here?
        $ids = array_map(function (ProductId $productId): int {
            return $productId->getValue();
        }, $productIds);

        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(id_product) as product_count')
            ->from($this->dbPrefix . 'product')
            ->where('IN (:productIds)')
            ->setParameter('productId', $ids, Connection::PARAM_INT_ARRAY);

        $results = $qb->execute()->fetchAll();

        if (!$results || (int) $results['product_count'] !== count($ids)) {
            throw new ProductNotFoundException('Some of products does not exist');
        }
    }

    /**
     * @param ProductId $productId
     * @param LanguageId $languageId
     *
     * @return array<string, mixed>
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

        return $product;
    }

    /**
     * @param Product $product
     * @param array $propertiesToUpdate
     * @param int $errorCode
     *
     * @throws CoreException
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
}
