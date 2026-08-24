<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter;

use Db;
use DbQuery;
use PrestaShopDatabaseException;

/**
 * Adapter for Db legacy class.
 */
class Database implements \PrestaShop\PrestaShop\Core\Foundation\Database\DatabaseInterface
{
    /**
     * Perform a SELECT sql statement.
     *
     * @param string $sqlString
     *
     * @return array|false
     *
     * @throws PrestaShopDatabaseException
     */
    public function select($sqlString)
    {
        return Db::getInstance()->executeS($sqlString);
    }

    /**
     * Escape $unsafe to be used into a SQL statement.
     *
     * @param string $unsafeData
     *
     * @return string
     */
    public function escape($unsafeData)
    {
        return Db::getInstance()->escape($unsafeData, true, true);
    }

    /**
     * Returns a value from the first row, first column of a SELECT query.
     *
     * @param string|DbQuery $sql
     * @param bool $useMaster
     * @param bool $useCache
     *
     * @return string|false|null
     */
    public function getValue($sql, $useMaster = true, $useCache = true)
    {
        return Db::getInstance($useMaster)->getValue($sql, $useCache);
    }

    /**
     * Returns the text of the error message from previous database operation.
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return Db::getInstance()->getMsgError();
    }

    /**
     * Enable the cache.
     */
    public function enableCache()
    {
        Db::getInstance()->enableCache();
    }

    /**
     * Disable the cache.
     */
    public function disableCache()
    {
        Db::getInstance()->disableCache();
    }
}
