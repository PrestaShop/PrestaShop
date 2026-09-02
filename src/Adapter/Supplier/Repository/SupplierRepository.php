<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Supplier\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use Supplier;

/**
 * Methods to access Supplier data source
 */
class SupplierRepository extends AbstractObjectModelRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

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

    /**
     * Exact-name lookup (legacy Supplier::getIdByName parity).
     *
     * supplier.name carries no unique constraint, so EVERY match is returned,
     * ordered by id ASC: callers use the first one and report the count when there
     * is more than one, rather than silently picking the oldest homonym.
     *
     * @return list<int>
     */
    public function getSupplierIdsByName(string $name): array
    {
        $supplierIds = $this->connection->createQueryBuilder()
            ->select('s.id_supplier')
            ->from($this->dbPrefix . 'supplier', 's')
            ->where('s.name = :name')
            ->orderBy('s.id_supplier', 'ASC')
            ->setParameter('name', $name)
            ->executeQuery()
            ->fetchFirstColumn()
        ;

        return array_map('intval', $supplierIds);
    }
}
