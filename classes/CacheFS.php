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

class CacheFSCore extends Cache {
	
	protected $_depth;
	
	protected function __construct()
	{
		parent::__construct();
		return $this->_init();
	}
	
	protected function _init()
	{
		$this->_depth = Db::getInstance()->getValue('SELECT value FROM '._DB_PREFIX_.'configuration WHERE name=\'PS_CACHEFS_DIRECTORY_DEPTH\'', false);
		return $this->_setKeys();
	}

	public function set($key, $value, $expire = 0)
	{
		$path = _PS_CACHEFS_DIRECTORY_;
		for ($i = 0; $i < $this->_depth; $i++)
		{
			$path.=$key[$i].'/';
		}
		if(file_put_contents($path.$key, serialize($value)))
		{
			$this->_keysCached[$key] = true;
			return $key;
		}
		return false;
	}
	
	public function setNumRows($key, $value, $expire = 0)
	{
		return $this->set($key.'_nrows', $value, $expire);
	}
	
	public function getNumRows($key)
	{
		return $this->get($key.'_nrows');
	}

	public function get($key)
	{
		if (!isset($this->_keysCached[$key]))
			return false;
		$path = _PS_CACHEFS_DIRECTORY_;
		for ($i = 0; $i < $this->_depth; $i++)
			$path.=$key[$i].'/';
		if (!file_exists($path.$key))
		{
			unset($this->_keysCached[$key]);
			return false;
		}
		$file = file_get_contents($path.$key);
		return unserialize($file);
	}

	protected function _setKeys()
	{
		if (file_exists(_PS_CACHEFS_DIRECTORY_.'keysCached'))
		{
			$file = file_get_contents(_PS_CACHEFS_DIRECTORY_.'keysCached');
			$this->_keysCached =	unserialize($file);
		}
		if (file_exists(_PS_CACHEFS_DIRECTORY_.'tablesCached'))
		{
			$file = file_get_contents(_PS_CACHEFS_DIRECTORY_.'tablesCached');
			$this->_tablesCached = unserialize($file);
		}
		return true;
	}

	public function setQuery($query, $result)
	{
		$md5_query = md5($query);
		if (isset($this->_keysCached[$md5_query]))
			return true;
		if ($this->isBlacklist($query))
			return true;
		$key = $this->set($md5_query, $result);
		if (preg_match_all('/('._DB_PREFIX_.'[a-z_-]*)`?.*/i', $query, $res))
			foreach($res[1] AS $table)
				if(!isset($this->_tablesCached[$table][$key]))
					$this->_tablesCached[$table][$key] = true;
	}

	public function delete($key, $timeout = 0)
	{
		$path = _PS_CACHEFS_DIRECTORY_;
		if (!isset($this->_keysCached[$key]))
			return;
		for ($i = 0; $i < $this->_depth; $i++)
			$path.=$key[$i].'/';
		if (!file_exists($path.$key))
			return true;
		if (!unlink($path.$key))
			return false;
		unset($this->_keysCached[$key]);
		return true;
	}

	public function deleteQuery($query)
	{

		if (preg_match_all('/('._DB_PREFIX_.'[a-z_-]*)`?.*/i', $query, $res))
			foreach ($res[1] AS $table)
				if (isset($this->_tablesCached[$table]))
				{
					foreach ($this->_tablesCached[$table] AS $fsKey => $foo)
					{
						$this->delete($fsKey);
						$this->delete($fsKey.'_nrows');
					}
					unset($this->_tablesCached[$table]);
				}
	}

	public function flush()
	{
	}

	public function __destruct()
	{
		parent::__destruct();
		file_put_contents(_PS_CACHEFS_DIRECTORY_.'keysCached', serialize($this->_keysCached));
		file_put_contents(_PS_CACHEFS_DIRECTORY_.'tablesCached', serialize($this->_tablesCached));
	}

	public static function deleteCacheDirectory()
	{
		Tools::deleteDirectory(_PS_CACHEFS_DIRECTORY_, false);
	}

	public static function createCacheDirectories($level_depth, $directory = false)
	{
		if (!$directory)
			$directory = _PS_CACHEFS_DIRECTORY_;
		$chars = '0123456789abcdefghijklmnopqrstuvwxyz';
		for ($i = 0; $i < strlen($chars); $i++)
		{
			$new_dir = $directory.$chars[$i].'/';
			if (mkdir($new_dir))
				if (chmod($new_dir, 0777))
					if ($level_depth - 1 > 0)
						self::createCacheDirectories($level_depth - 1, $new_dir);
		}
	}
}
