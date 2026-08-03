<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\Repository;

use Doctrine\DBAL\Connection;

/**
 * @internal only meant for internal use by the Import engine components,
 *           not to be overridden or decorated
 */
final class SupplierLookup
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * Exact-name lookup (legacy Supplier::getIdByName parity).
     */
    public function getSupplierIdByName(string $name): ?int
    {
        $supplierId = $this->connection->fetchOne(
            'SELECT id_supplier FROM ' . $this->dbPrefix . 'supplier WHERE name = :name ORDER BY id_supplier ASC',
            ['name' => $name]
        );

        return false === $supplierId ? null : (int) $supplierId;
    }

    public function supplierExists(int $supplierId): bool
    {
        return false !== $this->connection->fetchOne(
            'SELECT 1 FROM ' . $this->dbPrefix . 'supplier WHERE id_supplier = :supplierId',
            ['supplierId' => $supplierId]
        );
    }
}
