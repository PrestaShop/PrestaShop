<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminGeolocationControllerCore extends AdminController
{
	public function __construct()
	{
		parent::__construct();

		$this->bootstrap = true;
		$this->fields_options = array(
			'geolocationConfiguration' => array(
				'title' =>	$this->l('Geolocation by IP address'),
				'icon' =>	'icon-map-marker',
				'fields' =>	array(
		 			'PS_GEOLOCATION_ENABLED' => array(
		 				'title' => $this->l('Geolocation by IP address'),
		 				'hint' => $this->l('This option allows you, among other things, to restrict access to your shop for certain countries. See below.'),
		 				'validation' => 'isUnsignedId',
		 				'cast' => 'intval',
		 				'type' => 'bool'
					),
				),
				'submit' => array('title' => $this->l('Save'))
			),
			'geolocationCountries' => array(
				'title' =>	$this->l('Options'),
				'icon' =>	'icon-map-marker',
				'description' => $this->l('The following features are only available if you enable the Geolocation by IP address feature.'),
				'fields' =>	array(
		 			'PS_GEOLOCATION_BEHAVIOR' => array(
						'title' => $this->l('Geolocation behavior for restricted countries'),
						'type' => 'select',
						'identifier' => 'key',
						'list' => array(array('key' => _PS_GEOLOCATION_NO_CATALOG_, 'name' => $this->l('Visitors cannot see your catalog.')),
										array('key' => _PS_GEOLOCATION_NO_ORDER_, 'name' => $this->l('Visitors can see your catalog but cannot place an order.'))),
					),
		 			'PS_GEOLOCATION_NA_BEHAVIOR' => array(
						'title' => $this->l('Geolocation behavior for other countries'),
						'type' => 'select',
						'identifier' => 'key',
						'list' => array(array('key' => '-1', 'name' => $this->l('All features are available')),
										array('key' => _PS_GEOLOCATION_NO_CATALOG_, 'name' => $this->l('Visitors cannot see your catalog.')),
										array('key' => _PS_GEOLOCATION_NO_ORDER_, 'name' => $this->l('Visitors can see your catalog but cannot place an order.')))
					),
					'PS_ALLOWED_COUNTRIES' => array(
						'title' => $this->l('Select the countries from which your store is accessible'),
						'validation' => 'isArrayWithLangIsoCodes',
						'type' => 'checkbox_table',
						'identifier' => 'iso_code',
						'list' => Country::getCountries($this->context->language->id),
						'auto_value' => false
					),
				),
				'submit' => array('title' => $this->l('Save'))
			),
			'geolocationWhitelist' => array(
				'title' =>	$this->l('IP address whitelist'),
				'icon' =>	'icon-sitemap',
				'description' => $this->l('You can add IP addresses that will always be allowed to access your shop (e.g. Google bots\' IP).'),
				'fields' =>	array(
		 			'PS_GEOLOCATION_WHITELIST' => array('title' => $this->l('Whitelisted IP addresses'), 'type' => 'textarea_newlines', 'cols' => 15, 'rows' => 30),
				),
				'submit' => array('title' => $this->l('Save'))
			),
		);
	}

	/**
	 * @see AdminController::processUpdateOptions()
	 */
	public function processUpdateOptions()
	{
		// stop processing if geolocation is set to yes but geolite pack is not available
		if (!$this->isGeoLiteCityAvailable() && Tools::getValue('PS_GEOLOCATION_ENABLED'))
			$this->errors[] = Tools::displayError('The geolocation database is unavailable.');

		return parent::processUpdateOptions();
	}

	public function updateOptionPsGeolocationBehavior($value)
	{
		Configuration::updateValue(
			'PS_GEOLOCATION_BEHAVIOR',
			(!(int)$value ? _PS_GEOLOCATION_NO_CATALOG_ : _PS_GEOLOCATION_NO_ORDER_)
		);
	}

	public function updateOptionPsAllowedCountries($value)
	{
		$countryList = Tools::getValue('PS_ALLOWED_COUNTRIES');
		if (!empty($countryList)) {
			Configuration::updateValue('PS_ALLOWED_COUNTRIES', implode(';', $countryList));
		} else {
			$this->errors[] = Tools::displayError('Country selection is invalid.');
		}
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
				$this->l('and extract it (using Winrar or Gzip) into the /tools/geoip/ directory.'));

		parent::initContent();
	}

	protected function isGeoLiteCityAvailable()
	{
		if (file_exists(_PS_GEOIP_DIR_.'GeoLiteCity.dat'))
			return true;
		return false;
	}
}

