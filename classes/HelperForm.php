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

	/**
	 * Used to override default 'submitAdd' parameter in form action attribute
	 * @var string
	 */
	public $submit_action = '';

	/**
	 * @var bool
	 * Usage : Set the value to true if you want to simply remove the back button
	 */
	public $no_back = false;

	public $token;

	public $languages = null;
	public $default_form_language = null;
	public $allow_employee_form_lang = null;

	public $tpl = 'helper/form/form.tpl';

	public function generateForm($fields_form)
	{
		$this->fields_form = $fields_form;

		return $this->displayForm();
	}

	public function displayForm()
	{
		if ($this->submit_action == '')
			$this->submit_action = 'submitAdd'.$this->table;

		$this->context->smarty->assign(array(
			'submit_action' => $this->submit_action,
			'toolbar_btn' => $this->toolbar_btn,
			'firstCall' => $this->first_call,
			'current' => $this->currentIndex,
			'token' => $this->token,
			'table' => $this->table,
			'languages' => $this->languages,
			'defaultFormLanguage' => $this->default_form_language,
			'allowEmployeeFormLang' => $this->allow_employee_form_lang,
			'form_id' => $this->id,
			'back' => Tools::getValue('back'),
			'no_back' => $this->no_back,
			'fields' => $this->fields_form,
			'fields_value' => $this->fields_value,
			'required_fields' => $this->getFieldsRequired(),
			'vat_number' => file_exists(_PS_MODULE_DIR_.'vatnumber/ajax.php'),
			'module_dir' => _MODULE_DIR_,
			'contains_states' => (isset($this->fields_value['id_country']) && isset($this->fields_value['id_state'])) ? Country::containsStates($this->fields_value['id_country']) : null,
			'asso_shop' => (isset($this->fields_form['asso_shop']) && $this->fields_form['asso_shop']) ? $this->displayAssoShop() : null,
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
