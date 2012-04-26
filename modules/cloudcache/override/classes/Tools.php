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
*  @version  Release: $Revision: 12823 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!class_exists('CloudCache') && file_exists(dirname(__FILE__).'/../../modules/cloudcache/cloudcache.php'))
	require(dirname(__FILE__).'/../../modules/cloudcache/cloudcache.php');

class Tools extends ToolsCore
{
	/** @var _totalServerCount Total count of all servers */
	private static $_totalServerCount = 0;
	/** @var _servers Array containing all the media servers by type */
	private static $_servers = null;
	/** @var _serversCount Array countaining the count of servers by type */
	private static $_serversCount = null;
	/** @var _fileTypes Available file types */
	private static $_fileTypes = null;
	/** @var _activatedModule Flag weither or not the module is active */
	private static $_activatedModule = false;

	public static $id_group_shop = 1;
	public static $id_shop = 1;

	/**
	 * @brief Init the statics needed by getMediaServer
	 */
	private static function _initServers()
	{
		require_once(dirname(__FILE__).'/../../modules/cloudcache/backward_compatibility/backward.php');

		$context = Context::getContext();
		self::$id_shop = $context->shop->id;
		self::$id_group_shop = $context->shop->id_group_shop;

		// Init the statics
		self::$_servers = array();
		self::$_serversCount = array();
		self::$_fileTypes = array(CLOUDCACHE_FILE_TYPE_IMG, CLOUDCACHE_FILE_TYPE_JS,
												CLOUDCACHE_FILE_TYPE_CSS, CLOUDCACHE_FILE_TYPE_OTHER,
												CLOUDCACHE_FILE_TYPE_ALL);

		// check if the module is active
		self::$_activatedModule = Configuration::get('CLOUDCACHE_API_ACTIVE');

		foreach (self::$_fileTypes as $type)
		{
			self::$_servers[$type] = array();
			self::$_serversCount[$type] = 0;
		}

		$d = Db::getInstance()->executeS('SELECT `cdn_url`, `file_type`
								 FROM `'._DB_PREFIX_.'cloudcache_zone`
								 WHERE `file_type` != \''.CLOUDCACHE_FILE_TYPE_UNASSOCIATED.'\' AND `id_shop` = '.(int)self::$id_shop);

		$allOnly = false;
		foreach ($d as $line)
			if ($line['file_type'] == CLOUDCACHE_FILE_TYPE_ALL)
			{
				self::$_servers[CLOUDCACHE_FILE_TYPE_ALL][] = pSQL($line['cdn_url']);
				self::$_serversCount[CLOUDCACHE_FILE_TYPE_ALL]++;
				self::$_totalServerCount++;
				$allOnly = true;
			}

		foreach ($d as $line)
			if ($line['file_type'] && !$allOnly)
			{
				self::$_servers[$line['file_type']][] = $line['cdn_url'];
				self::$_serversCount[$line['file_type']]++;
				self::$_totalServerCount++;
			}
	}

	public static function addJS($js_uri)
	{
		parent::addJS($js_uri);
		global $js_files;

		foreach ($js_files as &$file)
			if (!preg_match('/^http(s?):\/\//i', $file))
				$file = 'http://'.self::getMediaServer($file).$file;
	}

	public static function addCSS($css_uri, $css_media_type = 'all')
	{
		parent::addCSS($css_uri, $css_media_type);
		global $css_files;

		$new = array();
		foreach ($css_files as $key => $file)
		{
			if (!preg_match('/^http(s?):\/\//i', $key))
				$key = 'http://'.self::getMediaServer($key).$key;
			$new[$key] = $file;
		}
		$css_files = $new;
	}

	/**
	 * @brief Retrieve the media server to use
	 *
	 * @param filename Name of the file to serve (acually, part of the path)
	 *
	 * @todo Check performences
	 *
	 * @return URL of the server to use.
	 */
	public static function getMediaServer($filename)
	{
		// Override default behavior only if module is active
		if (!class_exists('CloudCache'))
			include(dirname(__FILE__).'/../../modules/cloudcache/cloudcache.php');

		$module = new CloudCache();

		if (!$module->active)
			return parent::getMediaServer($filename);

		// Init the server list if needed
		if (!self::$_servers)
			self::_initServers();

		if (!self::$_activatedModule)
			return parent::getMediaServer($filename);

		// If there is a least one ALL server, then use one of them
		if (self::$_serversCount[CLOUDCACHE_FILE_TYPE_ALL])
					// Return one of those server
					return (self::$_servers[CLOUDCACHE_FILE_TYPE_ALL][(abs(crc32($filename)) %
								self::$_serversCount[CLOUDCACHE_FILE_TYPE_ALL])]);


		// If there is servers, then use them
		if (self::$_totalServerCount)
		{
			// Loop on the file types to find the current one
			foreach (self::$_fileTypes as $type)
				// If we find the type in the filename, then it is our
				if (strstr($filename, $type) && self::$_serversCount[$type])
				{
					// Return one of those server
					return (self::$_servers[$type][(abs(crc32($filename)) %
								self::$_serversCount[$type])]);
				}

			// If no file type found, then it is 'other'
			// If there is server setted for the 'other' type, use it
			if (self::$_serversCount[CLOUDCACHE_FILE_TYPE_OTHER])
				// Return one of the server setted up
				return (self::$_servers[$type][(abs(crc32($filename)) %
							self::$_serversCount[$type])]);
		}

		// If there is no server setted up, then use the parent method
		return parent::getMediaServer($filename);
	}
}