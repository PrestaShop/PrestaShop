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
	/** @var string Server (eg. localhost) */
	protected $_server;

	/** @var string Database user (eg. root) */
	protected $_user;

	/** @var string Database password (eg. can be empty !) */
	protected $_password;

	/** @var string Database name */
	protected $_database;

	/** @var mixed Ressource link */
	protected $_link;

	/** @var mixed SQL cached result */
	protected $_result;

	/** @var array List of DB instance */
	protected static $_instance = array();

	/** @var array Object instance for singleton */
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
	protected $_lastQuery;
	
	/**
	 * Last cached query
	 * 
	 * @var string
	 */
	protected $_lastCached;
	
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
	 * @param boolean $master Decides wether the connection to be returned by the master server or the slave server
	 * @return Db instance
	 */
	public static function getInstance($master = 1)
	{
		static $id = 0;

		$nServers = sizeof(self::$_servers);
		if ($master || $nServers == 1)
			$idServer = 0;
		else
		{
			$id++;
			$idServer = ($nServers > 2 && ($id % $nServers) != 0) ? $id : 1;
		}

		if (!isset(self::$_instance[$idServer]))
		{
			$class = Db::getClass();
			self::$_instance[$idServer] = new $class(self::$_servers[$idServer]['server'], self::$_servers[$idServer]['user'], self::$_servers[$idServer]['password'], self::$_servers[$idServer]['database']);
		}
	
		return self::$_instance[$idServer];
	}

	/**
	 * Get child layer class
	 * 
	 * @return string
	 */
	public static function getClass()
	{
		$class = 'MySQL';
		if (class_exists('mysqli', false))
			$class = 'DbMySQLi';
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
		$this->_server = $server;
		$this->_user = $user;
		$this->_password = $password;
		$this->_type = _DB_TYPE_;
		$this->_database = $database;
		
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
		if ($this->_link)
			$this->disconnect();
	}

	/**
	 * Filter SQL query within a blacklist
	 *
	 * @param string $table Table where insert/update data
	 * @param string $values Data to insert/update
	 * @param string $type INSERT or UPDATE
	 * @param string $where WHERE clause, only for UPDATE (optional)
	 * @param string $limit LIMIT clause (optional)
	 * @param bool $useNull If true, replace empty strings and NULL by a NULL value
	 * @return mixed|boolean SQL query result
	 */
	public function	autoExecute($table, $data, $type, $where = false, $limit = false, $use_cache = 1, $useNull = false)
	{
		if (!$data)
			return true;

		if (strtoupper($type) == 'INSERT')
		{
			$keys = $values = array();
			foreach ($data AS $key => $value)
			{
				$keys[] = "`$key`";
				$values[] = ($useNull && ($value === '' || is_null($value))) ? 'NULL' : "'$value'";
			}

			$sql = 'INSERT INTO `'.$table.'` ('.implode(', ', $keys).') VALUES ('.implode(', ', $values).')';
			if ($limit)
				$sql .= ' LIMIT '.(int)$limit;
			return $this->q($sql, $use_cache);
		}
		else if (strtoupper($type) == 'UPDATE')
		{
			$sql = 'UPDATE `'.$table.'` SET ';
			foreach ($data AS $key => $value)
				$sql .= ($useNull && ($value === '' || is_null($value))) ? "`$key` = NULL," : "`$key` = '$value',";
			$sql = rtrim($sql, ',');
			if ($where)
				$sql .= ' WHERE '.$where;
			if ($limit)
				$sql .= ' LIMIT '.(int)$limit;
			return $this->q($sql, $use_cache);
		}
		else
			die('Wrong argument (miss type) in Db::autoExecute()');
		
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
	public function	autoExecuteWithNullValues($table, $values, $type, $where = false, $limit = false)
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
	public function	delete($table, $where = false, $limit = false, $use_cache = 1)
	{
		$this->_result = false;
		$sql = 'DELETE FROM `'.pSQL($table).'`'.($where ? ' WHERE '.$where : '').($limit ? ' LIMIT '.(int)$limit : '');
		$res = $this->query($sql);
		if ($use_cache AND _PS_CACHE_ENABLED_)
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
	public function	Execute($sql, $use_cache = 1)
	{
		$this->_result = $this->query($sql);
		if ($use_cache AND _PS_CACHE_ENABLED_)
			Cache::getInstance()->deleteQuery($sql);
		return $this->_result;
	}
	
	/**
	 * ExecuteS return the result of $sql as array
	 * 
	 * @param string $sql query to execute
	 * @param boolean $array return an array instead of a mysql_result object
	 * @param int $use_cache if query has been already executed, use its result
	 * @return array or result object 
	 */
	public function	ExecuteS($sql, $array = true, $use_cache = 1)
	{
		$this->_result = false;
		$this->_lastQuery = $sql;
		if ($use_cache AND _PS_CACHE_ENABLED_ && $array AND ($result = Cache::getInstance()->get(md5($sql))))
		{
			$this->_lastCached = true;
			return $result;
		}

		$this->_result = $this->query($sql);
		if (!$this->_result)
			return false;

		$this->_lastCached = false;
		if (!$array)
			return $this->_result;

		$resultArray = array();
		while ($row = $this->nextRow($this->_result))
			$resultArray[] = $row;

		if ($use_cache AND _PS_CACHE_ENABLED_)	
			Cache::getInstance()->setQuery($sql, $resultArray);
		return $resultArray;
	}

	/**
	 * getRow return an associative array containing the first row of the query
	 * This function automatically add "limit 1" to the query
	 * 
	 * @param mixed $sql the select query (without "LIMIT 1")
	 * @param int $use_cache find it in cache first
	 * @return array associative array of (field=>value)
	 */
	public function	getRow($sql, $use_cache = 1)
	{
		$sql .= ' LIMIT 1';
		$this->_result = false;
		$this->_lastQuery = $sql;
		if ($use_cache && _PS_CACHE_ENABLED_ && ($result = Cache::getInstance()->get(md5($sql))))
		{
			$this->_lastCached = true;
			return $result;
		}

		$this->_result = $this->query($sql);
		if (!$this->_result)
			return false;

		$this->_lastCached = false;
		$result = $this->nextRow($this->_result);
		if ($use_cache AND _PS_CACHE_ENABLED_)
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
	public function	getValue($sql, $use_cache = 1)
	{
		if (!$result = $this->getRow($sql, $use_cache))
			return false;
		return array_shift($result);
	}
	
	/**
	 * Get number of rows for last result
	 * 
	 * @return int
	 */
	public function	NumRows()
	{
		if (!$this->_lastCached && $this->_result)
		{
			$nrows = $this->_numRows($this->_result);
			if (_PS_CACHE_ENABLED_)
				Cache::getInstance()->setNumRows(md5($this->_lastQuery), $nrows);
			return $nrows;
		}
		else if (_PS_CACHE_ENABLED_ AND $this->_lastCached)
			return Cache::getInstance()->getNumRows(md5($this->_lastQuery));
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

		$this->_result = false;
		$result = $this->query($sql);
		if ($use_cache AND _PS_CACHE_ENABLED_)
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
		elseif (_PS_DEBUG_SQL_ AND $errno AND !defined('PS_INSTALLATION_IN_PROGRESS'))
		{
			if ($sql)
				die(Tools::displayError($this->getMsgError().'<br /><br /><pre>'.$sql.'</pre>'));
			die(Tools::displayError($this->getMsgError()));
		}
	}
	
	/**
	 * Sanitize data which will be injected into SQL query
	 *
	 * @param string $string SQL data which will be injected into SQL query
	 * @param boolean $htmlOK Does data contain HTML code ? (optional)
	 * @return string Sanitized data
	 */
	public function escape($string, $htmlOK)
	{
		if (_PS_MAGIC_QUOTES_GPC_)
			$string = stripslashes($string);
		if (!is_numeric($string))
		{
			$string = $this->_escape($string);
			if (!$htmlOK)
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
	static public function checkConnection($server, $user, $pwd, $db, $newDbLink = true)
	{
		return call_user_func_array(array(Db::getClass(), 'tryToConnect'), array($server, $user, $pwd, $db, $newDbLink));
	}

	/**
	 * Try a connection to te database
	 * 
	 * @param string $server Server address
	 * @param string $user Login for database connection
	 * @param string $pwd Password for database connection
	 * @return int
	 */
	static public function checkEncoding($server, $user, $pwd)
	{
		return call_user_func_array(array(Db::getClass(), 'tryToConnect'), array($server, $user, $pwd));
	}

	/**
	 * Alias of Db::getInstance()->ExecuteS
	 *
	 * @acces string query The query to execute
	 * @return array Array of line returned by MySQL
	 */
	static public function s($sql, $use_cache = 1)
	{
		return Db::getInstance()->ExecuteS($sql, true, $use_cache);
	}
	
	static public function ps($sql, $use_cache = 1)
	{
		$ret = Db::s($sql, $use_cache);
		p($ret);
		return $ret;
	}
	
	static public function ds($sql, $use_cache = 1)
	{
		Db::s($sql, $use_cache);
		die();
	}
}