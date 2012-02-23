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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ThemeCore extends ObjectModel
{
	public $name;
	public $directory;

	/** @var int access rights of created folders (octal) */
	public static $access_rights = 0775;
	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'theme',
		'primary' => 'id_theme',
		'fields' => array(
			'name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64, 'required' => true),
			'directory' => array('type' => self::TYPE_STRING, 'validate' => 'isDirName', 'size' => 64, 'required' => true),
		),
	);

	public static function getThemes()
	{
		$themes = new Collection('Theme');
		$themes->orderBy('name');
		return $themes;
	}

	/**
	 * return an array of all available theme (installed or not)
	 * 
	 * @param boolean $installed_only
	 * @return array string (directory)
	 */
	public static function getAvailable($installed_only = true)
	{
		static $dirlist = array();
		$available_theme = array();

		if (empty($dirlist))
		{
			$themes = scandir(_PS_ALL_THEMES_DIR_);
			foreach ($themes as $theme)
				if (is_dir(_PS_ALL_THEMES_DIR_.DIRECTORY_SEPARATOR.$theme) && $theme[0] != '.')
					$dirlist[] = $theme;
		}

		if ($installed_only)
		{
			$themes = Theme::getThemes();
			foreach ($themes as $theme_obj)
				$themes_dir[] = $theme_obj->directory;
			foreach ($dirlist as $theme)
				if (false !== array_search($theme, $themes_dir))
					$available_theme[] = $theme;
		}
		else
			$available_theme = $dirlist;

		return $available_theme;

	}

	/**
	 * check if a theme is used by a shop
	 * 
	 * @return boolean
	 */
	public function isUsed()
	{
		return Db::getInstance()->getValue('SELECT count(*) 
			FROM '._DB_PREFIX_.'shop WHERE id_theme = '.(int)$this->id);
	}

	/**
	 * add only theme if the directory exists
	 * 
	 * @param bool $null_values
	 * @param bool $autodate
	 * @return boolean Insertion result
	 */
	public function add($autodate = true, $null_values = false)
	{
		if (!is_dir(_PS_ALL_THEMES_DIR_.$this->directory))
			return false;

		return parent::add($autodate, $null_values);
	}
}
