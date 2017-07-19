<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use Doctrine\DBAL\Configuration as DBALConfiguration;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;

/**
 * Class DbCore
 */
class DbCore
{
    /** @var int Constant used by insert() method */
    const INSERT = 1;

    /** @var int Constant used by insert() method */
    const INSERT_IGNORE = 2;

    /** @var int Constant used by insert() method */
    const REPLACE = 3;

    /** @var int Constant used by insert() method */
    const ON_DUPLICATE_KEY = 4;

    /** @var string Server (eg. localhost) */
    protected $server;

    /**  @var string Database user (eg. root) */
    protected $user;

    /** @var string Database password (eg. can be empty !) */
    protected $password;

    /** @var string Database name */
    protected $database;

    /** @var bool */
    protected $is_cache_enabled;

    /** @var \Doctrine\DBAL\Connection */
    protected $link;

    /** @var PDOStatement|mysqli_result|resource|bool SQL cached result */
    protected $result;

    /** @var array List of DB instances */
    public static $instance = array();

    /** @var array List of server settings */
    public static $_servers = array();

    /** @var null Flag used to load slave servers only once.
     * See loadSlaveServers() method.
     */
    public static $_slave_servers_loaded = null;

    /**
     * Store last executed query
     *
     * @var string
     */
    protected $last_query;

    /**
     * Store hash of the last executed query
     *
     * @var string
     */
    protected $last_query_hash;

    /**
     * Last cached query
     *
     * @var string
     */
    protected $last_cached;

    /**
     * Contains details about the connection to instanciate
     * 
     * @var array
     */
    protected $options;

    /**
     * Instantiates a database connection
     *
     * @param string $server Server address
     * @param string $user User login
     * @param string $password User password
     * @param string $database Database name
     * @param bool $connect If false, don't connect in constructor (since 1.5.0.1)
     */
    public function __construct($server, $user, $password, $database, $connect = true, array $options = array())
    {
        $this->server = $server;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
        $this->is_cache_enabled = (defined('_PS_CACHE_ENABLED_')) ? _PS_CACHE_ENABLED_ : false;

        if (!defined('_PS_DEBUG_SQL_')) {
            define('_PS_DEBUG_SQL_', false);
        }

        if ($connect) {
            $this->connect();
        }

        global $kernel;
        if (!empty($options['dbal_connection'])) {
            dump('doge');
            // ToDo: Hardcoded value to be replaced
            $this->link = $kernel->get('doctrine.dbal.default_connection');
        }
    }

    /**
     * Opens a database connection
     *
     * @return \Doctrine\DBAL\Connection
     */
    public function connect()
    {
        $this->connectionParams = array(
            'host' => $this->server,
            'user' => $this->user,
            'password' => $this->password,
            'driver' => 'pdo_mysql',
            'charset' => 'utf8',
            'server_version' => '5.5',
        );

        if (strpos($this->server, ':')) {
            list($this->connectionParams['host'], $this->connectionParams['port']) = explode(':', $this->server);
        }

        if ($this->database) {
            $this->connectionParams['dbname'] = $this->database;
        }

        $config = new DBALConfiguration();
        $this->link = DriverManager::getConnection($this->connectionParams, $config);

        $this->link->exec('SET SESSION sql_mode = \'\'');

        return $this->link;
    }

    /**
     * Closes database connection
     */
    public function disconnect()
    {
        $this->link->close();
    }

    /**
     * Get the ID generated from the previous INSERT operation
     *
     * @return int|string
     */
    public function Insert_ID()
    {
        return $this->link->lastInsertId();
    }

    /**
     * Get number of affected rows in previous database operation
     *
     * @return int
     */
    public function Affected_Rows()
    {
        return $this->result->rowCount();
    }

    /**
     * Get next row for a query which does not return an array
     *
     * @param PDOStatement|mysqli_result|resource|bool $result
     * @return array|object|false|null
     */
    public function nextRow($result = false)
    {
        if (!$result) {
            $result = $this->result;
        }

        if (!is_object($result)) {
            return false;
        }

        return $result->fetch();
    }

    /**
     * Get all rows for a query which return an array
     *
     * @param PDOStatement|mysqli_result|resource|bool|null $result
     * @return array
     */
    protected function getAll($result = false)
    {
        if (!$result) {
            $result = $this->result;
        }

        if (!is_object($result)) {
            return false;
        }

        return $result->fetchAll();
    }

    /**
     * Get database version
     *
     * @return string
     */
    public function getVersion()
    {
        // ToDo: Check if best answer
        return $this->getValue('SELECT VERSION()');
    }

