<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Cache;

use Cache;
use PrestaShop\PrestaShop\Core\Cache\LegacySqlCacheInvalidatorInterface;

/**
 * Invalidates the legacy SQL query cache, the way Db does for the legacy connection.
 */
final class LegacySqlCacheInvalidator implements LegacySqlCacheInvalidatorInterface
{
    /**
     * Anchored at the start on purpose: a read mentioning one of these words further along, in a
     * column name or in a string, changes nothing. The check runs on every statement the connection
     * executes, so it stays a single anchored match rather than parsing the statement.
     */
    private const WRITE_STATEMENT = '/^\s*(?:INSERT|UPDATE|DELETE|REPLACE|TRUNCATE|ALTER|DROP|CREATE|RENAME)\b/i';

    /**
     * @param bool $legacyCacheEnabled the value behind _PS_CACHE_ENABLED_, which is what
     *                                 Db::__construct() reads to decide whether it caches at all
     */
    public function __construct(private readonly bool $legacyCacheEnabled)
    {
    }

    public function shouldInvalidate(string $sql): bool
    {
        return $this->legacyCacheEnabled && preg_match(self::WRITE_STATEMENT, $sql) === 1;
    }

    public function invalidate(string $sql): void
    {
        if (!$this->shouldInvalidate($sql)) {
            return;
        }

        Cache::getInstance()->deleteQuery($sql);
    }
}
