<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Order;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

/**
 * Serializes order creation for one cart across concurrent requests.
 *
 * "Has an order already been placed with this cart?" followed by the INSERT is a check-then-act
 * sequence: without exclusion, N concurrent checkouts of the same cart all pass the check and
 * all create an order. A MySQL named lock is used rather than a Symfony lock store because it is
 * shared by every web node of the shop and needs no schema change.
 */
final class OrderCreationLock
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
        private readonly int $waitTimeoutInSeconds = 10,
    ) {
    }

    /**
     * Blocks up to the configured timeout while another request holds the lock for this cart.
     *
     * @return bool false when the lock could not be obtained in time
     */
    public function acquire(int $cartId): bool
    {
        $result = $this->connection->fetchOne(
            'SELECT GET_LOCK(?, ?)',
            [$this->lockName($cartId), $this->waitTimeoutInSeconds],
            [ParameterType::STRING, ParameterType::INTEGER]
        );

        return (int) $result === 1;
    }

    public function release(int $cartId): void
    {
        $this->connection->executeQuery('SELECT RELEASE_LOCK(?)', [$this->lockName($cartId)]);
    }

    /**
     * Named locks are server-wide, so the name is scoped to this shop's database and prefix.
     */
    private function lockName(int $cartId): string
    {
        return sprintf(
            'order_creation_%s_%d',
            md5($this->connection->getDatabase() . $this->dbPrefix),
            $cartId
        );
    }
}
