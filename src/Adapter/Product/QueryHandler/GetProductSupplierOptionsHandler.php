<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductSupplierHandler;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductSupplierRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryHandler\GetProductSupplierOptionsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * Handles @see GetProductSupplierOptions query
 */
#[AsQueryHandler]
class GetProductSupplierOptionsHandler extends AbstractProductSupplierHandler implements GetProductSupplierOptionsHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param ProductSupplierRepository $productSupplierRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        ProductSupplierRepository $productSupplierRepository,
        ProductRepository $productRepository
    ) {
        parent::__construct($productSupplierRepository);
        $this->productRepository = $productRepository;
    }

    /**
     * @param GetProductSupplierOptions $query
     *
     * @return ProductSupplierOptions
     */
    public function handle(GetProductSupplierOptions $query): ProductSupplierOptions
    {
        $defaultSupplier = $this->productSupplierRepository->getDefaultSupplierId($query->getProductId());
        $supplierIds = $this->productSupplierRepository->getAssociatedSupplierIds($query->getProductId());
        $productType = $this->productRepository->getProductType($query->getProductId());
        $productSuppliers = [];
        if ($productType->getValue() !== ProductType::TYPE_COMBINATIONS) {
            $productSuppliers = $this->getProductSuppliersInfo($query->getProductId());
        }
        $supplierIntIds = array_map(function (SupplierId $supplierId) {
            return $supplierId->getValue();
        }, $supplierIds);

        return new ProductSupplierOptions(
            null !== $defaultSupplier ? $defaultSupplier->getValue() : 0,
            $supplierIntIds,
            $productSuppliers
        );
    }
}
