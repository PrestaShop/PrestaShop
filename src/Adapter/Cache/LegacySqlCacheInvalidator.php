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

    private readonly bool $legacyCacheEnabled;

    /**
     * WHY the loose parameter type: %ps_cache_enable% comes from the generated app/config/parameters.yml,
     * which is written by the installer rather than by the project, so it reaches the container as a
     * bool, as 0/1, or as null when the key is present but empty. config/bootstrap.php treats it the
     * same loose way - it assigns the int 0 on the test and upgrade paths - so normalise here instead of
     * demanding a strict bool the configuration cannot promise.
     *
     * @param bool|int|string|null $legacyCacheEnabled the value behind _PS_CACHE_ENABLED_, which is what
     *                                                 Db::__construct() reads to decide whether it caches
     */
    public function __construct(bool|int|string|null $legacyCacheEnabled)
    {
        $this->legacyCacheEnabled = (bool) $legacyCacheEnabled;
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
