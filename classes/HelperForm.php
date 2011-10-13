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
*  @version  Release: $Revision: 9194 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class HelperFormCore extends Helper
{

	public $id;

	public $first_call = true;

	/**
	 * @var array of forms fields
	 * Usage :
	 *
	 */
	protected $fields_form = array();

	public $fields_value = array();

	public $table;

	public $token;

	public static $currentIndex;

	public $tpl = 'form.tpl';

	public function generateForm($fields_form)
	{
		$this->fields_form = $fields_form;

		return $this->displayForm();
	}

	public function displayForm()
	{
		$cookie = $this->context->cookie;
		$allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		if ($allow_employee_form_lang && !$cookie->employee_form_lang)
			$cookie->employee_form_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		$use_lang_from_cookie = false;
		$languages = Language::getLanguages(false);
		if ($allow_employee_form_lang)
			foreach ($languages as $lang)
				if ($cookie->employee_form_lang == $lang['id_lang'])
					$use_lang_from_cookie = true;
		if (!$use_lang_from_cookie)
			$default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
		else
			$default_form_language = (int)$cookie->employee_form_lang;

		$this->context->smarty->assign(array(
			'firstCall' => $this->first_call,
			'current' => self::$currentIndex,
			'token' => $this->token,
			'table' => $this->table,
			'defaultFormLanguage' => $default_form_language,
			'languages' => $languages,
			'allowEmployeeFormLang' => $allow_employee_form_lang,
			'form_id' => $this->id,
			'back' => Tools::getValue('back'),
			'fields' => $this->fields_form,
			'fields_value' => $this->fields_value,
			'requiredFields' => $this->getFieldsRequired(),
			'vat_number' => file_exists(_PS_MODULE_DIR_.'vatnumber/ajax.php'),
			'module_dir' => _MODULE_DIR_,
			'contains_states' => (isset($this->fields_value['id_country']) && isset($this->fields_value['id_state'])) ? Country::containsStates($this->fields_value['id_country']) : null
		));

		return $this->context->smarty->fetch(_PS_ADMIN_DIR_.'/themes/template/'.$this->tpl);
	}

	public function getFieldsRequired()
	{
		if (isset($this->fields_form['input']))
			foreach ($this->fields_form['input'] as $input)
				if (array_key_exists('required', $input) && $input['required'])
					return true;

		return false;
	}
}