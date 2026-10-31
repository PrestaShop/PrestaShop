<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Doctrine;

use Doctrine\DBAL\Driver\Middleware\AbstractConnectionMiddleware;
use Doctrine\DBAL\Driver\PDO\Connection;
use PDO;

/**
 * Keeps Doctrine transactions working on the PDO link shared with legacy code, which may
 * open or end transactions with raw SQL (START TRANSACTION, COMMIT, DDL implicit commits).
 */
final class SharedLegacyConnection extends AbstractConnectionMiddleware
{
    private bool $joinedLegacyTransaction = false;

    public function __construct(private readonly PDO $link)
    {
        parent::__construct(new Connection($link));
    }

    /**
     * {@inheritDoc}
     */
    public function beginTransaction()
    {
        if (!$this->isInTransaction()) {
            return parent::beginTransaction();
        }

        $this->exec('SAVEPOINT ' . $this->getSavepointName());
        $this->joinedLegacyTransaction = true;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function commit()
    {
        if ($this->endJoinedLegacyTransaction('RELEASE SAVEPOINT ')) {
            return true;
        }

        // Already committed implicitly by legacy code
        if (!$this->isInTransaction()) {
            return true;
        }

        return parent::commit();
    }

    /**
     * {@inheritDoc}
     */
    public function rollBack()
    {
        if ($this->endJoinedLegacyTransaction('ROLLBACK TO SAVEPOINT ')) {
            return true;
        }

        return parent::rollBack();
    }

    private function endJoinedLegacyTransaction(string $statement): bool
    {
        if (!$this->joinedLegacyTransaction) {
            return false;
        }

        $this->joinedLegacyTransaction = false;
        if ($this->isInTransaction()) {
            $this->exec($statement . $this->getSavepointName());
        }

        return true;
    }

    // Unique per instance: several Doctrine connections (one per kernel) can join the same transaction
    private function getSavepointName(): string
    {
        return 'PS_SHARED_CONNECTION_' . spl_object_id($this);
    }

    private function isInTransaction(): bool
    {
        return $this->link->inTransaction();
    }
}
