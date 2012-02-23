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
*  @version  Release: $Revision: 7060 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminContactControllerCore extends AdminController
{
	public function __construct()
	{
		$this->className = 'Configuration';
		$this->table = 'configuration';

		parent::__construct();

		$temporyArrayFields = $this->_getDefaultFieldsContent();
		$this->_buildOrderedFieldsShop($temporyArrayFields);
	}

	protected function _getDefaultFieldsContent()
	{
		$this->context = Context::getContext();
		$countryList = array();
		$countryList[] = array('id' => '0', 'name' => $this->l('Choose your country'));
		foreach (Country::getCountries($this->context->language->id) as $country)
			$countryList[] = array('id' => $country['id_country'], 'name' => $country['name']);
		$stateList = array();
		$stateList[] = array('id' => '0', 'name' => $this->l('Choose your state (if applicable)'));
		foreach (State::getStates($this->context->language->id) as $state)
			$stateList[] = array('id' => $state['id_state'], 'name' => $state['name']);

		$formFields = array(
			'PS_SHOP_NAME' => array('title' => $this->l('Shop name:'), 'desc' => $this->l('Displayed in e-mails and page titles'), 'validation' => 'isGenericName', 'required' => true, 'size' => 30, 'type' => 'text'),
			'PS_SHOP_EMAIL' => array('title' => $this->l('Shop e-mail:'), 'desc' => $this->l('Displayed in e-mails sent to customers'), 'validation' => 'isEmail', 'required' => true, 'size' => 30, 'type' => 'text'),
			'PS_SHOP_DETAILS' => array('title' => $this->l('Registration:'), 'desc' => $this->l('Shop registration information (e.g., SIRET or RCS)'), 'validation' => 'isGenericName', 'size' => 30, 'type' => 'textarea', 'cols' => 30, 'rows' => 5),
			'PS_SHOP_ADDR1' => array('title' => $this->l('Shop address line 1:'), 'validation' => 'isAddress', 'size' => 30, 'type' => 'text'),
			'PS_SHOP_ADDR2' => array('title' => 'Address line 2', 'validation' => 'isAddress', 'size' => 30, 'type' => 'text'),
			'PS_SHOP_CODE' => array('title' => $this->l('Post/Zip code:'), 'validation' => 'isGenericName', 'size' => 6, 'type' => 'text'),
			'PS_SHOP_CITY' => array('title' => $this->l('City:'), 'validation' => 'isGenericName', 'size' => 30, 'type' => 'text'),
			'PS_SHOP_COUNTRY_ID' => array('title' => $this->l('Country:'), 'validation' => 'isInt', 'size' => 30, 'type' => 'select', 'list' => $countryList, 'identifier' => 'id', 'cast' => 'intval'),
			'PS_SHOP_STATE_ID' => array('title' => $this->l('State:'), 'validation' => 'isInt', 'size' => 30, 'type' => 'select', 'list' => $stateList, 'identifier' => 'id', 'cast' => 'intval'),
			'PS_SHOP_PHONE' => array('title' => $this->l('Phone:'), 'validation' => 'isGenericName', 'size' => 30, 'type' => 'text'),
			'PS_SHOP_FAX' => array('title' => $this->l('Fax:'), 'validation' => 'isGenericName', 'size' => 30, 'type' => 'text'),
		);
		return $formFields;
	}

	protected function _buildOrderedFieldsShop($formFields)
	{
		$associatedOrderKey = array(
			'PS_SHOP_NAME' => 'company',
			'PS_SHOP_ADDR1' => 'address1',
			'PS_SHOP_ADDR2' => 'address2',
			'PS_SHOP_CITY' => 'city',
			'PS_SHOP_STATE_ID' => 'State:name',
			'PS_SHOP_CODE' => 'postcode',
			'PS_SHOP_COUNTRY_ID' => 'Country:name',
			'PS_SHOP_PHONE' => 'phone');

		$fields = array();
		$orderedFields = AddressFormat::getOrderedAddressFields(Configuration::get('PS_SHOP_COUNTRY_ID'), false, true);

		foreach ($orderedFields as $lineFields)
			if (($patterns = explode(' ', $lineFields)))
				foreach ($patterns as $pattern)
					if (($key = array_search($pattern, $associatedOrderKey)))
						$fields[$key] = $formFields[$key];
		foreach ($formFields as $key => $value)
			if (!isset($fields[$key]))
				$fields[$key] = $formFields[$key];

		$this->options = array(
			'general' => array(
				'title' =>	$this->l('Contact details'),
				'icon' =>	'tab-contact',
				'fields' =>	$fields,
				'submit' => array('title' => $this->l('   Save   '), 'class' => 'button')
			),
		);
	}

	public function beforeUpdateOptions()
	{
		if (isset($_POST['PS_SHOP_STATE_ID']) && $_POST['PS_SHOP_STATE_ID'] != '0')
		{
			$sql = 'SELECT `active` FROM `'._DB_PREFIX_.'state`
					WHERE `id_country` = '.(int)Tools::getValue('PS_SHOP_COUNTRY_ID').'
						AND `id_state` = '.(int)Tools::getValue('PS_SHOP_STATE_ID');
			$isStateOk = Db::getInstance()->getValue($sql);
			if ($isStateOk != 1)
				$this->errors[] = Tools::displayError('This state is not in this country.');
		}
	}

	public function updateOptionPsShopCountryId($value)
	{
		if (!$this->errors && $value)
		{
			$country = new Country($value, $this->context->language->id);
			if ($country->id)
			{
				Configuration::updateValue('PS_SHOP_COUNTRY_ID', $value);
				Configuration::updateValue('PS_SHOP_COUNTRY', pSQL($country->name));
			}
		}
	}

	public function updateOptionPsShopStateId($value)
	{
		if (!$this->errors && $value)
		{
			$state = new State($value);
			if ($state->id)
			{
				Configuration::updateValue('PS_SHOP_STATE_ID', $value);
				Configuration::updateValue('PS_SHOP_STATE', pSQL($state->name));
			}
		}
	}
}
