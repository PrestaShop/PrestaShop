<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Cache;

/**
 * Drops the legacy SQL query cache entries that a statement invalidates.
 *
 * The legacy connection does this itself: Db::execute() and Db::delete() call
 * Cache::deleteQuery() on every write, which is what keeps the SQL query cache
 * (CacheMemcached, CacheApc, CacheFs) consistent with the database. Any other connection to the
 * same database has to honour the same contract.
 */
interface LegacySqlCacheInvalidatorInterface
{
    /**
     * Whether running this statement can make a cached query result wrong.
     *
     * Separate from invalidate() so a caller can skip the work it would otherwise do around a
     * statement, such as decorating it, when the statement only reads.
     */
    public function shouldInvalidate(string $sql): bool;

    /**
     * Drops the cached results of every query reading a table this statement writes to.
     */
    public function invalidate(string $sql): void;
}
