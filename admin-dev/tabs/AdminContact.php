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

include_once(PS_ADMIN_DIR.'/../classes/AdminTab.php');
include_once(PS_ADMIN_DIR.'/tabs/AdminPreferences.php');

class AdminContact extends AdminPreferences
{
	public function __construct()
	{
		$this->className = 'Configuration';
		$this->table = 'configuration';

		global $cookie;
		$countryList = array();
		$countryList[] = array('id' => '0', 'name' => $this->l('Choose your country'));
		foreach (Country::getCountries(intval($cookie->id_lang)) AS $country)
			$countryList[] = array('id' => $country['id_country'], 'name' => $country['name']);
		$stateList = array();
		$stateList[] = array('id' => '0', 'name' => $this->l('Choose your state (if applicable)'));
		foreach (State::getStates(intval($cookie->id_lang)) AS $state)
			$stateList[] = array('id' => $state['id_state'], 'name' => $state['name']);		

 		$this->_fieldsShop = array(
			'PS_SHOP_NAME' => array('title' => $this->l('Shop name:'), 'desc' => $this->l('Displayed in e-mails and page titles'), 'validation' => 'isGenericName', 'required' => true, 'size' => 30, 'type' => 'text'),
			'PS_SHOP_EMAIL' => array('title' => $this->l('Shop e-mail:'), 'desc' => $this->l('Displayed in e-mails sent to customers'), 'validation' => 'isEmail', 'required' => true, 'size' => 30, 'type' => 'text'),
			'PS_SHOP_DETAILS' => array('title' => $this->l('Registration:'), 'desc' => $this->l('Shop registration information (e.g., SIRET or RCS)'), 'validation' => 'isGenericName', 'size' => 30, 'type' => 'textarea', 'cols' => 30, 'rows' => 5),
			'PS_SHOP_ADDR1' => array('title' => $this->l('Shop address:'), 'validation' => 'isAddress', 'size' => 30, 'type' => 'text'),
			'PS_SHOP_ADDR2' => array('title' => '', 'validation' => 'isAddress', 'size' => 30, 'type' => 'text'),
			'PS_SHOP_CODE' => array('title' => $this->l('Post/Zip code:'), 'validation' => 'isGenericName', 'size' => 6, 'type' => 'text'),
			'PS_SHOP_CITY' => array('title' => $this->l('City:'), 'validation' => 'isGenericName', 'size' => 30, 'type' => 'text'),
			'PS_SHOP_COUNTRY_ID' => array('title' => $this->l('Country:'), 'validation' => 'isInt', 'size' => 30, 'type' => 'select', 'list' => $countryList, 'identifier' => 'id', 'cast' => 'intval'),
			'PS_SHOP_STATE_ID' => array('title' => $this->l('State:'), 'validation' => 'isInt', 'size' => 30, 'type' => 'select', 'list' => $stateList, 'identifier' => 'id', 'cast' => 'intval'),
			'PS_SHOP_PHONE' => array('title' => $this->l('Phone:'), 'validation' => 'isGenericName', 'size' => 30, 'type' => 'text'),
			'PS_SHOP_FAX' => array('title' => $this->l('Fax:'), 'validation' => 'isGenericName', 'size' => 30, 'type' => 'text'),
		);
		parent::__construct();
	}

	public function postProcess()
	{
		if (isset($_POST['PS_SHOP_STATE_ID']) && $_POST['PS_SHOP_STATE_ID'] != '0')
		{
			$isStateOk = Db::getInstance()->getValue('SELECT `active` FROM `'._DB_PREFIX_.'state` WHERE `id_country` = '.(int)(Tools::getValue('PS_SHOP_COUNTRY_ID')).' AND `id_state` = '.(int)(Tools::getValue('PS_SHOP_STATE_ID')));
			if ($isStateOk != 1)
				$this->_errors[] = Tools::displayError('This state is not in this country.');
		}
		parent::postProcess();
	}

	protected function _postConfig($fields)
	{
		global $cookie;
		if (!$this->_errors && isset($_POST['PS_SHOP_COUNTRY_ID']))
		{
			$country = new Country((int)($_POST['PS_SHOP_COUNTRY_ID']), intval($cookie->id_lang));
			Configuration::updateValue('PS_SHOP_COUNTRY', pSQL($country->name));
		}
		if (!$this->_errors && isset($_POST['PS_SHOP_STATE_ID']))
		{
			$state = new State((int)($_POST['PS_SHOP_STATE_ID']));
			Configuration::updateValue('PS_SHOP_STATE', pSQL($state->name));
		}
		parent::_postConfig($fields);
	}
	
	public function display()
	{
		$this->_displayForm('shop', $this->_fieldsShop, $this->l('Contact details'), 'width3', 'tab-contact');
	}
}


