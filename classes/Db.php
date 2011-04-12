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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (file_exists(dirname(__FILE__).'/../config/settings.inc.php'))
	include_once(dirname(__FILE__).'/../config/settings.inc.php');
//include_once(dirname(__FILE__).'/../classes/MySQL.php');

abstract class DbCore
{
	/** @var string Server (eg. localhost) */
	protected $_server;

	/** @var string Database user (eg. root) */
	protected $_user;

	/** @var string Database password (eg. can be empty !) */
	protected $_password;

	/** @var string Database type (MySQL, PgSQL) */
	protected $_type;

	/** @var string Database name */
	protected $_database;

	/** @var mixed Ressource link */
	protected $_link;

	/** @var mixed SQL cached result */
	protected $_result;

	/** @var mixed ? */
	protected static $_db;

	/** @var mixed Object instance for singleton */
	protected static $_instance = array();

	protected static $_servers = array(	
	array('server' => _DB_SERVER_, 'user' => _DB_USER_, 'password' => _DB_PASSWD_, 'database' => _DB_NAME_), /* MySQL Master server */
	/* Add here your slave(s) server(s)*/
	/*array('server' => '192.168.0.15', 'user' => 'rep', 'password' => '123456', 'database' => 'rep'),
	array('server' => '192.168.0.3', 'user' => 'myuser', 'password' => 'mypassword', 'database' => 'mydatabase'),
	*/
	);
	
	protected $_lastQuery;
	protected $_lastCached;
	
	protected static $_idServer;

	/**
	 * Get Db object instance (Singleton)
	 *
	 * @param boolean $master Decides wether the connection to be returned by the master server or the slave server
	 * @return object Db instance
	 */
	public static function getInstance($master = 1)
	{
		if ($master OR ($nServers = sizeof(self::$_servers)) == 1)
			$idServer = 0;
		else
			$idServer = ($nServers > 2 AND ($id = ++self::$_idServer % (int)$nServers) !== 0) ? $id : 1;

		if(!isset(self::$_instance[$idServer]))
			self::$_instance[(int)($idServer)] = new MySQL(self::$_servers[(int)($idServer)]['server'], self::$_servers[(int)($idServer)]['user'], self::$_servers[(int)($idServer)]['password'], self::$_servers[(int)($idServer)]['database']);
		
		return self::$_instance[(int)($idServer)];
	}
	
	public function getRessource() { return $this->_link;}
	
	public function __destruct()
	{
		$this->disconnect();
	}

