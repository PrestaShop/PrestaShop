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

class DbMySQLiCore extends Db
{
	/**
	 * @see DbCore::connect()
	 */
	public function	connect()
	{
		$this->_link = new mysqli($this->_server, $this->_user, $this->_password, $this->_database);
		
		// Do not use object way for error because this work bad before PHP 5.2.9
		if (mysqli_connect_error())
			die(Tools::displayError('Link to database cannot be established : '.mysqli_connect_error()));

		// UTF-8 support
		if (!$this->_link->query('SET NAMES \'utf8\''))
			die(Tools::displayError('PrestaShop Fatal error: no utf-8 support. Please check your server configuration.'));

		return $this->_link;
	}
	
	/**
	 * @see DbCore::disconnect()
	 */
	public function	disconnect()
	{
		$this->_link->close();
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
		return $result->fetch_assoc();
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
		return $this->_link->insert_id;
	}

	/**
	 * @see DbCore::Affected_Rows()
	 */
	public function	Affected_Rows()
	{
		return $this->_link->affected_rows;
	}

	/**
	 * @see DbCore::getMsgError()
	 */
	public function getMsgError($query = false)
	{
		return $this->_link->error;
	}

	/**
	 * @see DbCore::getNumberError()
	 */
	public function getNumberError()
	{
		return $this->_link->errno;
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
		return $this->_link->real_escape_string($str);
	}
	
	/**
	 * @see DbCore::set_db()
	 */
	public function set_db($db_name)
	{
		return $this->_link->query('USE '.pSQL($db_name));
	}

	static public function tryToConnect($server, $user, $pwd, $db, $newDbLink = true)
	{
		$link = @new mysqli($server, $user, $pwd, $db);
		if (mysqli_connect_error())
			return 1;
		$link->close();
		return 0;
	}

	static public function tryUTF8($server, $user, $pwd)
	{
		$link = @new mysqli($server, $user, $pwd, $db);
		$ret = $link->query("SET NAMES 'UTF8'");
		$link->close();
		return $ret;
	}
}
