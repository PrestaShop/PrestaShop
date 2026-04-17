<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Supplier\Repository;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use Supplier;

/**
 * Methods to access Supplier data source
 */
class SupplierRepository extends AbstractObjectModelRepository
{
    /**
     * @param SupplierId $supplierId
     *
     * @throws SupplierNotFoundException
     */
    public function assertSupplierExists(SupplierId $supplierId): void
    {
        $this->assertObjectModelExists($supplierId->getValue(), 'supplier', SupplierNotFoundException::class);
    }

    /**
     * @param SupplierId $supplierId
     *
     * @return Supplier
     *
     * @throws SupplierNotFoundException
     */
    public function get(SupplierId $supplierId): Supplier
    {
        /** @var Supplier $supplier */
        $supplier = $this->getObjectModel(
            $supplierId->getValue(),
            Supplier::class,
            SupplierNotFoundException::class
        );

        return $supplier;
    }
}