	/**
	 * Build a Db object
	 */
	public function __construct($server, $user, $password, $database)
	{
		$this->_server = $server;
		$this->_user = $user;
		$this->_password = $password;
		$this->_type = _DB_TYPE_;
		$this->_database = $database;

		$this->connect();
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
	public function	autoExecute($table, $values, $type, $where = false, $limit = false, $use_cache = 1)
	{
		if (!sizeof($values))
			return true;

		if (strtoupper($type) == 'INSERT')
		{
			$query = 'INSERT INTO `'.$table.'` (';
			foreach ($values AS $key => $value)
				$query .= '`'.$key.'`,';
			$query = rtrim($query, ',').') VALUES (';
			foreach ($values AS $key => $value)
				$query .= '\''.$value.'\',';
			$query = rtrim($query, ',').')';
			if ($limit)
				$query .= ' LIMIT '.(int)($limit);
			return $this->q($query, $use_cache);
		}
		elseif (strtoupper($type) == 'UPDATE')
		{
			$query = 'UPDATE `'.$table.'` SET ';
			foreach ($values AS $key => $value)
				$query .= '`'.$key.'` = \''.$value.'\',';
			$query = rtrim($query, ',');
			if ($where)
				$query .= ' WHERE '.$where;
			if ($limit)
				$query .= ' LIMIT '.(int)($limit);
			return $this->q($query, $use_cache);
		}
		
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
		if (!sizeof($values))
			return true;

		if (strtoupper($type) == 'INSERT')
		{
			$query = 'INSERT INTO `'.$table.'` (';
			foreach ($values AS $key => $value)
				$query .= '`'.$key.'`,';
			$query = rtrim($query, ',').') VALUES (';
			foreach ($values AS $key => $value)
				$query .= (($value === '' OR $value === NULL) ? 'NULL' : '\''.$value.'\'').',';
			$query = rtrim($query, ',').')';
			if ($limit)
				$query .= ' LIMIT '.(int)($limit);
			return $this->q($query);
		}
		elseif (strtoupper($type) == 'UPDATE')
		{
			$query = 'UPDATE `'.$table.'` SET ';
			foreach ($values AS $key => $value)
				$query .= '`'.$key.'` = '.(($value === '' OR $value === NULL) ? 'NULL' : '\''.$value.'\'').',';
			$query = rtrim($query, ',');
			if ($where)
				$query .= ' WHERE '.$where;
			if ($limit)
				$query .= ' LIMIT '.(int)($limit);
			return $this->q($query);
		}
		
		return false;
	}

	/*********************************************************
	 * ABSTRACT METHODS
	 *********************************************************/
	
	/**
	 * Open a connection
	 */
	abstract public function connect();

	/**
	 * Get the ID generated from the previous INSERT operation
	 */
	abstract public function Insert_ID();

	/**
	 * Get number of affected rows in previous databse operation
	 */
	abstract public function Affected_Rows();

	/**
	 * Gets the number of rows in a result
	 */
	abstract public function NumRows();

	/**
	 * Delete
	 */
	abstract public function delete ($table, $where = false, $limit = false, $use_cache = 1);
	/**
	 * Fetches a row from a result set
	 */
	abstract public function Execute ($query, $use_cache = 1);

	/**
	 * Fetches an array containing all of the rows from a result set
	 */
	abstract public function ExecuteS($query, $array = true, $use_cache = 1);
	
	/*
	 * Get next row for a query which doesn't return an array 
	 */
	abstract public function nextRow($result = false);
	
	/**
		 * Alias of Db::getInstance()->ExecuteS
		 *
		 * @acces string query The query to execute
		 * @return array Array of line returned by MySQL
		 */
	static public function s($query, $use_cache = 1)
	{
		return Db::getInstance()->ExecuteS($query, true, $use_cache);
	}
	
	static public function ps($query, $use_cache = 1)
	{
		$ret = Db::s($query, $use_cache);
		p($ret);
		return $ret;
	}
	
	static public function ds($query, $use_cache = 1)
	{
		Db::s($query, $use_cache);
		die();
	}

	/**
	 * getRow return an associative array containing the first row of the query
	 * This function automatically add "limit 1" to the query
	 * 
	 * @param mixed $query the select query (without "LIMIT 1")
	 * @param int $use_cache find it in cache first
	 * @return array associative array of (field=>value)
	 */
	abstract public function getRow($query, $use_cache = 1);

	/**
	 * getValue return the first item of a select query.
	 * 
	 * @param mixed $query 
	 * @param int $use_cache 
	 * @return void
	 */
	abstract public function getValue($query, $use_cache = 1);

	/**
	 * Returns the text of the error message from previous database operation
	 */
	abstract public function getMsgError();
}

/**
 * Sanitize data which will be injected into SQL query
 *
 * @param string $string SQL data which will be injected into SQL query
 * @param boolean $htmlOK Does data contain HTML code ? (optional)
 * @return string Sanitized data
 */
function pSQL($string, $htmlOK = false)
{
	if (_PS_MAGIC_QUOTES_GPC_)
		$string = stripslashes($string);
	if (!is_numeric($string))
	{
		$link = Db::getInstance()->getRessource();
		$string = _PS_MYSQL_REAL_ESCAPE_STRING_ ? mysql_real_escape_string($string, $link) : addslashes($string);
		if (!$htmlOK)
			$string = strip_tags(nl2br2($string));
	}
		
	return $string;
}

/**
 * Convert \n and \r\n and \r to <br />
 *
 * @param string $string String to transform
 * @return string New string
 */
function nl2br2($string)
{
	return str_replace(array("\r\n", "\r", "\n"), '<br />', $string);
}


