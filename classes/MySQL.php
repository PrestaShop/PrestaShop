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

class MySQLCore extends Db
{
	public function	connect()
	{
		if (!defined('_PS_DEBUG_SQL_'))
			define('_PS_DEBUG_SQL_', false);
		if ($this->_link = mysql_connect($this->_server, $this->_user, $this->_password))
		{
			if(!$this->set_db($this->_database))
				die(Tools::displayError('The database selection cannot be made.'));
		}
		else
			die(Tools::displayError('Link to database cannot be established.'));
		/* UTF-8 support */
		if (!mysql_query('SET NAMES \'utf8\'', $this->_link))
			die(Tools::displayError('PrestaShop Fatal error: no utf-8 support. Please check your server configuration.'));
		/* Disable some MySQL limitations */
		mysql_query('SET GLOBAL SQL_MODE=\'\'', $this->_link);
		return $this->_link;
	}
	
	/* do not remove, useful for some modules */
	public function set_db($db_name) {
		return mysql_select_db($db_name, $this->_link);
	}
	
	public function	disconnect()
	{
		if ($this->_link)
			@mysql_close($this->_link);
		$this->_link = false;
	}
	
	public function	getRow($query, $use_cache = 1)
	{
		$this->_result = false;
		$this->_lastQuery = $query;
		if($use_cache AND _PS_CACHE_ENABLED_)
			if ($result = Cache::getInstance()->get(md5($query)))
			{
				$this->_lastCached = true;
				return $result;
			}
		if ($this->_link)
			if ($this->_result = mysql_query($query.' LIMIT 1', $this->_link))
			{
				$this->_lastCached = false;
				if (_PS_DEBUG_SQL_)
					$this->displayMySQLError($query);
				$result = mysql_fetch_assoc($this->_result);
				if ($use_cache = 1 AND _PS_CACHE_ENABLED_)
					Cache::getInstance()->setQuery($query, $result);
				return $result;
			}
		if (_PS_DEBUG_SQL_)
			$this->displayMySQLError($query);
		return false;
	}

	public function	getValue($query, $use_cache = 1)
	{
		$this->_result = false;
		$this->_lastQuery = $query;
		if ($use_cache AND _PS_CACHE_ENABLED_)
			if ($result = Cache::getInstance()->get(md5($query)))
			{
				$this->_lastCached = true;
				return $result;
			}
		if ($this->_link AND $this->_result = mysql_query($query.' LIMIT 1', $this->_link) AND is_array($tmpArray = mysql_fetch_assoc($this->_result)))
		{ 
			$this->_lastCached = false;
			$result =  array_shift($tmpArray);
			if($use_cache AND _PS_CACHE_ENABLED_)
				Cache::getInstance()->setQuery($query, $result);
			return $result;
		}
		return false;
	}
	
	public function	Execute($query, $use_cache = 1)
	{
		$this->_result = false;
		if ($this->_link)
		{
			$this->_result = mysql_query($query, $this->_link);
			if (_PS_DEBUG_SQL_)
				$this->displayMySQLError($query);
			if ($use_cache AND _PS_CACHE_ENABLED_)
				Cache::getInstance()->deleteQuery($query);
			return $this->_result;
		}
		if (_PS_DEBUG_SQL_)
			$this->displayMySQLError($query);
		return false;
	}
	
