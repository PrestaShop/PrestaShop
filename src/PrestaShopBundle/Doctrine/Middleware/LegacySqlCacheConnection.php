<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Doctrine\Middleware;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Middleware\AbstractConnectionMiddleware;
use Doctrine\DBAL\Driver\Middleware\AbstractStatementMiddleware;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use PrestaShop\PrestaShop\Core\Cache\LegacySqlCacheInvalidatorInterface;

/**
 * Invalidates the legacy SQL query cache for the statements this connection writes.
 *
 * A statement reaches the driver two ways: exec() when it carries no parameter, and
 * prepare() followed by Statement::execute() when it does. Both are covered - the reported case,
 * a Doctrine QueryBuilder update, takes the second one.
 */
final class LegacySqlCacheConnection extends AbstractConnectionMiddleware
{
    public function __construct(
        Connection $connection,
        private readonly LegacySqlCacheInvalidatorInterface $invalidator,
    ) {
        parent::__construct($connection);
    }

    public function exec(string $sql): int
    {
        $affected = parent::exec($sql);
        $this->invalidator->invalidate($sql);

        return $affected;
    }

    public function prepare(string $sql): Statement
    {
        $statement = parent::prepare($sql);

        // Nothing is cached for a read, so a prepared SELECT is handed back untouched.
        if (!$this->invalidator->shouldInvalidate($sql)) {
            return $statement;
        }

        return new class($statement, $this->invalidator, $sql) extends AbstractStatementMiddleware {
            public function __construct(
                Statement $statement,
                private readonly LegacySqlCacheInvalidatorInterface $invalidator,
                private readonly string $sql,
            ) {
                parent::__construct($statement);
            }

            public function execute($params = null): Result
            {
                $result = parent::execute($params);
                $this->invalidator->invalidate($this->sql);

                return $result;
            }
        };
    }
}
