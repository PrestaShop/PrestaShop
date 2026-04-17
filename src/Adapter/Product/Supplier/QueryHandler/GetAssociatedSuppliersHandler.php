<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Supplier\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductSupplierRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetAssociatedSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryHandler\GetAssociatedSuppliersHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\AssociatedSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\NoSupplierId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

#[AsQueryHandler]
class GetAssociatedSuppliersHandler implements GetAssociatedSuppliersHandlerInterface
{
    /**
     * @var ProductSupplierRepository
     */
    private $productSupplierRepository;

    /**
     * @param ProductSupplierRepository $productSupplierRepository
     */
    public function __construct(
        ProductSupplierRepository $productSupplierRepository
    ) {
        $this->productSupplierRepository = $productSupplierRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(GetAssociatedSuppliers $query): AssociatedSuppliers
    {
        $defaultSupplier = $this->productSupplierRepository->getDefaultSupplierId($query->getProductId());
        $supplierIds = $this->productSupplierRepository->getAssociatedSupplierIds($query->getProductId());

        return new AssociatedSuppliers(
            $defaultSupplier ? $defaultSupplier->getValue() : NoSupplierId::NO_SUPPLIER_ID,
            array_map(static function (SupplierId $supplierId): int {
                return $supplierId->getValue();
            }, $supplierIds)
        );
    }
}
