<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\TradeCredit\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Module\Configuration\TradeCreditPaymentModuleConfiguratorInterface;

/**
 * Stores which payment modules charge the customer trade credit, per shop
 * (table module_trade_credit, same shape as module_group / module_carrier).
 */
class TradeCreditPaymentModuleRepository implements TradeCreditPaymentModuleConfiguratorInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix
    ) {
    }

    /**
     * @return int[]
     */
    public function getTradeCreditModuleIds(int $shopId): array
    {
        return array_map('intval', $this->connection->fetchFirstColumn(
            'SELECT id_module FROM ' . $this->table() . ' WHERE id_shop = ? ORDER BY id_module',
            [$shopId]
        ));
    }

    public function isTradeCreditModule(int $moduleId, int $shopId): bool
    {
        return false !== $this->connection->fetchOne(
            'SELECT 1 FROM ' . $this->table() . ' WHERE id_module = ? AND id_shop = ?',
            [$moduleId, $shopId]
        );
    }

    /**
     * Replaces the trade credit payment modules of the shop.
     *
     * @param int[] $moduleIds
     */
    public function setTradeCreditModules(int $shopId, array $moduleIds): void
    {
        $this->connection->transactional(function (Connection $connection) use ($shopId, $moduleIds): void {
            $connection->executeStatement('DELETE FROM ' . $this->table() . ' WHERE id_shop = ?', [$shopId]);

            foreach (array_unique(array_map('intval', $moduleIds)) as $moduleId) {
                $connection->insert($this->table(), ['id_module' => $moduleId, 'id_shop' => $shopId]);
            }
        });
    }

    public function deleteForModule(int $moduleId): void
    {
        $this->connection->executeStatement('DELETE FROM ' . $this->table() . ' WHERE id_module = ?', [$moduleId]);
    }

    private function table(): string
    {
        return $this->dbPrefix . 'module_trade_credit';
    }
}
