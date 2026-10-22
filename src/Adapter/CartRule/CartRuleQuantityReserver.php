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

namespace PrestaShop\PrestaShop\Adapter\CartRule;

use Db;
use PrestaShopDatabaseException;

/**
 * Takes and gives back units of the total available quantity of a cart rule.
 *
 * Each change is a single conditional UPDATE, so the database decides which order gets
 * the last available unit, and the quantity can never drop below zero.
 */
final class CartRuleQuantityReserver
{
    /**
     * Takes one unit of the available quantity.
     *
     * @return bool false when no unit is left
     *
     * @throws PrestaShopDatabaseException when the update fails, so a database error is never read as an exhausted cart rule
     */
    public function reserve(int $cartRuleId): bool
    {
        $db = Db::getInstance();
        $updated = $db->execute(
            'UPDATE `' . _DB_PREFIX_ . 'cart_rule`
            SET `quantity` = `quantity` - 1, `date_upd` = "' . pSQL(date('Y-m-d H:i:s')) . '"
            WHERE `id_cart_rule` = ' . $cartRuleId . ' AND `quantity` > 0'
        );
        if (!$updated) {
            throw new PrestaShopDatabaseException(sprintf('Cannot take a unit of cart rule %d: %s', $cartRuleId, $db->getMsgError()));
        }

        return $db->Affected_Rows() > 0;
    }

    /**
     * Gives back one unit taken by reserve().
     */
    public function release(int $cartRuleId): void
    {
        Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'cart_rule`
            SET `quantity` = `quantity` + 1, `date_upd` = "' . pSQL(date('Y-m-d H:i:s')) . '"
            WHERE `id_cart_rule` = ' . $cartRuleId
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

        // Read from the master server: the order cart rules have just been written
        $rows = Db::getInstance()->executeS(
            'SELECT DISTINCT `id_cart_rule` FROM `' . _DB_PREFIX_ . 'order_cart_rule`
            WHERE `id_order` IN (' . implode(',', array_map('intval', $orderIds)) . ') AND `deleted` = 0',
            true,
            false
        );

        return array_map(static function (array $row): int {
            return (int) $row['id_cart_rule'];
        }, is_array($rows) ? $rows : []);
    }
}
