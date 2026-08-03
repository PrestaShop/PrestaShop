<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Service\Database;

use Doctrine\ORM\EntityManager;
use PrestaShop\PrestaShop\Adapter\Database;
use PrestaShop\PrestaShop\Core\Repository\TransactionManagerInterface;
use Throwable;

class TransactionManager implements TransactionManagerInterface
{
    /**
     * @param EntityManager $entityManager
     */
    public function __construct(private readonly EntityManager $entityManager, private readonly Database $database)
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
     * {@inheritdoc}
     */
    public function beginTransaction(): void
    {
        $this->entityManager->beginTransaction();
    }

    /**
     * Executes a callable within a dual-layer database transaction covering both legacy Db and Doctrine EntityManager.
     *
     * Benefits of transactional management:
     * - Guarantees atomicity and data consistency across operations executed in both legacy PrestaShop (Db/ObjectModel) and Doctrine ORM layers.
     * - Ensures that if any failure or exception occurs during execution, all changes made across both layers are rolled back together, preventing partial data updates.
     *
     * Deadlock risks with dual transaction layers:
     * - Managing transactions across two separate database abstraction layers (legacy Db connection and Doctrine EntityManager) increases the risk of database deadlocks.
     * - If legacy SQL queries and Doctrine flush operations access or lock database tables/rows in different orders within the same transaction workflow, or if they use separate DB connections holding uncommitted locks, concurrent executions can trigger deadlocks or lock wait timeouts.
     *
     * {@inheritdoc}
     */
    public function executeInTransaction(callable $func): mixed
    {
        $db = $this->database->getInstance();
        $db->execute('START TRANSACTION');

        try {
            $result = $this->entityManager->wrapInTransaction($func);
            $db->execute('COMMIT');

            return $result;
        } catch (Throwable $e) {
            $db->execute('ROLLBACK');
            throw $e;
        }
    }
}
