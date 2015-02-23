<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class DbMySQLiCore extends Db
{
	/**
	 * @see DbCore::connect()
	 */
	public function	connect()
	{
		if (strpos($this->server, ':') !== false)
		{
			list($server, $port) = explode(':', $this->server);
			$this->link = @new mysqli($server, $this->user, $this->password, $this->database, $port);
		}
		else
			$this->link = @new mysqli($this->server, $this->user, $this->password, $this->database);

		// Do not use object way for error because this work bad before PHP 5.2.9
		if (mysqli_connect_error())
			throw new PrestaShopDatabaseException(sprintf(Tools::displayError('Link to database cannot be established: %s'), mysqli_connect_error()));

		// UTF-8 support
		if (!$this->link->query('SET NAMES \'utf8\''))
			throw new PrestaShopDatabaseException(Tools::displayError('PrestaShop Fatal error: no utf-8 support. Please check your server configuration.'));

		return $this->link;
	}

	public static function createDatabase($host, $user = null, $password = null, $database = null, $dropit = false)
	{
		if (is_null($user))
			$user = $this->user;
		if (is_null($password))
			$password = $this->password;
		if (is_null($database))
			$database = $this->database;

		if (strpos($host, ':') !== false)
		{
			list($host, $port) = explode(':', $host);
			$link = @new mysqli($host, $user, $password, null, $port);
		}
		else
			$link = @new mysqli($host, $user, $password);
		$success = $link->query('CREATE DATABASE `'.str_replace('`', '\\`', $database).'`');
		if ($dropit && ($link->query('DROP DATABASE `'.str_replace('`', '\\`', $database).'`') !== false))
			return true;
		return $success;
	}

	/**
	 * @see DbCore::disconnect()
	 */
	public function	disconnect()
	{
		@$this->link->close();
	}

	/**
	 * @see DbCore::_query()
	 */
	protected function _query($sql)
	{
		return $this->link->query($sql);
	}

	/**
	 * @see DbCore::nextRow()
	 */
	public function nextRow($result = false)
	{
		if (!$result)
			$result = $this->result;

		if (!is_object($result))
			return false;

		return $result->fetch_assoc();
	}

	/**
	 * @see DbCore::getAll()
	*/
	protected function getAll($result = false)
	{
		if (!$result)
			$result = $this->result;

		if (!is_object($result))
			return false;

		if (method_exists($result, 'fetch_all'))
			return $result->fetch_all(MYSQLI_ASSOC);
		else
		{
			$ret = array();

			while ($row = $this->nextRow($result))
				$ret[] = $row;

			return $ret;
		}
	}

	/**
	 * @see DbCore::_numRows()
	 */
	protected function _numRows($result)
	{
		return $result->num_rows;
	}

	/**
	 * @see DbCore::Insert_ID()
	 */
	public function	Insert_ID()
	{
		return $this->link->insert_id;
	}

	/**
	 * @see DbCore::Affected_Rows()
	 */
	public function	Affected_Rows()
	{
		return $this->link->affected_rows;
	}

	/**
	 * @see DbCore::getMsgError()
	 */
	public function getMsgError($query = false)
	{
		return $this->link->error;
	}

	/**
	 * @see DbCore::getNumberError()
	 */
	public function getNumberError()
	{
		return $this->link->errno;
	}

	/**
	 * @see DbCore::getVersion()
	 */
	public function getVersion()
	{
		return $this->getValue('SELECT VERSION()');
	}

	/**
	 * @see DbCore::_escape()
	 */
	public function _escape($str)
	{
		return $this->link->real_escape_string($str);
	}

	/**
	 * @see DbCore::set_db()
	 */
	public function set_db($db_name)
	{
		return $this->link->query('USE `'.bqSQL($db_name).'`');
	}

	/**
	 * @see Db::hasTableWithSamePrefix()
	 */
	public static function hasTableWithSamePrefix($server, $user, $pwd, $db, $prefix)
	{
		$link = @new mysqli($server, $user, $pwd, $db);
		if (mysqli_connect_error())
			return false;

		$sql = 'SHOW TABLES LIKE \''.$prefix.'%\'';
		$result = $link->query($sql);
		return (bool)$result->fetch_assoc();
	}

	/**
	 * @see Db::checkConnection()
	 */
	public static function tryToConnect($server, $user, $pwd, $db, $newDbLink = true, $engine = null, $timeout = 5)
	{
		$link = mysqli_init();
		if (!$link)
			return -1;

		if (!$link->options(MYSQLI_OPT_CONNECT_TIMEOUT, $timeout))
			return 1;

		// There is an @ because mysqli throw a warning when the database does not exists
		if (!@$link->real_connect($server, $user, $pwd, $db))
			return (mysqli_connect_errno() == 1049) ? 2 : 1;

		$link->close();
		return 0;
	}

	public function getBestEngine()
	{
		$value = 'InnoDB';

		$sql = 'SHOW VARIABLES WHERE Variable_name = \'have_innodb\'';
		$result = $this->link->query($sql);
		if (!$result)
			$value = 'MyISAM';
		$row = $result->fetch_assoc();
		if (!$row || strtolower($row['Value']) != 'yes')
			$value = 'MyISAM';

		/* MySQL >= 5.6 */
		$sql = 'SHOW ENGINES';
		$result = $this->link->query($sql);
		while ($row = $result->fetch_assoc())
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
		$link = @new mysqli($server, $user, $pwd, $db);
		if (mysqli_connect_error())
			return false;

		if ($engine === null)
			$engine = 'MyISAM';

		$result = $link->query('
		CREATE TABLE `'.$prefix.'test` (
			`test` tinyint(1) unsigned NOT NULL
		) ENGINE='.$engine);

		if (!$result)
			return $link->error;

		$link->query('DROP TABLE `'.$prefix.'test`');
		return true;
	}

	/**
	 * @see Db::checkEncoding()
	 */
	static public function tryUTF8($server, $user, $pwd)
	{
		$link = @new mysqli($server, $user, $pwd);
		$ret = $link->query("SET NAMES 'UTF8'");
		$link->close();
		return $ret;
	}

	public static function checkAutoIncrement($server, $user, $pwd)
	{
		$link = @new mysqli($server, $user, $pwd);
		$ret = (bool)(($result = $link->query('SELECT @@auto_increment_increment as aii')) && ($row = $result->fetch_assoc()) && $row['aii'] == 1);
		$ret &= (bool)(($result = $link->query('SELECT @@auto_increment_offset as aio')) && ($row = $result->fetch_assoc()) && $row['aio'] == 1);
		$link->close();
		return $ret;
	}
}
