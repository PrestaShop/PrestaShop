<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * Class DbPDOCore.
 *
 * @since 1.5.0.1
 */
class DbPDOCore extends Db
{
    /** @var PDO */
    protected $link;

    /** @var PDOStatement */
    protected $result;

    /**
     * Returns a new PDO object (database link).
     *
     * @deprecated use getPDO
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $dbname
     * @param int $timeout
     *
     * @return PDO
     */
    protected static function _getPDO($host, $user, $password, $dbname, $timeout = 5)
    {
        return static::getPDO($host, $user, $host, $dbname, $timeout);
    }

    /**
     * Returns a new PDO object (database link).
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $dbname
     * @param int $timeout
     *
     * @return PDO
     */
    protected static function getPDO($host, $user, $password, $dbname, $timeout = 5)
    {
        $dsn = 'mysql:';
        if ($dbname) {
            $dsn .= 'dbname=' . $dbname . ';';
        }
        if (preg_match('/^(.*):([0-9]+)$/', $host, $matches)) {
            $dsn .= 'host=' . $matches[1] . ';port=' . $matches[2];
        } elseif (preg_match('#^.*:(/.*)$#', $host, $matches)) {
            $dsn .= 'unix_socket=' . $matches[1];
        } else {
            $dsn .= 'host=' . $host;
        }
        $dsn .= ';charset=utf8mb4';

        return new PDO(
            $dsn,
            $user,
            $password,
            [
                PDO::ATTR_TIMEOUT => $timeout,
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
            ]
        );
    }

    /**
     * Tries to connect and create a new database.
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $dbname
     * @param bool $dropit if true, drops the created database
     *
     * @return bool|int
     */
    public static function createDatabase($host, $user, $password, $dbname, $dropit = false)
    {
        try {
            $link = DbPDO::getPDO($host, $user, $password, false);
            $success = $link->exec('CREATE DATABASE `' . str_replace('`', '\\`', $dbname) . '`');
            if ($dropit && ($link->exec('DROP DATABASE `' . str_replace('`', '\\`', $dbname) . '`') !== false)) {
                return true;
            }
        } catch (PDOException $e) {
            return false;
        }

        return $success;
    }

    /**
     * Tries to connect to the database.
     *
     * @see DbCore::connect()
     *
     * @return PDO
     *
     * @throws PrestaShopException
     */
    public function connect()
    {
        try {
            $this->link = $this->getPDO($this->server, $this->user, $this->password, $this->database, 5);
        } catch (PDOException $e) {
            throw new PrestaShopException('Link to database cannot be established: ' . $e->getMessage());
        }

        $this->link->exec('SET SESSION sql_mode = \'\'');

        return $this->link;
    }

    /**
     * Destroys the database connection link.
     *
     * @see DbCore::disconnect()
     */
    public function disconnect()
    {
        unset($this->link);
    }

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object or true/false.
     *
     * @see DbCore::_query()
     *
     * @param string $sql
     *
     * @return PDOStatement
     */
    protected function _query($sql)
    {
        return $this->link->query($sql);
    }