    /**
     * Protect string against SQL injections
     *
     * @param string $str
     * @return string
     */
    public function _escape($str)
    {
        $search = array("\\", "\0", "\n", "\r", "\x1a", "'", '"');
        $replace = array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"');
        return str_replace($search, $replace, $str);
    }

    /**
     * Returns the text of the error message from previous database operation
     *
     * @return string
     */
    public function getMsgError()
    {
        $error = $this->link->errorInfo();
        return ($error[0] == '00000') ? '' : $error[2];
    }

    /**
     * Returns the number of the error from previous database operation
     *
     * @return int
     */
    public function getNumberError()
    {
        // ToDo: check function called, errorCode() should exists
        $error = $this->link->errorInfo();
        return isset($error[1]) ? $error[1] : 0;
    }

    /**
     * Sets the current active database on the server that's associated with the specified link identifier.
     * Do not remove, useful for some modules.
     *
     * @param string $dbName
     * @return bool|int
     */
    public function set_db($dbName)
    {
        $this->query('use '.$dbName);
    }

    /**
     * Returns database object instance.
     *
     * @param bool $master Decides whether the connection to be returned by the master server or the slave server
     * @return Db Singleton instance of Db object
     */
    public static function getInstance($master = true)
    {
        static $id = 0;

        // This MUST not be declared with the class members because some defines (like _DB_SERVER_) may not exist yet (the constructor can be called directly with params)
        if (!self::$_servers) {
            self::$_servers = array(
                array('server' => _DB_SERVER_, 'user' => _DB_USER_, 'password' => _DB_PASSWD_, 'database' => _DB_NAME_, 'name' => 'default'), /* MySQL Master server */
            );
        }

        if (!$master) {
            Db::loadSlaveServers();
        }

        $total_servers = count(self::$_servers);
        if ($master || $total_servers == 1) {
            $id_server = 0;
        } else {
            $id++;
            $id_server = ($total_servers > 2 && ($id % $total_servers) != 0) ? $id % $total_servers : 1;
        }

        if (!isset(self::$instance[$id_server])) {
            $options = array();
            global $kernel;
            // To be checked, it seems it is not instanciated yet
            $sfFound = ($kernel instanceof \Symfony\Component\HttpKernel\KernelInterface);

            if ($sfFound && !empty(self::$_servers[$id_server]['name'])) {
                $options['dbal_connection'] = self::$_servers[$id_server]['name'];
            }

            $class = Db::getClass();
            self::$instance[$id_server] = new $class(
                self::$_servers[$id_server]['server'],
                self::$_servers[$id_server]['user'],
                self::$_servers[$id_server]['password'],
                self::$_servers[$id_server]['database'],
                !$sfFound, // Do not connect if container service exists
                $options
            );
        }

        return self::$instance[$id_server];
    }

    public function getPrefix()
    {
        return _DB_PREFIX_;
    }

    /**
     * @param $test_db Db
     * Unit testing purpose only
     */
    public static function setInstanceForTesting($test_db)
    {
        self::$instance[0] = $test_db;
    }

    /**
     * Unit testing purpose only
     */
    public static function deleteTestingInstance()
    {
        self::$instance = array();
    }

    /**
     * Loads configuration settings for slave servers if needed.
     */
    protected static function loadSlaveServers()
    {
        if (self::$_slave_servers_loaded !== null) {
            return;
        }

        // Add here your slave(s) server(s) in this file
        if (file_exists(_PS_ROOT_DIR_.'/config/db_slave_server.inc.php')) {
            self::$_servers = array_merge(self::$_servers, require(_PS_ROOT_DIR_.'/config/db_slave_server.inc.php'));
        }

        self::$_slave_servers_loaded = true;
    }

    /**
     * Returns the best child layer database class.
     *
     * @return string
     */
    public static function getClass()
    {
        // Removed all classes extendint DbCore  so ...
        return str_replace('Core', '', __CLASS__);
    }

    /**
     * Disable the use of the cache
     *
     */
    public function disableCache()
    {
        $this->is_cache_enabled = false;
    }

    /**
     * Enable & flush the cache
     *
     */
    public function enableCache()
    {
        $this->is_cache_enabled = true;
    }

    /**
     * Closes connection to database
     */
    public function __destruct()
    {
        if ($this->link) {
            $this->disconnect();
        }
    }

