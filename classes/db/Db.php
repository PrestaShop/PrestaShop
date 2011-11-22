<?php
/*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (file_exists(dirname(__FILE__).'/../config/settings.inc.php'))
	include_once(dirname(__FILE__).'/../config/settings.inc.php');

abstract class DbCore
{
	/**
	 * @var string Server (eg. localhost)
	 */
	protected $server;

	/**
	 * @var string Database user (eg. root)
	 */
	protected $user;

	/**
	 * @var string Database password (eg. can be empty !)
	 */
	protected $password;

	/**
	 * @var string Database name
	 */
	protected $database;

	/**
	 * @var bool
	 */
	protected $is_cache_enabled;

	/**
	 * @var mixed Ressource link
	 */
	protected $link;

	/**
	 * @var mixed SQL cached result
	 */
	protected $result;

	/**
	 * @var array List of DB instance
	 */
	protected static $instance = array();

	/**
	 * @var array Object instance for singleton
	 */
	protected static $_servers = array(
		array('server' => _DB_SERVER_, 'user' => _DB_USER_, 'password' => _DB_PASSWD_, 'database' => _DB_NAME_), /* MySQL Master server */
		// Add here your slave(s) server(s)
			// array('server' => '192.168.0.15', 'user' => 'rep', 'password' => '123456', 'database' => 'rep'),
			// array('server' => '192.168.0.3', 'user' => 'myuser', 'password' => 'mypassword', 'database' => 'mydatabase'),
	);

	/**
	 * Store last executed query
	 *
	 * @var string
	 */
	protected $last_query;

	/**
	 * Last cached query
	 *
	 * @var string
	 */
	protected $last_cached;

	/**
	 * Open a connection
	 */
	abstract public function connect();

	/**
	 * Close a connection
	 */
	abstract public function disconnect();

	/**
	 * Execute a query and get result ressource
	 *
	 * @param string $sql
	 * @return mixed
	 */
	abstract protected function _query($sql);

	/**
	 * Get number of rows in a result
	 */
	abstract protected function _numRows($result);

	/**
	 * Get the ID generated from the previous INSERT operation
	 */
	abstract public function Insert_ID();

	/**
	 * Get number of affected rows in previous databse operation
	 */
	abstract public function Affected_Rows();

	/**
	 * Get next row for a query which doesn't return an array
	 */
	abstract public function nextRow($result = false);

	/**
	 * Get database version
	 *
	 * @return string
	 */
	abstract public function getVersion();

	/**
	 * Protect string against SQL injections
	 *
	 * @param string $str
	 * @return string
	 */
	abstract public function _escape($str);

	/**
	 * Returns the text of the error message from previous database operation
	 */
	abstract public function getMsgError();

	/**
	 * Returns the number of the error from previous database operation
	 */
	abstract public function getNumberError();

	/* do not remove, useful for some modules */
	abstract public function set_db($db_name);

	/**
	 * Get Db object instance
	 *
	 * @param bool $master Decides wether the connection to be returned by the master server or the slave server
	 * @return Db instance
	 */
	public static function getInstance($master = true)
	{
		static $id = 0;

		$total_servers = count(self::$_servers);
		if ($master || $total_servers == 1)
			$id_server = 0;
		else
		{
			$id++;
			$id_server = ($total_servers > 2 && ($id % $total_servers) != 0) ? $id : 1;
		}

		if (!isset(self::$instance[$id_server]))
		{
			$class = Db::getClass();
			self::$instance[$id_server] = new $class(
				self::$_servers[$id_server]['server'],
				self::$_servers[$id_server]['user'],
				self::$_servers[$id_server]['password'],
				self::$_servers[$id_server]['database']
			);
		}

		return self::$instance[$id_server];
	}

	/**
	 * Get child layer class
	 *
	 * @return string
	 */
	public static function getClass()
	{
		$class = 'MySQL';
		if (extension_loaded('mysqli'))
			$class = 'DbMySQLi';
		else if (extension_loaded('pdo_mysql'))
			$class = 'DbPDO';
		return $class;
	}

	/**
	 * Instantiate database connection
	 *
	 * @param string $server Server address
	 * @param string $user User login
	 * @param string $password User password
	 * @param string $database Database name
	 * @param bool $connect If false, don't connect in constructor (since 1.5.0)
	 */
	public function __construct($server, $user, $password, $database, $connect = true)
	{
		$this->server = $server;
		$this->user = $user;
		$this->password = $password;
		$this->database = $database;
		$this->is_cache_enabled = (defined('_PS_CACHE_ENABLED_')) ? _PS_CACHE_ENABLED_ : false;

		if (!defined('_PS_DEBUG_SQL_'))
			define('_PS_DEBUG_SQL_', false);

		if ($connect)
			$this->connect();
	}

	/**
	 * Close connection to database
	 */
	public function __destruct()
	{
		if ($this->link)
			$this->disconnect();
	}

	/**
	 * Filter SQL query within a blacklist
	 *
	 * @param string $table Table where insert/update data
	 * @param string $data Data to insert/update
	 * @param string $type INSERT or INSERT IGNORE or UPDATE
	 * @param string $where WHERE clause, only for UPDATE (optional)
	 * @param string $limit LIMIT clause (optional)
	 * @param bool $use_null If true, replace empty strings and NULL by a NULL value
	 * @return mixed|boolean SQL query result
	 */
	public function autoExecute($table, $data, $type, $where = false, $limit = false, $use_cache = 1, $use_null = false)
	{
		if (!$data)
			return true;

		$type = strtoupper($type);
		if ($type == 'INSERT' || $type == 'INSERT IGNORE')
		{
			// Check if $data is a list of row
			$current = current($data);
			if (!is_array($current) || isset($current['type']))
				$data = array($data);

			$keys = array();
			$values_stringified = array();
			foreach ($data as $row_data)
			{
				$values = array();
				foreach ($row_data as $key => $value)
				{
					if (isset($keys_stringified))
					{
						// Check if row array mapping are the same
						if (!in_array("`$key`", $keys))
							throw new PrestashopDatabaseException('Keys form $data subarray don\'t match');
					}
					else
						$keys[] = "`$key`";

					if (!is_array($value))
						$value = array('type' => 'text', 'value' => $value);
					if ($value['type'] == 'sql')
						$values[] = $value['value'];
					else
						$values[] = $use_null && ($value['value'] === '' || is_null($value['value'])) ? 'NULL' : "'{$value['value']}'";
				}
				$keys_stringified = implode(', ', $keys);
				$values_stringified[] = '('.implode(', ', $values).')';
			}

			$sql = $type.' INTO `'.$table.'` ('.$keys_stringified.') VALUES '.implode(', ', $values_stringified);
			if ($limit)
				$sql .= ' LIMIT '.(int)$limit;
			return $this->q($sql, $use_cache);
		}
		else if ($type == 'UPDATE')
		{
			$sql = 'UPDATE `'.$table.'` SET ';
			foreach ($data as $key => $value)
			{
				if (!is_array($value))
					$value = array('type' => 'text', 'value' => $value);
				if ($value['type'] == 'sql')
					$sql .= "`$key` = {$value['value']},";
				else
					$sql .= ($use_null && ($value['value'] === '' || is_null($value['value']))) ? "`$key` = NULL," : "`$key` = '{$value['value']}',";
			}

			$sql = rtrim($sql, ',');
			if ($where)
				$sql .= ' WHERE '.$where;
			if ($limit)
				$sql .= ' LIMIT '.(int)$limit;
			return $this->q($sql, $use_cache);
		}
		else
			throw new PrestashopDatabaseException('Wrong argument (miss type) in Db::autoExecute()');

		return false;
	}

	/**
	 * Filter SQL query within a blacklist
	 *
	 * @param string $table Table where insert/update data
	 * @param string $values Data to insert/update
	 * @param string $type INSERT or UPDATE
	 * @param string $where WHERE clause, only for UPDATE (optional)
	 * @param string $limit LIMIT clause (optional)
	 * @return mixed|boolean SQL query result
	 */
	public function autoExecuteWithNullValues($table, $values, $type, $where = false, $limit = false)
	{
		return $this->autoExecute($table, $values, $type, $where, $limit, 0, true);
	}

	/**
	 * Execute a query and get result ressource
	 *
	 * @param string $sql
	 * @return mixed
	 */
	public function query($sql)
	{
		$sql = (string)$sql;
		$result = $this->_query($sql);
		if (_PS_DEBUG_SQL_)
			$this->displayError($sql);
		return $result;
	}

	/**
	 * Execute a DELETE query
	 *
	 * @param string $table Name of the table to delete
	 * @param string $where WHERE clause on query
	 * @param int $limit Number max of rows to delete
	 * @param bool $use_cache Use cache or not
	 * @return bool
	 */
	public function delete($table, $where = false, $limit = false, $use_cache = 1)
	{
		$this->result = false;
		$sql = 'DELETE FROM `'.bqSQL($table).'`'.($where ? ' WHERE '.$where : '').($limit ? ' LIMIT '.(int)$limit : '');
		$res = $this->query($sql);
		if ($use_cache && $this->is_cache_enabled)
			Cache::getInstance()->deleteQuery($sql);
		return $res;
	}

	/**
	 * Execute a query
	 *
	 * @param string $sql
	 * @param bool $use_cache
	 * @return mixed
	 */
	public function execute($sql, $use_cache = 1)
	{
		$sql = (string)$sql;
		$this->result = $this->query($sql);
		if ($use_cache && $this->is_cache_enabled)
			Cache::getInstance()->deleteQuery($sql);
		return $this->result;
	}

	/**
	 * ExecuteS return the result of $sql as array
	 *
	 * @param string $sql query to execute
	 * @param boolean $array return an array instead of a mysql_result object
	 * @param int $use_cache if query has been already executed, use its result
	 * @return array or result object
	 */
	public function executeS($sql, $array = true, $use_cache = 1)
	{
		$sql = (string)$sql;

		// This methode must be used only with queries which display results
		if (!preg_match('#^\s*(select|show|explain)\s#i', $sql))
		{
			if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_)
				throw new PrestashopDatabaseException('Db->executeS() must be used only with select, show or explain queries');
			return $this->execute($sql, $use_cache);
		}

		$this->result = false;
		$this->last_query = $sql;
		if ($use_cache && $this->is_cache_enabled && $array && ($result = Cache::getInstance()->get(md5($sql))))
		{
			$this->last_cached = true;
			return $result;
		}

		$this->result = $this->query($sql);
		if (!$this->result)
			return false;

		$this->last_cached = false;
		if (!$array)
			return $this->result;

		$result_array = array();
		while ($row = $this->nextRow($this->result))
			$result_array[] = $row;

		if ($use_cache && $this->is_cache_enabled)
			Cache::getInstance()->setQuery($sql, $result_array);
		return $result_array;
	}

	/**
	 * getRow return an associative array containing the first row of the query
	 * This function automatically add "limit 1" to the query
	 *
	 * @param mixed $sql the select query (without "LIMIT 1")
	 * @param int $use_cache find it in cache first
	 * @return array associative array of (field=>value)
	 */
	public function getRow($sql, $use_cache = 1)
	{
		$sql = (string)$sql;
		$sql .= ' LIMIT 1';
		$this->result = false;
		$this->last_query = $sql;
		if ($use_cache && $this->is_cache_enabled && ($result = Cache::getInstance()->get(md5($sql))))
		{
			$this->last_cached = true;
			return $result;
		}

		$this->result = $this->query($sql);
		if (!$this->result)
			return false;

		$this->last_cached = false;
		$result = $this->nextRow($this->result);
		if ($use_cache && $this->is_cache_enabled)
			Cache::getInstance()->setQuery($sql, $result);
		return $result;
	}

	/**
	 * getValue return the first item of a select query.
	 *
	 * @param mixed $sql
	 * @param int $use_cache
	 * @return void
	 */
	public function getValue($sql, $use_cache = 1)
	{
		$sql = (string)$sql;
		if (!$result = $this->getRow($sql, $use_cache))
			return false;
		return array_shift($result);
	}

	/**
	 * Get number of rows for last result
	 *
	 * @return int
	 */
	public function numRows()
	{
		if (!$this->last_cached && $this->result)
		{
			$nrows = $this->_numRows($this->result);
			if ($this->is_cache_enabled)
				Cache::getInstance()->set(md5($this->last_query).'_nrows', $nrows);
			return $nrows;
		}
		else if ($this->is_cache_enabled && $this->last_cached)
			return Cache::getInstance()->get(md5($this->last_query).'_nrows');
	}

	/**
	 *
	 * Execute a query
	 *
	 * @param string $sql
	 * @param bool $use_cache
	 */
	protected function q($sql, $use_cache = 1)
	{
		global $webservice_call;

		$sql = (string)$sql;
		$this->result = false;
		$result = $this->query($sql);
		if ($use_cache && $this->is_cache_enabled)
			Cache::getInstance()->deleteQuery($sql);
		return $result;
	}

	/**
	 * Display last SQL error
	 *
	 * @param unknown_type $sql
	 */
	public function displayError($sql = false)
	{
		global $webservice_call;

		$errno = $this->getNumberError();
		if ($webservice_call && $errno)
			WebserviceRequest::getInstance()->setError(500, '[SQL Error] '.$this->getMsgError().'. Query was : '.$sql, 97);
		else if (_PS_DEBUG_SQL_ && $errno && !defined('PS_INSTALLATION_IN_PROGRESS'))
		{
			if ($sql)
				throw new PrestashopDatabaseException(Tools::displayError($this->getMsgError().'<br /><br /><pre>'.$sql.'</pre>'));
			throw new PrestashopDatabaseException(Tools::displayError($this->getMsgError()));
		}
	}

	/**
	 * Sanitize data which will be injected into SQL query
	 *
	 * @param string $string SQL data which will be injected into SQL query
	 * @param boolean $html_ok Does data contain HTML code ? (optional)
	 * @return string Sanitized data
	 */
	public function escape($string, $html_ok)
	{
		if (_PS_MAGIC_QUOTES_GPC_)
			$string = stripslashes($string);
		if (!is_numeric($string))
		{
			$string = $this->_escape($string);
			if (!$html_ok)
				$string = strip_tags(Tools::nl2br($string));
		}

		return $string;
	}

	/**
	 * Try a connection to te database
	 *
	 * @param string $server Server address
	 * @param string $user Login for database connection
	 * @param string $pwd Password for database connection
	 * @param string $db Database name
	 * @param bool $newDbLink
	 * @return int
	 */
	public static function checkConnection($server, $user, $pwd, $db, $new_db_link = true, $engine = null)
	{
		return call_user_func_array(array(Db::getClass(), 'tryToConnect'), array($server, $user, $pwd, $db, $new_db_link, $engine));
	}

	/**
	 * Try a connection to te database
	 *
	 * @param string $server Server address
	 * @param string $user Login for database connection
	 * @param string $pwd Password for database connection
	 * @return int
	 */
	public static function checkEncoding($server, $user, $pwd)
	{
		return call_user_func_array(array(Db::getClass(), 'tryUTF8'), array($server, $user, $pwd));
	}

	/**
	 * Alias of Db::getInstance()->ExecuteS
	 *
	 * @acces string query The query to execute
	 * @return array Array of line returned by MySQL
	 */
	public static function s($sql, $use_cache = 1)
	{
		return Db::getInstance()->executeS($sql, true, $use_cache);
	}

	public static function ps($sql, $use_cache = 1)
	{
		$ret = Db::s($sql, $use_cache);
		p($ret);
		return $ret;
	}

	public static function ds($sql, $use_cache = 1)
	{
		Db::s($sql, $use_cache);
		die();
	}
}
