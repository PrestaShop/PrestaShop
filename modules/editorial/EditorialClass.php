<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class		EditorialClass extends ObjectModel
{
	/** @var integer editorial id*/
	public		$id = 1;
	
	/** @var string body_title*/
	public		$body_home_logo_link;

	/** @var string body_title*/
	public		$body_title;

	/** @var string body_title*/
	public		$body_subheading;

	/** @var string body_title*/
	public		$body_paragraph;

	/** @var string body_title*/
	public		$body_logo_subheading;
	
	protected 	$table = 'editorial';
	protected 	$identifier = 'id_editorial';
	
	protected 	$fieldsValidate = array('body_home_logo_link' => 'isUrl');
	protected 	$fieldsValidateLang = array(
		'body_title' => 'isGenericName',
		'body_subheading' => 'isGenericName',
		'body_paragraph' => 'isCleanHtml',
		'body_logo_subheading' => 'isGenericName');
	
	/**
	  * Check then return multilingual fields for database interaction
	  *
	  * @return array Multilingual fields
	  */
	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();

		$fieldsArray = array('body_title', 'body_subheading', 'body_paragraph', 'body_logo_subheading');
		$fields = array();
		$languages = Language::getLanguages(false);
		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		foreach ($languages as $language)
		{
			$fields[$language['id_lang']]['id_lang'] = (int)($language['id_lang']);
			$fields[$language['id_lang']][$this->identifier] = (int)($this->id);
			foreach ($fieldsArray as $field)
			{
				if (!Validate::isTableOrIdentifier($field))
					die(Tools::displayError());
				if (isset($this->{$field}[$language['id_lang']]) AND !empty($this->{$field}[$language['id_lang']]))
					$fields[$language['id_lang']][$field] = pSQL($this->{$field}[$language['id_lang']], true);
				elseif (in_array($field, $this->fieldsRequiredLang))
					$fields[$language['id_lang']][$field] = pSQL($this->{$field}[$defaultLanguage], true);
				else
					$fields[$language['id_lang']][$field] = '';
			}
		}
		return $fields;
	}
	
	public function copyFromPost()
	{
		/* Classical fields */
		foreach ($_POST AS $key => $value)
			if (key_exists($key, $this) AND $key != 'id_'.$this->table)
				$this->{$key} = $value;

		/* Multilingual fields */
		if (sizeof($this->fieldsValidateLang))
		{
			$languages = Language::getLanguages(false);
			foreach ($languages AS $language)
				foreach ($this->fieldsValidateLang AS $field => $validation)
					if (isset($_POST[$field.'_'.(int)($language['id_lang'])]))
						$this->{$field}[(int)($language['id_lang'])] = $_POST[$field.'_'.(int)($language['id_lang'])];
		}
	}
	
	public function getFields()
	{
		parent::validateFields();
		$fields['id_editorial'] = (int)($this->id);
		$fields['body_home_logo_link'] = pSQL($this->body_home_logo_link);
		return $fields;
	}
}
