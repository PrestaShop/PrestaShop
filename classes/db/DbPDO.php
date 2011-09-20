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
			$this->_link = new PDO('mysql:dbname='.$this->_database.';host='.$this->_server, $this->_user, $this->_password);
		}
		catch (PDOException $e)
		{
			die(Tools::displayError('Link to database cannot be established. ('.$e->getMessage().')'));
		}

		// UTF-8 support
		if ($this->_link->exec('SET NAMES \'utf8\'') === false)
			die(Tools::displayError('PrestaShop Fatal error: no utf-8 support. Please check your server configuration.'));

		return $this->_link;
	}

	/**
	 * @see DbCore::disconnect()
	 */
	public function	disconnect()
	{
		unset($this->_link);
	}

	/**
	 * @see DbCore::_query()
	 */
	protected function _query($sql)
	{
		return $this->_link->query($sql);
	}

	/**
	 * @see DbCore::nextRow()
	 */
	public function nextRow($result = false)
	{
		if (!$result)
			$result = $this->_result;
		return $result->fetch();
	}

	/**
	 * @see DbCore::_numRows()
	 */
	protected function _numRows($result)
	{
		return $result->rowCount();;
	}

	/**
	 * @see DbCore::Insert_ID()
	 */
	public function	Insert_ID()
	{
		return $this->_link->lastInsertId();
	}

	/**
	 * @see DbCore::Affected_Rows()
	 */
	public function	Affected_Rows()
	{
		return $this->_result->rowCount();
	}

	/**
	 * @see DbCore::getMsgError()
	 */
	public function getMsgError($query = false)
	{
		$error = $this->_link->errorInfo();
		return $error[2];
	}

	/**
	 * @see DbCore::getNumberError()
	 */
	public function getNumberError()
	{
		$error = $this->_link->errorInfo();
		return $error[1];
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
		return $this->_link->exec('USE '.pSQL($db_name));
	}

	/**
	 * @see DbCore::tryToConnect()
	 */
	static public function tryToConnect($server, $user, $pwd, $db)
	{
		try
		{
			$test = new PDO('mysql:dbname='.$db.';host='.$server, $user, $pwd);
		}
		catch (PDOException $e)
		{
			return 1;
		}
		unset($test);
		return 0;
	}

	/**
	 * @see DbCore::tryUTF8()
	 */
	static public function tryUTF8($server, $user, $pwd)
	{
		try
		{
			$test = new PDO('mysql:dbname='.$db.';host='.$server, $user, $pwd);
		}
		catch (PDOException $e)
		{
			return false;
		}
		$result = $test->exec('SET NAMES \'utf8\'');
		unset($test);

		return ($result === false) ? false : true;
	}
}