    /**
     * Returns the next row from the result set.
     *
     * @see DbCore::nextRow()
     *
     * @param bool $result
     *
     * @return array|false|null
     */
    public function nextRow($result = false)
    {
        if (!$result) {
            $result = $this->result;
        }

        if (!is_object($result)) {
            return false;
        }

        return $result->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Returns all rows from the result set.
     *
     * @see DbCore::getAll()
     *
     * @param bool $result
     *
     * @return array|false|null
     */
    protected function getAll($result = false)
    {
        if (!$result) {
            $result = $this->result;
        }

        if (!is_object($result)) {
            return false;
        }

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns row count from the result set.
     *
     * @see DbCore::_numRows()
     *
     * @param PDOStatement $result
     *
     * @return int
     */
    protected function _numRows($result)
    {
        return $result->rowCount();
    }

    /**
     * Returns ID of the last inserted row.
     *
     * @see DbCore::Insert_ID()
     *
     * @return string|int
     */
    public function Insert_ID()
    {
        return $this->link->lastInsertId();
    }

    /**
     * Return the number of rows affected by the last SQL query.
     *
     * @see DbCore::Affected_Rows()
     *
     * @return int
     */
    public function Affected_Rows()
    {
        return $this->result->rowCount();
    }

    /**
     * Returns error message.
     *
     * @see DbCore::getMsgError()
     *
     * @param bool $query
     *
     * @return string
     */
    public function getMsgError($query = false)
    {
        $error = $this->link->errorInfo();

        return ($error[0] == '00000') ? '' : $error[2];
    }

    /**
     * Returns error code.
     *
     * @see DbCore::getNumberError()
     *
     * @return int
     */
    public function getNumberError()
    {
        $error = $this->link->errorInfo();

        return isset($error[1]) ? $error[1] : 0;
    }

    /**
     * Returns database server version.
     *
     * @see DbCore::getVersion()
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->getValue('SELECT VERSION()');
    }

    /**
     * Escapes illegal characters in a string.
     *
     * @see DbCore::_escape()
     *
     * @param string $str
     *
     * @return string
     */
    public function _escape($str)
    {
        $search = ['\\', "\0", "\n", "\r", "\x1a", "'", '"'];
        $replace = ['\\\\', '\\0', '\\n', '\\r', "\Z", "\'", '\"'];

        return str_replace($search, $replace, $str);
    }

    /**
     * Switches to a different database.
     *
     * @see DbCore::set_db()
     *
     * @param string $db_name
     *
     * @return int
     */
    public function set_db($db_name)
    {
        return $this->link->exec('USE ' . pSQL($db_name));
    }

    /**
     * Try a connection to the database and check if at least one table with same prefix exists.
     *
     * @see Db::hasTableWithSamePrefix()
     *
     * @param string $server Server address
     * @param string $user Login for database connection
     * @param string $pwd Password for database connection
     * @param string $db Database name
     * @param string $prefix Tables prefix
     *
     * @return bool
     */
    public static function hasTableWithSamePrefix($server, $user, $pwd, $db, $prefix)
    {
        try {
            $link = DbPDO::getPDO($server, $user, $pwd, $db, 5);
        } catch (PDOException $e) {
            return false;
        }

        $sql = 'SHOW TABLES LIKE \'' . $prefix . '%\'';
        $result = $link->query($sql);

        return (bool) $result->fetch();
    }

    /**
     * Tries to connect to the database and create a table (checking creation privileges).
     *
     * @param string $server
     * @param string $user
     * @param string $pwd
     * @param string $db
     * @param string $prefix
     * @param string|null $engine Table engine
     *
     * @return bool|string True, false or error
     */
    public static function checkCreatePrivilege($server, $user, $pwd, $db, $prefix, $engine = null)
    {
        try {
            $link = DbPDO::getPDO($server, $user, $pwd, $db, 5);
        } catch (PDOException $e) {
            return false;
        }

        $enginesToTest = ['InnoDB', 'MyISAM'];
        if ($engine !== null) {
            $enginesToTest = [$engine];
        }

        foreach ($enginesToTest as $engineToTest) {
            $result = $link->query('
            CREATE TABLE `' . $prefix . 'test` (
                `test` tinyint(1) unsigned NOT NULL
            ) ENGINE=' . $engineToTest);

            if ($result) {
                $link->query('DROP TABLE `' . $prefix . 'test`');

                return true;
            }
        }

        $error = $link->errorInfo();

        return $error[2];
    }

    /**
     * Tries to connect to the database and select content (checking select privileges).
     *
     * @param string $server
     * @param string $user
     * @param string $pwd
     * @param string $db
     * @param string $prefix
     * @param string|null $engine Table engine
     *
     * @return bool|string True, false or error
     */
    public static function checkSelectPrivilege($server, $user, $pwd, $db, $prefix, $engine = null)
    {
        try {
            $link = DbPDO::getPDO($server, $user, $pwd, $db, 5);
        } catch (PDOException $e) {
            return false;
        }

        if ($engine === null) {
            $engine = 'MyISAM';
        }

        // Create a table
        $link->query('
		CREATE TABLE `' . $prefix . 'test` (
			`test` tinyint(1) unsigned NOT NULL
		) ENGINE=' . $engine);

        // Select content
        $result = $link->query('SELECT * FROM `' . $prefix . 'test`');

        // Drop the table
        $link->query('DROP TABLE `' . $prefix . 'test`');

        if (!$result) {
            $error = $link->errorInfo();

            return $error[2];
        }

        return true;
    }

    /**
     * Try a connection to the database.
     *
     * @see Db::checkConnection()
     *
     * @param string $server Server address
     * @param string $user Login for database connection
     * @param string $pwd Password for database connection
     * @param string $db Database name
     * @param bool $new_db_link
     * @param string|bool $engine
     * @param int $timeout
     *
     * @return int Error code or 0 if connection was successful
     */
    public static function tryToConnect($server, $user, $pwd, $db, $new_db_link = true, $engine = null, $timeout = 5)
    {
        try {
            $link = DbPDO::getPDO($server, $user, $pwd, $db, $timeout);
        } catch (PDOException $e) {
            // hhvm wrongly reports error status 42000 when the database does not exist - might change in the future
            return ($e->getCode() == 1049 || (defined('HHVM_VERSION') && $e->getCode() == 42000)) ? 2 : 1;
        }
        unset($link);

        return 0;
    }

    /**
     * Selects best table engine.
     *
     * @return string
     */
    public function getBestEngine()
    {
        $value = 'InnoDB';

        $sql = 'SHOW VARIABLES WHERE Variable_name = \'have_innodb\'';
        $result = $this->link->query($sql);

        if (!$result) {
            $value = 'MyISAM';
        } else {
            $row = $result->fetch();
            if (!$row || strtolower($row['Value']) != 'yes') {
                $value = 'MyISAM';
            }
        }

        /* MySQL >= 5.6 */
        $sql = 'SHOW ENGINES';
        $result = $this->link->query($sql);
        while ($row = $result->fetch()) {
            if ($row['Engine'] == 'InnoDB') {
                if (in_array($row['Support'], ['DEFAULT', 'YES'])) {
                    $value = 'InnoDB';
                }

                break;
            }
        }

        return $value;
    }

    /**
     * Try a connection to the database and set names to UTF-8.
     *
     * @see Db::checkEncoding()
     *
     * @param string $server Server address
     * @param string $user Login for database connection
     * @param string $pwd Password for database connection
     *
     * @return bool
     */
    public static function tryUTF8($server, $user, $pwd)
    {
        try {
            $link = DbPDO::getPDO($server, $user, $pwd, false, 5);
        } catch (PDOException $e) {
            return false;
        }
        $result = $link->exec('SET NAMES utf8mb4');
        unset($link);

        return ($result === false) ? false : true;
    }
}