	/**
	 * ExecuteS return the result of $query as array, 
	 * or as mysqli_result if $array set to false
	 * 
	 * @param string $query query to execute
	 * @param boolean $array return an array instead of a mysql_result object
	 * @param int $use_cache if query has been already executed, use its result
	 * @return array or result object 
	 */
	public function	ExecuteS($query, $array = true, $use_cache = 1)
	{
		$this->_result = false;
		$this->_lastQuery = $query;
		if ($use_cache AND _PS_CACHE_ENABLED_)
			if ($array AND ($result = Cache::getInstance()->get(md5($query))))
			{
				$this->_lastCached = true;
				return $result;
			}
		if ($this->_link && $this->_result = mysql_query($query, $this->_link))
		{
			$this->_lastCached = false;
			if (_PS_DEBUG_SQL_)
				$this->displayMySQLError($query);
			if (!$array)
				return $this->_result;
			$resultArray = array();
			// Only SELECT queries and a few others return a valid resource usable with mysql_fetch_assoc
			if ($this->_result !== true)
				while ($row = mysql_fetch_assoc($this->_result))
					$resultArray[] = $row;
			if ($use_cache AND _PS_CACHE_ENABLED_)	
				Cache::getInstance()->setQuery($query, $resultArray);
			return $resultArray;
		}
		if (_PS_DEBUG_SQL_)
			$this->displayMySQLError($query);
		return false;
	}

	public function nextRow($result = false)
	{
		return mysql_fetch_assoc($result ? $result : $this->_result);
	}
	
	public function	delete($table, $where = false, $limit = false, $use_cache = 1)
	{
		$this->_result = false;
		if ($this->_link)
		{
			$query  = 'DELETE FROM `'.pSQL($table).'`'.($where ? ' WHERE '.$where : '').($limit ? ' LIMIT '.(int)($limit) : '');
			$res =  mysql_query($query, $this->_link);
			if ($use_cache AND _PS_CACHE_ENABLED_)
				Cache::getInstance()->deleteQuery($query);
			return $res;
		}
			
		return false;
	}
	
	public function	NumRows()
	{
		if (!$this->_lastCached AND $this->_link AND $this->_result)
		{
			$nrows = mysql_num_rows($this->_result);
			if (_PS_CACHE_ENABLED_)
				Cache::getInstance()->setNumRows(md5($this->_lastQuery), $nrows);
			return $nrows;
		}
		elseif (_PS_CACHE_ENABLED_ AND $this->_lastCached)
		{
			return Cache::getInstance()->getNumRows(md5($this->_lastQuery));
		}
	}
	
	public function	Insert_ID()
	{
		if ($this->_link)
			return mysql_insert_id($this->_link);
		return false;
	}
	
	public function	Affected_Rows()
	{
		if ($this->_link)
			return mysql_affected_rows($this->_link);
		return false;
	}

	protected function q($query, $use_cache = 1)
	{
		global $webservice_call;
		$this->_result = false;
		if ($this->_link)
		{
			$result =  mysql_query($query, $this->_link);
			$this->_lastQuery = $query;
			if ($webservice_call)
				$this->displayMySQLError($query);
			if ($use_cache AND _PS_CACHE_ENABLED_)
				Cache::getInstance()->deleteQuery($query);
			return $result;
		}
		return false;
	}
	
	/**
	 * Returns the text of the error message from previous MySQL operation
	 *
	 * @acces public
	 * @return string error
	 */
	public function getMsgError($query = false)
	{
		return mysql_error();
	}

	public function getNumberError()
	{
		return mysql_errno();
	}

	public function displayMySQLError($query = false)
	{
		global $webservice_call;
		if ($webservice_call && mysql_errno())
		{
			WebserviceRequest::getInstance()->setError(500, '[SQL Error] '.mysql_error().'. Query was : '.$query, 97);

		}
		elseif (_PS_DEBUG_SQL_ AND mysql_errno() AND !defined('PS_INSTALLATION_IN_PROGRESS'))
		{
			if ($query)
				die(Tools::displayError(mysql_error().'<br /><br /><pre>'.$query.'</pre>'));
			die(Tools::displayError((mysql_error())));
		}
	}

	static public function tryToConnect($server, $user, $pwd, $db)
	{
		if (!$link = @mysql_connect($server, $user, $pwd))
			return 1;
		if (!@mysql_select_db($db, $link))
			return 2;
		@mysql_close($link);
		return 0;
	}

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
