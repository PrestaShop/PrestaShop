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
     * {@inheritdoc}
     */
    public function executeInTransaction(callable $func): mixed
    {
        $this->connectionSwitcher->switchConnection();

        return $this->entityManager->wrapInTransaction($func);
    }
}
