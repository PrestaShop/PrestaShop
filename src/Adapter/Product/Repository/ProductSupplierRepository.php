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
use PrestaShop\PrestaShop\Adapter\Product\Validate\ProductSupplierValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\CannotAddProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\CannotBulkDeleteProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\CannotDeleteProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\CannotUpdateProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use ProductSupplier;

/**
 * Methods for accessing ProductSupplier data source
 */
class ProductSupplierRepository extends AbstractObjectModelRepository
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
     * @var ProductSupplierValidator
     */
    private $productSupplierValidator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param ProductSupplierValidator $productSupplierValidator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        ProductSupplierValidator $productSupplierValidator
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->productSupplierValidator = $productSupplierValidator;
    }

    /**
     * @param ProductSupplierId $productSupplierId
     *
     * @return ProductSupplier
     *
     * @throws ProductSupplierNotFoundException
     */
    public function get(ProductSupplierId $productSupplierId): ProductSupplier
    {
        /** @var ProductSupplier $productSupplier */
        $productSupplier = $this->getObjectModel(
            $productSupplierId->getValue(),
            ProductSupplier::class,
            ProductSupplierNotFoundException::class
        );

        return $productSupplier;
    }

    /**
     * @param ProductSupplier $productSupplier
     * @param int $errorCode
     *
     * @return ProductSupplierId
     *
     * @throws CannotAddProductSupplierException
     */
    public function add(ProductSupplier $productSupplier, int $errorCode = 0): ProductSupplierId
    {
        $this->productSupplierValidator->validate($productSupplier);
        $id = $this->addObjectModel($productSupplier, CannotAddProductSupplierException::class, $errorCode);

        return new ProductSupplierId($id);
    }

    /**
     * @param ProductSupplier $productSupplier
     *
     * @throws CannotUpdateProductSupplierException
     */
    public function update(ProductSupplier $productSupplier): void
    {
        $this->productSupplierValidator->validate($productSupplier);
        $this->updateObjectModel($productSupplier, CannotUpdateProductSupplierException::class);
    }

    /**
     * @param ProductSupplierId $productSupplierId
     *
     * @throws ProductSupplierNotFoundException
     */
    public function delete(ProductSupplierId $productSupplierId): void
    {
        $this->deleteObjectModel($this->get($productSupplierId), CannotDeleteProductSupplierException::class);
    }

    /**
     * @param array $productSupplierIds
     *
     * @throws CannotBulkDeleteProductSupplierException
     */
    public function bulkDelete(array $productSupplierIds): void
    {
        $failedIds = [];
        foreach ($productSupplierIds as $productSupplierId) {
            try {
                $this->delete($productSupplierId);
            } catch (CannotDeleteProductSupplierException $e) {
                $failedIds[] = $productSupplierId->getValue();
            }
        }

        if (empty($failedIds)) {
            return;
        }

        throw new CannotBulkDeleteProductSupplierException($failedIds, sprintf(
            'Failed to delete following product suppliers: %s',
            implode(', ', $failedIds)
        ));
    }

    /**
     * @param ProductId $productId
     * @param bool $noCombination
     *
     * @return array
     */
    public function getProductSuppliersInfo(ProductId $productId, bool $noCombination = true): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('*')
            ->from($this->dbPrefix . 'product_supplier', 'ps')
            ->leftJoin(
                'ps',
                $this->dbPrefix . 'supplier',
                's',
                'ps.id_supplier = s.id_supplier'
            )
            ->where('ps.id_product = :productId')
            ->setParameter('productId', $productId->getValue())
        ;

        if ($noCombination) {
            $qb->andWhere('ps.id_product_attribute = 0');
        }

        return $qb->execute()->fetchAll();
    }
}
