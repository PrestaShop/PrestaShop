<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class MySQLCore extends Db
{
	/**
	 * @see DbCore::connect()
	 */
	public function connect()
	{
		if (!defined('_PS_MYSQL_REAL_ESCAPE_STRING_'))
			define('_PS_MYSQL_REAL_ESCAPE_STRING_', function_exists('mysql_real_escape_string'));

		if (!$this->link = mysql_connect($this->server, $this->user, $this->password))
			throw new PrestaShopDatabaseException(Tools::displayError('Link to database cannot be established.'));

		if (!$this->set_db($this->database))
			throw new PrestaShopDatabaseException(Tools::displayError('The database selection cannot be made.'));

		// UTF-8 support
		if (!mysql_query('SET NAMES \'utf8\'', $this->link))
			throw new PrestaShopDatabaseException(Tools::displayError('PrestaShop Fatal error: no utf-8 support. Please check your server configuration.'));

		return $this->link;
	}
	
	public static function createDatabase($host, $user, $password, $dbname, $dropit = false)
	{
		$link = mysql_connect($host, $user, $password);
		$success = mysql_query('CREATE DATABASE `'.str_replace('`', '\\`', $dbname).'`', $link);
		if ($dropit && (mysql_query('DROP DATABASE `'.str_replace('`', '\\`', $dbname).'`', $link) !== false))
			return true;
		return $success;
	}

	/**
	 * @see DbCore::disconnect()
	 */
	public function disconnect()
	{
		mysql_close($this->link);
	}

	/**
	 * @see DbCore::_query()
	 */
	protected function _query($sql)
	{
		return mysql_query($sql, $this->link);
	}

	/**
	 * @see DbCore::nextRow()
	 */
	public function nextRow($result = false)
	{
		$return = false;
		if(is_resource($result) && $result)
			$return = mysql_fetch_assoc($result);
		elseif(is_resource($this->_result) && $this->_result)
			$return = mysql_fetch_assoc($this->_result);
		return $return;	
	}

	/**
	 * @see DbCore::_numRows()
	 */
	protected function _numRows($result)
	{
		return mysql_num_rows($result);
	}

	/**
	 * @see DbCore::Insert_ID()
	 */
	public function Insert_ID()
	{
		return mysql_insert_id($this->link);
	}

	/**
	 * @see DbCore::Affected_Rows()
	 */
	public function Affected_Rows()
	{
		return mysql_affected_rows($this->link);
	}

	/**
	 * @see DbCore::getMsgError()
	 */
	public function getMsgError($query = false)
	{
		return mysql_error($this->link);
	}

	/**
	 * @see DbCore::getNumberError()
	 */
	public function getNumberError()
	{
		return mysql_errno($this->link);
	}

	/**
	 * @see DbCore::getVersion()
	 */
	public function getVersion()
	{
		return mysql_get_server_info($this->link);
	}

	/**
	 * @see DbCore::_escape()
	 */
	public function _escape($str)
	{
		return _PS_MYSQL_REAL_ESCAPE_STRING_ ? mysql_real_escape_string($str, $this->link) : addslashes($str);
	}

	/**
	 * @see DbCore::set_db()
	 */
	public function set_db($db_name)
	{
		return mysql_select_db($db_name, $this->link);
	}

	/**
	 * @see Db::hasTableWithSamePrefix()
	 */
	public static function hasTableWithSamePrefix($server, $user, $pwd, $db, $prefix)
	{
		if (!$link = @mysql_connect($server, $user, $pwd, true))
			return false;
		if (!@mysql_select_db($db, $link))
			return false;

		$sql = 'SHOW TABLES LIKE \''.$prefix.'%\'';
		$result = mysql_query($sql);
		return (bool)@mysql_fetch_assoc($result);
	}

	/**
	 * @see Db::checkConnection()
	 */
	public static function tryToConnect($server, $user, $pwd, $db, $newDbLink = true, $engine = null, $timeout = 5)
	{
		ini_set('mysql.connect_timeout', $timeout);
		if (!$link = @mysql_connect($server, $user, $pwd, $newDbLink))
			return 1;
		if (!@mysql_select_db($db, $link))
			return 2;
		@mysql_close($link);
		return 0;
	}
		
	public function getBestEngine()
	{
		$value = 'InnoDB';
		
		$sql = 'SHOW VARIABLES WHERE Variable_name = \'have_innodb\'';
		$result = mysql_query($sql);
		if (!$result)
			$value = 'MyISAM';
		$row = mysql_fetch_assoc($result);
		if (!$row || strtolower($row['Value']) != 'yes')
			$value = 'MyISAM';
		
		/* MySQL >= 5.6 */
		$sql = 'SHOW ENGINES';
		$result = mysql_query($sql);
		while ($row = mysql_fetch_assoc($result))
			if ($row['Engine'] == 'InnoDB')
			{
				if (in_array($row['Support'], array('DEFAULT', 'YES')))
					$value = 'InnoDB';
				break;
			}
		return $value;
	}
	
	public static function checkCreatePrivilege($server, $user, $pwd, $db, $prefix, $engine = null)
	{
		ini_set('mysql.connect_timeout', 5);
		if (!$link = @mysql_connect($server, $user, $pwd, true))
			return false;
		if (!@mysql_select_db($db, $link))
			return false;

		$sql = '
			CREATE TABLE `'.$prefix.'test` (
			`test` tinyint(1) unsigned NOT NULL
			) ENGINE=MyISAM';

		$result = mysql_query($sql, $link);
		
		if (!$result)
			return mysql_error($link);

		mysql_query('DROP TABLE `'.$prefix.'test`', $link);
		return true;
	}

	/**
	 * @see Db::checkEncoding()
	 */
	static public function tryUTF8($server, $user, $pwd)
	{
		$link = @mysql_connect($server, $user, $pwd);
		if (!mysql_query('SET NAMES \'utf8\'', $link))
			$ret = false;
		else
			$ret = true;
		@mysql_close($link);
		return $ret;
	}
}
