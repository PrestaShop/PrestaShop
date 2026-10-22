<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CartRule;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

/**
 * Takes and gives back units of the total available quantity of a cart rule.
 *
 * Each change is a single conditional UPDATE, so the database decides which order gets
 * the last available unit, and the quantity can never drop below zero.
 * Cart rules with no quantity limit (NULL quantity) are never changed.
 */
final class CartRuleQuantityReserver
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * Takes one unit of the available quantity, for a cart rule with a limited quantity.
     *
     * @return bool false when no unit is left (or when the cart rule has no quantity limit)
     */
    public function reserve(int $cartRuleId): bool
    {
        $affectedRows = $this->connection->executeStatement(
            'UPDATE `' . $this->dbPrefix . 'cart_rule`
            SET `quantity` = `quantity` - 1, `date_upd` = :dateUpd
            WHERE `id_cart_rule` = :cartRuleId AND `quantity` > 0',
            [
                'cartRuleId' => $cartRuleId,
                'dateUpd' => date('Y-m-d H:i:s'),
            ]
        );

        return $affectedRows > 0;
    }

    /**
     * Gives back one unit taken by reserve().
     */
    public function release(int $cartRuleId): void
    {
        $this->connection->executeStatement(
            'UPDATE `' . $this->dbPrefix . 'cart_rule`
            SET `quantity` = `quantity` + 1, `date_upd` = :dateUpd
            WHERE `id_cart_rule` = :cartRuleId AND `quantity` IS NOT NULL',
            [
                'cartRuleId' => $cartRuleId,
                'dateUpd' => date('Y-m-d H:i:s'),
            ]
        );
    }

    /**
     * Returns the cart rules saved on the given orders.
     *
     * @param int[] $orderIds
     *
     * @return int[]
     */
    public function getCartRuleIdsKeptByOrders(array $orderIds): array
    {
        if (empty($orderIds)) {
            return [];
        }

        $cartRuleIds = $this->connection->fetchFirstColumn(
            'SELECT DISTINCT `id_cart_rule` FROM `' . $this->dbPrefix . 'order_cart_rule`
            WHERE `id_order` IN (:orderIds) AND `deleted` = 0',
            ['orderIds' => $orderIds],
            ['orderIds' => ArrayParameterType::INTEGER]
        );

        return array_map('intval', $cartRuleIds);
    }
}
