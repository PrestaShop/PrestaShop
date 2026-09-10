<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Service\Database;

use Doctrine\ORM\EntityManager;
use PrestaShop\PrestaShop\Adapter\ConnectionSwitcher;
use PrestaShop\PrestaShop\Core\Repository\TransactionManagerInterface;
use Throwable;

class TransactionManager implements TransactionManagerInterface
{
    /**
     * @param EntityManager $entityManager
     */
    public function __construct(private readonly EntityManager $entityManager, private readonly ConnectionSwitcher $connectionSwitcher)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function rollback(): void
    {
        $this->entityManager->rollback();
    }

    /**
     * {@inheritdoc}
     */
    public function commit(): void
    {
        $this->entityManager->commit();
    }

    /**
     * The legacy Db connection is synchronized here too (not just in executeInTransaction()), otherwise
     * code using this begin/commit/rollback trio instead of executeInTransaction() would silently lose
     * the single-connection guarantee: legacy writes would go back to running on their own connection.
     *
     * {@inheritdoc}
     */
    public function beginTransaction(): void
    {
        $this->connectionSwitcher->switchConnection();
        $this->entityManager->beginTransaction();
    }

    /**
     * Executes a callable within a single database transaction covering both legacy Db and Doctrine EntityManager
     * operations.
     *
     * The legacy Db connection is synchronized beforehand (see ConnectionSwitcher::switchConnection()) to share the same
     * underlying PDO connection as Doctrine, so both layers participate in the very same physical transaction
     * instead of two independent ones. This avoids the deadlocks and lock wait timeouts that a dual-connection,
     * dual-transaction approach would be exposed to.
     *
     * Transaction control is deliberately kept at the DBAL connection level instead of delegating to
     * EntityManager::wrapInTransaction(): that helper calls EntityManager::close() on any failure, and PrestaShop
     * command handlers throw domain exceptions as ordinary control flow (a BO controller catches them and re-renders
     * the page). Closing the manager would make every later Doctrine call in the same request fail with "The
     * EntityManager is closed" after what is, for the caller, a plain validation error. The connection is also the
     * layer that actually matters here, since that is what the legacy Db instance shares - and it keeps this method
     * consistent with the beginTransaction()/commit()/rollback() trio above, which delegates to the same primitives.
     *
     * Note that flush() covers the whole unit of work, not only what $func touched: that is inherent to Doctrine's
     * UoW, and is the behaviour wrapInTransaction() already had.
     *
     * {@inheritdoc}
     */
    public function executeInTransaction(callable $func): mixed
    {
        $this->connectionSwitcher->switchConnection();

        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            $result = $func($this->entityManager);
            $this->entityManager->flush();
            $connection->commit();

            return $result;
        } catch (Throwable $throwable) {
            // Guarded like wrapInTransaction() does: a DDL statement inside $func implicitly commits on
            // MySQL, so the transaction may already be gone by the time we get here.
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }

            throw $throwable;
        }
    }
}