    /**
     * Execute a query and get result resource
     *
     * @param string|DbQuery $sql
     * @return bool|mysqli_result|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public function query($sql)
    {
        if ($sql instanceof DbQuery) {
            $sql = $sql->build();
        }

        $this->result = $this->link->executeQuery($sql);

        if (!$this->result && $this->getNumberError() == 2006) {
            if ($this->connect()) {
                $this->result = $this->link->executeQuery($sql);
            }
        }

        if (_PS_DEBUG_SQL_) {
            $this->displayError($sql);
        }

        return $this->result;
    }

    /**
     * Executes an INSERT query
     *
     * @param string $table Table name without prefix
     * @param array $data Data to insert as associative array. If $data is a list of arrays, multiple insert will be done
     * @param bool $null_values If we want to use NULL values instead of empty quotes
     * @param bool $use_cache
     * @param int $type Must be Db::INSERT or Db::INSERT_IGNORE or Db::REPLACE
     * @param bool $add_prefix Add or not _DB_PREFIX_ before table name
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function insert($table, $data, $null_values = false, $use_cache = true, $type = Db::INSERT, $add_prefix = true)
    {
        if (!$data && !$null_values) {
            return true;
        }

        if ($add_prefix) {
            $table = _DB_PREFIX_.$table;
        }

        if ($type == Db::INSERT) {
            $insert_keyword = 'INSERT';
        } elseif ($type == Db::INSERT_IGNORE) {
            $insert_keyword = 'INSERT IGNORE';
        } elseif ($type == Db::REPLACE) {
            $insert_keyword = 'REPLACE';
        } elseif ($type == Db::ON_DUPLICATE_KEY) {
            $insert_keyword = 'INSERT';
        } else {
            throw new PrestaShopDatabaseException('Bad keyword, must be Db::INSERT or Db::INSERT_IGNORE or Db::REPLACE');
        }

        // Check if $data is a list of row
        $current = current($data);
        if (!is_array($current) || isset($current['type'])) {
            $data = array($data);
        }

        $keys = array();
        $values_stringified = array();
        $first_loop = true;
        $duplicate_key_stringified = '';
        foreach ($data as $row_data) {
            $values = array();
            foreach ($row_data as $key => $value) {
                if (!$first_loop) {
                    // Check if row array mapping are the same
                    if (!in_array("`$key`", $keys)) {
                        throw new PrestaShopDatabaseException('Keys form $data subarray don\'t match');
                    }

                    if ($duplicate_key_stringified != '') {
                        throw new PrestaShopDatabaseException('On duplicate key cannot be used on insert with more than 1 VALUE group');
                    }
                } else {
                    $keys[] = '`'.bqSQL($key).'`';
                }

                if (!is_array($value)) {
                    $value = array('type' => 'text', 'value' => $value);
                }
                if ($value['type'] == 'sql') {
                    $values[] = $string_value = $value['value'];
                } else {
                    $values[] = $string_value = $null_values && ($value['value'] === '' || is_null($value['value'])) ? 'NULL' : "'{$value['value']}'";
                }

                if ($type == Db::ON_DUPLICATE_KEY) {
                    $duplicate_key_stringified .= '`'.bqSQL($key).'` = '.$string_value.',';
                }
            }
            $first_loop = false;
            $values_stringified[] = '('.implode(', ', $values).')';
        }
        $keys_stringified = implode(', ', $keys);

        $sql = $insert_keyword.' INTO `'.$table.'` ('.$keys_stringified.') VALUES '.implode(', ', $values_stringified);
        if ($type == Db::ON_DUPLICATE_KEY) {
            $sql .= ' ON DUPLICATE KEY UPDATE '.substr($duplicate_key_stringified, 0, -1);
        }

        return (bool)$this->query($sql, $use_cache);
    }

    /**
     * Executes an UPDATE query
     *
     * @param string $table Table name without prefix
     * @param array $data Data to insert as associative array. If $data is a list of arrays, multiple insert will be done
     * @param string $where WHERE condition
     * @param int $limit
     * @param bool $null_values If we want to use NULL values instead of empty quotes
     * @param bool $use_cache
     * @param bool $add_prefix Add or not _DB_PREFIX_ before table name
     * @return bool
     */
    public function update($table, $data, $where = '', $limit = 0, $null_values = false, $use_cache = true, $add_prefix = true)
    {
        if (!$data) {
            return true;
        }

        if ($add_prefix) {
            $table = _DB_PREFIX_.$table;
        }

        $sql = 'UPDATE `'.bqSQL($table).'` SET ';
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $value = array('type' => 'text', 'value' => $value);
            }
            if ($value['type'] == 'sql') {
                $sql .= '`'.bqSQL($key)."` = {$value['value']},";
            } else {
                $sql .= ($null_values && ($value['value'] === '' || is_null($value['value']))) ? '`'.bqSQL($key).'` = NULL,' : '`'.bqSQL($key)."` = '{$value['value']}',";
            }
        }

        $sql = rtrim($sql, ',');
        if ($where) {
            $sql .= ' WHERE '.$where;
        }
        if ($limit) {
            $sql .= ' LIMIT '.(int)$limit;
        }

        return (bool)$this->query($sql, $use_cache);
    }

    /**
     * Executes a DELETE query
     *
     * @param string $table Name of the table to delete
     * @param string $where WHERE clause on query
     * @param int $limit Number max of rows to delete
     * @param bool $use_cache Use cache or not
     * @param bool $add_prefix Add or not _DB_PREFIX_ before table name
     * @return bool
     */
    public function delete($table, $where = '', $limit = 0, $use_cache = true, $add_prefix = true)
    {
        if (_DB_PREFIX_ && !preg_match('#^'._DB_PREFIX_.'#i', $table) && $add_prefix) {
            $table = _DB_PREFIX_.$table;
        }

        $this->result = false;
        $sql = 'DELETE FROM `'.bqSQL($table).'`'.($where ? ' WHERE '.$where : '').($limit ? ' LIMIT '.(int)$limit : '');
        $res = $this->query($sql);

        return (bool)$res;
    }

    /**
     * Executes a query
     * @deprecated use query() instead
     * @param string|DbQuery $sql
     * @param bool $use_cache
     * @return bool
     */
    public function execute($sql, $use_cache = true)
    {
        return (bool) $this->query($sql);
    }

    /**
     * Executes return the result of $sql as array
     *
     * @param string|DbQuery $sql Query to execute
     * @param bool $array Return an array instead of a result object (deprecated since 1.5.0.1, use query method instead)
     * @param bool $use_cache
     * @return array|false|null|mysqli_result|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public function executeS($sql, $array = true, $use_cache = true)
    {
        if ($sql instanceof DbQuery) {
            $sql = $sql->build();
        }

        $this->result = false;
        $this->last_query = $sql;

        // This method must be used only with queries which display results
        if (!preg_match('#^\s*\(?\s*(select|show|explain|describe|desc)\s#i', $sql)) {
            if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_) {
                throw new PrestaShopDatabaseException('Db->executeS() must be used only with select, show, explain or describe queries');
            }
            return $this->query($sql);
        }

        $this->result = $this->query($sql);

        if (!$this->result) {
            $result = false;
        } else {
            if (!$array) {
                $use_cache = false;
                $result = $this->result;
            } else {
                $result = $this->getAll($this->result);
            }
        }

        return $result;
    }

    /**
     * Returns an associative array containing the first row of the query
     * This function automatically adds "LIMIT 1" to the query
     *
     * @param string|DbQuery $sql the select query (without "LIMIT 1")
     * @param bool $use_cache Find it in cache first
     * @return array|bool|object|null
     */
    public function getRow($sql, $use_cache = true)
    {
        if ($sql instanceof DbQuery) {
            $sql = $sql->build();
        }

        $sql = rtrim($sql, " \t\n\r\0\x0B;").' LIMIT 1';
        $this->result = false;
        $this->last_query = $sql;

        $this->result = $this->query($sql);
        if (!$this->result) {
            $result = false;
        } else {
            $result = $this->nextRow($this->result);
        }

        return ($result ? $result : false);
    }

    /**
     * Returns a value from the first row, first column of a SELECT query
     *
     * @param string|DbQuery $sql
     * @param bool $use_cache
     * @return string|false|null
     */
    public function getValue($sql, $use_cache = true)
    {
        if ($sql instanceof DbQuery) {
            $sql = $sql->build();
        }

        if (!$result = $this->getRow($sql, $use_cache)) {
            return false;
        }

        return array_shift($result);
    }

    /**
     * Get number of rows for last result
     *
     * @return int
     */
    public function numRows()
    {
        if ($this->result) {
            return $this->result->rowCount();
        }
    }

    /**
     * Displays last SQL error
     *
     * @param string|bool $sql
     * @throws PrestaShopDatabaseException
     */
    public function displayError($sql = false)
    {
        global $webservice_call;

        $errno = $this->getNumberError();
        if ($webservice_call && $errno) {
            $dbg = debug_backtrace();
            WebserviceRequest::getInstance()->setError(500, '[SQL Error] '.$this->getMsgError().'. From '.(isset($dbg[3]['class']) ? $dbg[3]['class'] : '').'->'.$dbg[3]['function'].'() Query was : '.$sql, 97);
        } elseif (_PS_DEBUG_SQL_ && $errno && !defined('PS_INSTALLATION_IN_PROGRESS')) {
            if ($sql) {
                throw new PrestaShopDatabaseException($this->getMsgError().'<br /><br /><pre>'.$sql.'</pre>');
            }

            throw new PrestaShopDatabaseException($this->getMsgError());
        }
    }

    /**
     * Sanitize data which will be injected into SQL query
     *
     * @param string $string SQL data which will be injected into SQL query
     * @param bool $html_ok Does data contain HTML code ? (optional)
     * @return string Sanitized data
     */
    public function escape($string, $html_ok = false, $bq_sql = false)
    {
        if (_PS_MAGIC_QUOTES_GPC_) {
            $string = stripslashes($string);
        }

        if (!is_numeric($string)) {
            $string = $this->_escape($string);

            if (!$html_ok) {
                $string = strip_tags(Tools::nl2br($string));
            }

            if ($bq_sql === true) {
                $string = str_replace('`', '\`', $string);
            }
        }

        return $string;
    }

    /**
     * Get used link instance
     *
     * @return \Doctrine\DBAL\Connection
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Tries to connect to the database and create a table (checking creation privileges)
     *
     * @param string $server
     * @param string $user
     * @param string $pwd
     * @param string $db
     * @param string $prefix
     * @param string|null $engine Table engine
     * @return bool|string True, false or error
     */
    public static function checkCreatePrivilege($server, $user, $pwd, $db, $prefix, $engine = null)
    {
        try {
            $link = new self($server, $user, $pwd, $db, true);
        } catch (DBALException $e) {
            return false;
        }
        if ($engine === null) {
            $engine = 'MyISAM';
        }
        $result = $link->query('
		CREATE TABLE `'.$prefix.'test` (
			`test` tinyint(1) unsigned NOT NULL
		) ENGINE='.$engine);
        if (!$result) {
            $error = $link->errorInfo();
            return $error[2];
        }
        $link->query('DROP TABLE `'.$prefix.'test`');
        return true;
    }
    
    /**
     * Try a connection to the database
     *
     * @param string $server Server address
     * @param string $user Login for database connection
     * @param string $pwd Password for database connection
     * @param string $db Database name
     * @param bool $new_db_link
     * @param string|bool $engine
     * @param int $timeout
     * @return int Error code or 0 if connection was successful
     */
    public static function checkConnection($server, $user, $pwd, $db, $new_db_link = true, $engine = null, $timeout = 5)
    {
        return self::tryToConnect($server, $user, $pwd, $db);
    }

    /**
     * Try a connection to the database
     *
     * @see Db::checkConnection()
     * @param string $server Server address
     * @param string $user Login for database connection
     * @param string $pwd Password for database connection
     * @param string $db Database name
     * @param bool $newDbLink
     * @param string|bool $engine
     * @param int $timeout
     * @return int Error code or 0 if connection was successful
     */
    public static function tryToConnect($server, $user, $pwd, $db, $new_db_link = true, $engine = null, $timeout = 5)
    {
        try {
            $link = new self($server, $user, $pwd, $db, true);
        } catch (DBALException $e) {
            return ($e->getPrevious()->getCode() == 1049 || (defined('HHVM_VERSION') && $e->getPrevious()->getCode() == 42000)) ? 2 : 1;
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
        }else {
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
                if (in_array($row['Support'], array('DEFAULT', 'YES'))) {
                    $value = 'InnoDB';
                }
                break;
            }
        }
        return $value;
    }

    /**
     * Checks if auto increment value and offset is 1
     *
     * @param string $server
     * @param string $user
     * @param string $pwd
     * @return bool
     */
    public static function checkAutoIncrement($server, $user, $pwd)
    {
        try {
            $link = new self($server, $user, $pwd, null, true);
        } catch (DBALException $e) {
            return false;
        }
        $ret = (bool)(($result = $link->query('SELECT @@auto_increment_increment as aii')) && ($row = $result->fetch()) && $row['aii'] == 1);
        $ret &= (bool)(($result = $link->query('SELECT @@auto_increment_offset as aio')) && ($row = $result->fetch()) && $row['aio'] == 1);
        unset($link);
        return $ret;
    }

    /**
     * Tries to connect and create a new database
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $dbname
     * @param bool $dropit If true, drops the created database.
     * @return bool|int
     */
    public static function createDatabase($server, $user, $pwd, $dbname, $dropit = false)
    {
        try {
            $link = new self($server, $user, $pwd, null, true);
        } catch (DBALException $e) {
            return false;
        }
        $success = $link->link->exec('CREATE DATABASE `'.str_replace('`', '\\`', $dbname).'`');
        if ($dropit && ($link->link->exec('DROP DATABASE `'.str_replace('`', '\\`', $dbname).'`') !== false)) {
            return true;
        }
        return $success;
    }
}