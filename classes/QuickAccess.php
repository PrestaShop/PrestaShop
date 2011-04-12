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

class QuickAccessCore extends ObjectModel
{
 	/** @var string Name */
	public 		$name;
	
	/** @var string Link */
	public 		$link;
	
	/** @var boolean New windows or not */
	public 		$new_window;
	
 	protected 	$fieldsRequired = array('link', 'new_window');
 	protected 	$fieldsSize = array('link' => 128);
 	protected 	$fieldsValidate = array('link' => 'isUrl', 'new_window' => 'isBool');
 	protected 	$fieldsRequiredLang = array('name');
 	protected 	$fieldsSizeLang = array('name' => 32);
 	protected 	$fieldsValidateLang = array('name' => 'isGenericName');

	protected 	$table = 'quick_access';
	protected 	$identifier = 'id_quick_access';
		
	public function getFields()
	{
		parent::validateFields();
		$fields['link'] = pSQL($this->link);
		$fields['new_window'] = (int)($this->new_window);
		return $fields;
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
	* Get all available quick_accesses
	*
	* @return array QuickAccesses
	*/
	static public function getQuickAccesses($id_lang)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'quick_access` qa
		LEFT JOIN `'._DB_PREFIX_.'quick_access_lang` qal ON (qa.`id_quick_access` = qal.`id_quick_access` AND qal.`id_lang` = '.(int)($id_lang).')
		ORDER BY `name` ASC');
	}
}

