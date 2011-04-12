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

class ProfileCore extends ObjectModel
{
 	/** @var string Name */
	public 		$name;
	
 	protected 	$fieldsRequiredLang = array('name');
 	protected 	$fieldsSizeLang = array('name' => 32);
 	protected 	$fieldsValidateLang = array('name' => 'isGenericName');

	protected 	$table = 'profile';
	protected 	$identifier = 'id_profile';
		
	public function getFields()
	{
		return array('id_profile' => $this->id);
	}
	
	/**
	* Check then return multilingual fields for database interaction
	*
	* @return array Multilingual fields
	*/
	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();
		return parent::getTranslationsFields(array('name'));
	}
	
	/**
	* Get all available profiles
	*
	* @return array Profiles
	*/
	static public function getProfiles($id_lang)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT p.`id_profile`, `name`
		FROM `'._DB_PREFIX_.'profile` p
		LEFT JOIN `'._DB_PREFIX_.'profile_lang` pl ON (p.`id_profile` = pl.`id_profile` AND `id_lang` = '.(int)($id_lang).')
		ORDER BY `name` ASC');
	}

	/**
	* Get the current profile name
	*
	* @return string Profile
	*/
	static public function getProfile($id_profile, $id_lang = NULL)
	{
		if ($id_lang == NULL)
			$id_lang = Configuration::get('PS_LANG_DEFAULT');
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `name` 
		FROM `'._DB_PREFIX_.'profile` p 
		LEFT JOIN `'._DB_PREFIX_.'profile_lang` pl ON (p.`id_profile` = pl.`id_profile`) 
		WHERE p.`id_profile` = '.(int)($id_profile).'
		AND pl.`id_lang` = '.(int)($id_lang));
	}

	
	public function add($autodate = true, $nullValues = false)
	{
	 	if (parent::add($autodate, true))
			return Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'access (SELECT '.(int)($this->id).', id_tab, 0, 0, 0, 0 FROM '._DB_PREFIX_.'tab)');
		return false;
	}
	
	public function delete()
	{
	 	if (parent::delete())
	 	 	return Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'access` WHERE `id_profile` = '.(int)($this->id));
		return false;
	}

	public static function getProfileAccess($id_profile, $id_tab)
	{
	 	/* Accesses selection */
	 	return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `view`, `add`, `edit`, `delete`
		FROM `'._DB_PREFIX_.'access`
		WHERE `id_profile` = '.(int)($id_profile).' AND `id_tab` = '.(int)($id_tab));
	}

	public static function getProfileAccesses($id_profile)
	{
	 	/* Accesses selection */
	 	$accesses = Db::getInstance()->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'access`
		WHERE `id_profile` = '.(int)($id_profile));

	 	$result = array();
		foreach($accesses AS $access) {
		 	/* If it is the first time we meet this tab we prepare it */
		 	if (!isset($result[$access['id_tab']]))
		 		$result[$access['id_tab']] = array();
			$result[$access['id_tab']] = $access;
		}
		return $result;
	}
}


