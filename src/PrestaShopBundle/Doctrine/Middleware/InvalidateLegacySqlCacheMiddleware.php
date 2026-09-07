<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Doctrine\Middleware;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Middleware;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;
use PrestaShop\PrestaShop\Core\Cache\LegacySqlCacheInvalidatorInterface;
use SensitiveParameter;

/**
 * Makes writes on the Doctrine connection invalidate the legacy SQL query cache, the way writes on
 * the legacy connection already do.
 *
 * Db::execute() and Db::delete() call Cache::deleteQuery() so that a cached SELECT is dropped as
 * soon as one of its tables changes. Doctrine reaches the same database without going through Db,
 * so a row written by a migrated controller or repository left the legacy cached reads of that
 * table untouched: the back office saved, and the front office kept serving the previous result
 * until the entry was evicted or the whole cache flushed. See issue #18171.
 *
 * This mirrors the legacy Db behaviour for the Symfony/CQRS connection, as
 * SetSessionTimeZoneMiddleware does for the session time zone.
 */
final class InvalidateLegacySqlCacheMiddleware implements Middleware
{
    public function __construct(private readonly LegacySqlCacheInvalidatorInterface $invalidator)
    {
    }

    public function wrap(Driver $driver): Driver
    {
        return new class($driver, $this->invalidator) extends AbstractDriverMiddleware {
            public function __construct(
                Driver $driver,
                private readonly LegacySqlCacheInvalidatorInterface $invalidator,
            ) {
                parent::__construct($driver);
            }

            public function connect(
                #[SensitiveParameter]
                array $params
            ): Connection {
                return new LegacySqlCacheConnection(parent::connect($params), $this->invalidator);
            }
        };
    }
}
