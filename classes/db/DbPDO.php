<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * This class is currently only here for tests
 *
 * @since 1.5.0
 */
class DbPDOCore extends Db
{
	/**
	 * @see DbCore::connect()
	 */
	public function	connect()
	{
		try
		{
			$dsn = 'mysql:dbname='.$this->database;
			if (strpos($this->server, ':') !== false)
			{
				list($server, $port) = explode(':', $this->server);
				$dsn .= ';host='.$server.';port='.$port;
			}
			else
				$dsn .= ';host='.$this->server;

			$this->link = new PDO($dsn, $this->user, $this->password);
		}
		catch (PDOException $e)
		{
			throw new PrestaShopDatabaseException(Tools::displayError('Link to database cannot be established. ('.$e->getMessage().')'));
		}

		// UTF-8 support
		if ($this->link->exec('SET NAMES \'utf8\'') === false)
			throw new PrestaShopDatabaseException(Tools::displayError('PrestaShop Fatal error: no utf-8 support. Please check your server configuration.'));

		return $this->link;
	}

	/**
	 * @see DbCore::disconnect()
	 */
	public function	disconnect()
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
		return $result->fetch(PDO::FETCH_ASSOC);
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
	public function	Insert_ID()
	{
		return $this->link->lastInsertId();
	}

	/**
	 * @see DbCore::Affected_Rows()
	 */
	public function	Affected_Rows()
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
		try
		{
			$link = @new PDO('mysql:dbname='.$db.';host='.$server, $user, $pwd);
		}
		catch (PDOException $e)
		{
			return false;
		}

		$sql = 'SHOW TABLES LIKE \''.$prefix.'%\'';
		$result = $link->query($sql);
		return (bool)$result->fetch();
	}

	/**
	 * @see Db::checkConnection()
	 */
	static public function tryToConnect($server, $user, $pwd, $db, $newDbLink = true, $engine = null)
	{
		try
		{
			$link = @new PDO('mysql:dbname='.$db.';host='.$server, $user, $pwd);
		}
		catch (PDOException $e)
		{
			return ($e->getCode() == 1049) ? 2 : 1;
		}

		if (strtolower($engine) == 'innodb')
		{
			$sql = 'SHOW VARIABLES WHERE Variable_name = \'have_innodb\'';
			$result = $link->query($sql);
			if (!$result)
				return 4;
			$row = $result->fetch();
			if (!$row || strtolower($row['Value']) != 'yes')
				return 4;
		}
		unset($link);
		return 0;
	}

	/**
	 * @see Db::checkEncoding()
	 */
	static public function tryUTF8($server, $user, $pwd)
	{
		try
		{
			$link = new PDO('mysql:host='.$server, $user, $pwd);
		}
		catch (PDOException $e)
		{
			return false;
		}
		$result = $link->exec('SET NAMES \'utf8\'');
		unset($link);

		return ($result === false) ? false : true;
	}
}
