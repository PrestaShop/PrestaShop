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
final class ShopLookup
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * Exact-name lookup (legacy Shop::getIdByName parity).
     */
    public function getShopIdByName(string $name): ?int
    {
        $shopId = $this->connection->fetchOne(
            'SELECT id_shop FROM ' . $this->dbPrefix . 'shop WHERE name = :name ORDER BY id_shop ASC',
            ['name' => $name]
        );

        return false === $shopId ? null : (int) $shopId;
    }
}
