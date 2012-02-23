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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminGeolocationControllerCore extends AdminController
{
	public function __construct()
	{
		parent::__construct();

		$this->options = array(
			'geolocationConfiguration' => array(
				'title' =>	$this->l('Geolocation by IP:'),
				'icon' =>	'world',
				'fields' =>	array(
		 			'PS_GEOLOCATION_ENABLED' => array(
		 				'title' => $this->l('Geolocation by IP:'),
		 				'desc' => $this->l('This option allows you, among other things, to restrict access to your shop for many countries. See below.'),
		 				'validation' => 'isUnsignedId',
		 				'cast' => 'intval',
		 				'type' => 'bool'
					),
				),
			),
			'geolocationCountries' => array(
				'title' =>	$this->l('Options'),
				'icon' =>	'world',
				'description' => $this->l('The following features are only available if you enable the Geolocation by IP feature.'),
				'fields' =>	array(
		 			'PS_GEOLOCATION_BEHAVIOR' => array(
						'title' => $this->l('Geolocation behavior for restricted countries:'),
						'type' => 'select',
						'identifier' => 'key',
						'list' => array(array('key' => _PS_GEOLOCATION_NO_CATALOG_, 'name' => $this->l('Visitors can\'t see your catalog')),
										array('key' => _PS_GEOLOCATION_NO_ORDER_, 'name' => $this->l('Visitors can see your catalog but can\'t make an order'))),
					),
		 			'PS_GEOLOCATION_NA_BEHAVIOR' => array(
						'title' => $this->l('Geolocation behavior for undefined countries:'),
						'type' => 'select',
						'identifier' => 'key',
						'list' => array(array('key' => '-1', 'name' => $this->l('All features are available')),
										array('key' => _PS_GEOLOCATION_NO_CATALOG_, 'name' => $this->l('Visitors can\'t see your catalog')),
										array('key' => _PS_GEOLOCATION_NO_ORDER_, 'name' => $this->l('Visitors can see your catalog but can\'t make an order')))
					),
		 			'countries' => array(
						'title' => $this->l('Select countries that can access your store:'),
						'type' => 'checkbox_table',
						'identifier' => 'iso_code',
						'list' => Country::getCountries($this->context->language->id)
					),
				),
			),
			'geolocationWhitelist' => array(
				'title' =>	$this->l('Whitelist of IP addresses'),
				'icon' =>	'world',
				'description' => $this->l('You can add many IP addresses, these addresses will always be allowed to access your shop (e.g. Google bots IP).'),
				'fields' =>	array(
		 			'PS_GEOLOCATION_WHITELIST' => array('title' => $this->l('Allowed IP addresses:'), 'type' => 'textarea_newlines', 'cols' => 80, 'rows' => 30),
				),
				'submit' => array(),
			),
		);
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitGeolocationWhitelist'))
		{
			$redirectAdmin = false;
			if ($this->isGeoLiteCityAvailable())
			{
				Configuration::updateValue('PS_GEOLOCATION_ENABLED', intval(Tools::getValue('PS_GEOLOCATION_ENABLED')));
				$redirectAdmin = true;
			}
			else
				$this->errors[] = Tools::displayError('Geolocation database is unavailable.');

			if (!is_array(Tools::getValue('countries')) || !count(Tools::getValue('countries')))
				$this->errors[] = Tools::displayError('Country selection is invalid');
			else
			{
				Configuration::updateValue(
					'PS_GEOLOCATION_BEHAVIOR',
					(!(int)Tools::getValue('PS_GEOLOCATION_BEHAVIOR') ? _PS_GEOLOCATION_NO_CATALOG_ : _PS_GEOLOCATION_NO_ORDER_)
				);
				Configuration::updateValue('PS_GEOLOCATION_NA_BEHAVIOR', (int)Tools::getValue('PS_GEOLOCATION_NA_BEHAVIOR'));
				Configuration::updateValue('PS_ALLOWED_COUNTRIES', implode(';', Tools::getValue('countries')));
				$redirectAdmin = true;
			}

			if (!Validate::isCleanHtml(Tools::getValue('PS_GEOLOCATION_WHITELIST')))
				$this->errors[] = Tools::displayError('Invalid whitelist');
			else
			{
				Configuration::updateValue(
					'PS_GEOLOCATION_WHITELIST',
					str_replace("\n", ';', str_replace("\r", '', Tools::getValue('PS_GEOLOCATION_WHITELIST')))
				);
				$redirectAdmin = true;
			}

			if ($redirectAdmin)
				Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token').'&conf=4');
		}

		return parent::postProcess();
	}

	public function renderOptions()
	{
		$this->tpl_option_vars = array('allowed_countries' => explode(';', Configuration::get('PS_ALLOWED_COUNTRIES')));

		return parent::renderOptions();
	}

	public function initContent()
	{
		$this->display = 'options';
		if (!$this->isGeoLiteCityAvailable())
			$this->displayWarning($this->l('In order to use Geolocation, please download').' 
				<a href="http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz">'.$this->l('this file').'</a> '.
				$this->l('and decompress it into tools/geoip/ directory'));

		parent::initContent();
	}

	protected function isGeoLiteCityAvailable()
	{
		if (file_exists(_PS_GEOIP_DIR_.'GeoLiteCity.dat'))
			return true;
		return false;
	}
}

