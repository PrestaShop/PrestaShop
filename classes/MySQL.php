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
*  @version  Release: $Revision: 6856 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class MySQLCore extends Db
{
	/**
	 * @see DbCore::connect()
	 */
	public function	connect()
	{
		if (!defined('_PS_MYSQL_REAL_ESCAPE_STRING_'))
			define('_PS_MYSQL_REAL_ESCAPE_STRING_', function_exists('mysql_real_escape_string'));
		
		if (!$this->_link = mysql_connect($this->_server, $this->_user, $this->_password))
			die(Tools::displayError('Link to database cannot be established.'));

		if (!$this->set_db($this->_database))
			die(Tools::displayError('The database selection cannot be made.'));

		// UTF-8 support
		if (!mysql_query('SET NAMES \'utf8\'', $this->_link))
			die(Tools::displayError('PrestaShop Fatal error: no utf-8 support. Please check your server configuration.'));

		return $this->_link;
	}
	
	/**
	 * @see DbCore::disconnect()
	 */
	public function	disconnect()
	{
		mysql_close($this->_link);
	}
	
	/**
	 * @see DbCore::_query()
	 */
	protected function _query($sql)
	{
		return mysql_query($sql, $this->_link);
	}

	/**
	 * @see DbCore::nextRow()
	 */
	public function nextRow($result = false)
	{
		return mysql_fetch_assoc($result ? $result : $this->_result);
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
	public function	Insert_ID()
	{
		return mysql_insert_id($this->_link);
	}

	/**
	 * @see DbCore::Affected_Rows()
	 */
	public function	Affected_Rows()
	{
		return mysql_affected_rows($this->_link);
	}

	/**
	 * @see DbCore::getMsgError()
	 */
	public function getMsgError($query = false)
	{
		return mysql_error($this->_link);
	}

	/**
	 * @see DbCore::getNumberError()
	 */
	public function getNumberError()
	{
		return mysql_errno($this->_link);
	}
	
	/**
	 * @see DbCore::getVersion()
	 */
	public function getVersion()
	{
		return mysql_get_server_info($this->_link);
	}
	
	/**
	 * @see DbCore::_escape()
	 */
	public function _escape($str)
	{
		return _PS_MYSQL_REAL_ESCAPE_STRING_ ? mysql_real_escape_string($str, $this->_link) : addslashes($str);
	}
	
	/**
	 * @see DbCore::set_db()
	 */
	public function set_db($db_name)
	{
		return mysql_select_db($db_name, $this->_link);
	}

	/**
	 * tryToConnect return 0 if the connection succeed and the database can be selected.
	 * @since 1.4.4.0, the parameter $newDbLink (default true) has been added.
	 * 
	 * @param string $server mysql server name
	 * @param string $user mysql user
	 * @param string $pwd mysql user password
	 * @param string $db mysql database name
	 * @param boolean $newDbLink if set to true, the function will not create a new link if one already exists.
	 * @return integer
	 */
	static public function tryToConnect($server, $user, $pwd, $db, $newDbLink = true)
	{
		if (!$link = @mysql_connect($server, $user, $pwd, $newDbLink))
			return 1;
		if (!@mysql_select_db($db, $link))
			return 2;
		@mysql_close($link);
		return 0;
	}

	/**
	 * @see DbCore::tryUTF8()
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
