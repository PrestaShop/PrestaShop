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

namespace PrestaShop\PrestaShop\Adapter\Order;

use Db;

/**
 * Serializes order creation for one cart across concurrent requests.
 *
 * "Has an order already been placed with this cart?" followed by the INSERT is a check-then-act
 * sequence: without exclusion, N concurrent checkouts of the same cart all pass the check and
 * all create an order. A MySQL named lock is used because it is shared by every web node of the
 * shop and needs no schema change. The legacy Db is the only database access available from the
 * front-office container where PaymentModule runs.
 */
final class OrderCreationLock
{
    /**
     * @var Db
     */
    private $db;

    /**
     * @var string
     */
    private $dbName;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var int
     */
    private $waitTimeoutInSeconds;

    public function __construct(Db $db, string $dbName, string $dbPrefix, int $waitTimeoutInSeconds = 10)
    {
        $this->db = $db;
        $this->dbName = $dbName;
        $this->dbPrefix = $dbPrefix;
        $this->waitTimeoutInSeconds = $waitTimeoutInSeconds;
    }

    /**
     * Blocks up to the configured timeout while another request holds the lock for this cart.
     *
     * @return bool false when the lock could not be obtained in time
     */
    public function acquire(int $cartId): bool
    {
        $result = $this->db->getValue(
            sprintf("SELECT GET_LOCK('%s', %d)", $this->lockName($cartId), $this->waitTimeoutInSeconds),
            false
        );

        return (int) $result === 1;
    }

    public function release(int $cartId): void
    {
        $this->db->getValue(sprintf("SELECT RELEASE_LOCK('%s')", $this->lockName($cartId)), false);
    }

    /**
     * Named locks are server-wide, so the name is scoped to this shop's database and prefix.
     * The name only contains hex digits and an integer, so it is safe to inline in SQL.
     */
    private function lockName(int $cartId): string
    {
        return sprintf('order_creation_%s_%d', md5($this->dbName . $this->dbPrefix), $cartId);
    }
}
