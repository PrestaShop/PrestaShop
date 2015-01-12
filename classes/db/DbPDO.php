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
class DbPDOCore extends Db
{
	protected static function _getPDO($host, $user, $password, $dbname, $timeout = 5)
	{
		$dsn = 'mysql:';
		if ($dbname)
			$dsn .= 'dbname='.$dbname.';';
		if (preg_match('/^(.*):([0-9]+)$/', $host, $matches))
			$dsn .= 'host='.$matches[1].';port='.$matches[2];
		elseif (preg_match('#^.*:(/.*)$#', $host, $matches))
			$dsn .= 'unix_socket='.$matches[1];
		else
			$dsn .= 'host='.$host;

		return new PDO($dsn, $user, $password, array(PDO::ATTR_TIMEOUT => $timeout, PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
	}

	public static function createDatabase($host, $user, $password, $dbname, $dropit = false)
	{
		try {
			$link = DbPDO::_getPDO($host, $user, $password, false);
			$success = $link->exec('CREATE DATABASE `'.str_replace('`', '\\`', $dbname).'`');
			if ($dropit && ($link->exec('DROP DATABASE `'.str_replace('`', '\\`', $dbname).'`') !== false))
				return true;
		} catch (PDOException $e) {
			return false;
		}
		return $success;
	}

	/**
	 * @see DbCore::connect()
	 */
	public function connect()
	{
		try {
			$this->link = $this->_getPDO($this->server, $this->user, $this->password, $this->database, 5);
		} catch (PDOException $e) {
			die(sprintf(Tools::displayError('Link to database cannot be established: %s'), utf8_encode($e->getMessage())));
		}

		// UTF-8 support
		if ($this->link->exec('SET NAMES \'utf8\'') === false)
			die(Tools::displayError('PrestaShop Fatal error: no utf-8 support. Please check your server configuration.'));

		return $this->link;
	}

	/**
	 * @see DbCore::disconnect()
	 */
	public function disconnect()
	{
		unset($this->link);
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

		return $result->fetch(PDO::FETCH_ASSOC);
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

		return $result->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * @see DbCore::_numRows()
	 */
	protected function _numRows($result)
	{
		return $result->rowCount();
	}

	/**
	 * @see DbCore::Insert_ID()
	 */
	public function Insert_ID()
	{
		return $this->link->lastInsertId();
	}

	/**
	 * @see DbCore::Affected_Rows()
	 */
	public function Affected_Rows()
	{
		return $this->result->rowCount();
	}

	/**
	 * @see DbCore::getMsgError()
	 */
	public function getMsgError($query = false)
	{
		$error = $this->link->errorInfo();
		return ($error[0] == '00000') ? '' : $error[2];
	}

	/**
	 * @see DbCore::getNumberError()
	 */
	public function getNumberError()
	{
		$error = $this->link->errorInfo();
		return isset($error[1]) ? $error[1] : 0;
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
		$search = array("\\", "\0", "\n", "\r", "\x1a", "'", '"');
		$replace = array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"');
		return str_replace($search, $replace, $str);
	}

	/**
	 * @see DbCore::set_db()
	 */
	public function set_db($db_name)
	{
		return $this->link->exec('USE '.pSQL($db_name));
	}

	/**
	 * @see Db::hasTableWithSamePrefix()
	 */
	public static function hasTableWithSamePrefix($server, $user, $pwd, $db, $prefix)
	{
		try {
			$link = DbPDO::_getPDO($server, $user, $pwd, $db, 5);
		} catch (PDOException $e) {
			return false;
		}

		$sql = 'SHOW TABLES LIKE \''.$prefix.'%\'';
		$result = $link->query($sql);
		return (bool)$result->fetch();
	}

	public static function checkCreatePrivilege($server, $user, $pwd, $db, $prefix, $engine = null)
	{
		try {
			$link = DbPDO::_getPDO($server, $user, $pwd, $db, 5);
		} catch (PDOException $e) {
			return false;
		}

		if ($engine === null)
			$engine = 'MyISAM';

		$result = $link->query('
		CREATE TABLE `'.$prefix.'test` (
			`test` tinyint(1) unsigned NOT NULL
		) ENGINE='.$engine);
		if (!$result)
		{
			$error = $link->errorInfo();
			return $error[2];
		}
		$link->query('DROP TABLE `'.$prefix.'test`');
		return true;
	}

	/**
	 * @see Db::checkConnection()
	 */
	public static function tryToConnect($server, $user, $pwd, $db, $newDbLink = true, $engine = null, $timeout = 5)
	{
		try {
			$link = DbPDO::_getPDO($server, $user, $pwd, $db, $timeout);
		} catch (PDOException $e) {
			// hhvm wrongly reports error status 42000 when the database does not exist - might change in the future
			return ($e->getCode() == 1049 || (defined('HHVM_VERSION') && $e->getCode() == 42000)) ? 2 : 1;
		}
		unset($link);
		return 0;
	}

	public function getBestEngine()
	{
		$value = 'InnoDB';

		$sql = 'SHOW VARIABLES WHERE Variable_name = \'have_innodb\'';
		$result = $this->link->query($sql);
		if (!$result)
			$value = 'MyISAM';
		$row = $result->fetch();
		if (!$row || strtolower($row['Value']) != 'yes')
			$value = 'MyISAM';

		/* MySQL >= 5.6 */
		$sql = 'SHOW ENGINES';
		$result = $this->link->query($sql);
		while ($row = $result->fetch())
			if ($row['Engine'] == 'InnoDB')
			{
				if (in_array($row['Support'], array('DEFAULT', 'YES')))
					$value = 'InnoDB';
				break;
			}
		return $value;
	}

	/**
	 * @see Db::checkEncoding()
	 */
	public static function tryUTF8($server, $user, $pwd)
	{
		try {
			$link = DbPDO::_getPDO($server, $user, $pwd, false, 5);
		} catch (PDOException $e) {
			return false;
		}
		$result = $link->exec('SET NAMES \'utf8\'');
		unset($link);

		return ($result === false) ? false : true;
	}

	public static function checkAutoIncrement($server, $user, $pwd)
	{
		try {
			$link = DbPDO::_getPDO($server, $user, $pwd, false, 5);
		} catch (PDOException $e) {
			return false;
		}
		$ret = (bool)(($result = $link->query('SELECT @@auto_increment_increment as aii')) && ($row = $result->fetch()) && $row['aii'] == 1);
		$ret &= (bool)(($result = $link->query('SELECT @@auto_increment_offset as aio')) && ($row = $result->fetch()) && $row['aio'] == 1);
		unset($link);
		return $ret;
	}
}
