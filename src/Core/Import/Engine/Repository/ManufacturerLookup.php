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
final class ManufacturerLookup
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * Exact-name lookup (legacy Manufacturer::getIdByName parity).
     */
    public function getManufacturerIdByName(string $name): ?int
    {
        $manufacturerId = $this->connection->fetchOne(
            'SELECT id_manufacturer FROM ' . $this->dbPrefix . 'manufacturer WHERE name = :name ORDER BY id_manufacturer ASC',
            ['name' => $name]
        );

        return false === $manufacturerId ? null : (int) $manufacturerId;
    }

    public function manufacturerExists(int $manufacturerId): bool
    {
        return false !== $this->connection->fetchOne(
            'SELECT 1 FROM ' . $this->dbPrefix . 'manufacturer WHERE id_manufacturer = :manufacturerId',
            ['manufacturerId' => $manufacturerId]
        );
    }
}
