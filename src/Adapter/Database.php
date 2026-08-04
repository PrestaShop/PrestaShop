<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter;

use Db;
use DbPDO;
use DbQuery;
use Doctrine\DBAL\Connection;
use LogicException;
use PDO;
use PrestaShopDatabaseException;

/**
 * Adapter for Db legacy class.
 */
class Database implements \PrestaShop\PrestaShop\Core\Foundation\Database\DatabaseInterface
{
    /**
     * The connection is optional: this class is also built by PrestaShop\PrestaShop\Core\Foundation\IoC\Container
     * (see Core\ContainerBuilder), a reflection-based container with no notion of Symfony services, which cannot
     * supply one. Without it, getInstance() just skips sharing the connection with Doctrine.
     */
    public function __construct(
        private readonly ?Connection $connection = null,
    ) {
    }

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

    /**
     * Returns the legacy Db instance. When a Doctrine connection was injected, it is synchronized to
     * share the same underlying PDO connection as Doctrine, so that both layers participate in a single
     * physical database transaction. Without one, this just returns the legacy Db instance untouched.
     *
     * @throws LogicException if the configured Doctrine DBAL driver isn't PDO-based, since there would
     *                        then be no native PDO connection to share with the legacy Db instance
     */
    public function getInstance(): DbPDO
    {
        /** @var DbPDO $db */
        $db = Db::getInstance();

        if (null === $this->connection) {
            return $db;
        }

        $nativeConnection = $this->connection->getNativeConnection();
        if (!$nativeConnection instanceof PDO) {
            throw new LogicException(sprintf(
                'Expected the Doctrine DBAL connection to be backed by a PDO instance to share it with the legacy Db, got %s instead.',
                get_debug_type($nativeConnection)
            ));
        }
        $db->setPDO($nativeConnection);

        return $db;
    }
}
