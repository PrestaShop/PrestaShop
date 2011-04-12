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

class ContactCore extends ObjectModel
{
	public 		$id;
	
	/** @var string Name */
	public 		$name;
	
	/** @var string e-mail */
	public 		$email;

	/** @var string Detailed description */
	public 		$description;
	
	public 		$customer_service;
	
 	protected 	$fieldsRequired = array();
 	protected 	$fieldsSize = array('email' => 128);
 	protected 	$fieldsValidate = array('email' => 'isEmail', 'customer_service' => 'isBool');
 	protected 	$fieldsRequiredLang = array('name');
 	protected 	$fieldsSizeLang = array('name' => 32);
 	protected 	$fieldsValidateLang = array('name' => 'isGenericName', 'description' => 'isCleanHtml');
	
	protected 	$table = 'contact';
	protected 	$identifier = 'id_contact';

	public function getFields()
	{
		parent::validateFields();
		$fields['email'] = pSQL($this->email);
		$fields['customer_service'] = (int)($this->customer_service);
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
		return parent::getTranslationsFields(array('name', 'description'));
	}
	
	/**
	  * Return available contacts
	  *
	  * @param integer $id_lang Language ID
	  * @return array Contacts
	  */
	static public function getContacts($id_lang)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'contact` c
		LEFT JOIN `'._DB_PREFIX_.'contact_lang` cl ON c.`id_contact` = cl.`id_contact`
		WHERE cl.`id_lang` = '.(int)($id_lang).'
		ORDER BY `name` ASC');
	}
}

