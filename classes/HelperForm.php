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

	public function generateForm($fields_form)
	{
		$this->fields_form = $fields_form;

		return $this->displayForm();
	}

	public function displayForm()
	{
		$allowEmployeeFormLang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		if ($allowEmployeeFormLang && !$this->context->cookie->employee_form_lang)
			$this->context->cookie->employee_form_lang = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$useLangFromCookie = false;
		$languages = Language::getLanguages(false);
		if ($allowEmployeeFormLang)
			foreach ($languages as $lang)
				if ($this->context->cookie->employee_form_lang == $lang['id_lang'])
					$useLangFromCookie = true;
		if (!$useLangFromCookie)
			$defaultFormLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		else
			$defaultFormLanguage = (int)($this->context->cookie->employee_form_lang);

		info($this->fields_value);
		$this->context->smarty->assign(array(
			'firstCall' => $this->first_call,
			'current' => self::$currentIndex,
			'token' => $this->token,
			'table' => $this->table,
			'defaultFormLanguage' => $defaultFormLanguage,
			'languages' => $languages,
			'allowEmployeeFormLang' => $allowEmployeeFormLang,
			'form_id' => $this->id,
			'back' => Tools::getValue('back'),
			'fields' => $this->fields_form,
			'fields_value' => $this->fields_value,
			'requiredFields' => $this->getFieldsRequired()
		));

		return $this->context->smarty->fetch(_PS_ADMIN_DIR_.'/themes/template/form.tpl');
	}

	public function getFieldsRequired()
	{
		foreach ($this->fields_form['input'] as $input)
			if (array_key_exists('required', $input) && $input['required'])
				return true;

		return false;
	}
}