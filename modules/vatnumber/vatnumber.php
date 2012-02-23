<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class VatNumber extends TaxManagerModule
{
	public function __construct()
	{
		$this->name = 'vatnumber';
		$this->tab = 'billing_invoicing';
		$this->version = 1.2;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		$this->tax_manager_class = 'VATNumberTaxManager';

		parent::__construct();
		$id_country = (int)Configuration::get('VATNUMBER_COUNTRY');

		if ($id_country == 0)
			$this->warning = $this->l('No default country set.');

		$this->displayName = $this->l('European VAT number');
		$this->description = $this->l('Enable entering of the VAT intra-community number when creating the address (You must fill in the company field to allow keyboarding VAT number)');
	}

	public function	install()
	{
		return (parent::install() AND Configuration::updateValue('VATNUMBER_MANAGEMENT', 1));
	}

	public function uninstall()
	{
		return (parent::uninstall() AND Configuration::updateValue('VATNUMBER_MANAGEMENT', 0));
	}

	public function enable($forceAll = false)
	{
		parent::enable();
		Configuration::updateValue('VATNUMBER_MANAGEMENT', 1);
	}

	public function disable($forceAll = false)
	{
		parent::disable();
		Configuration::updateValue('VATNUMBER_MANAGEMENT', 0);
	}

	public static function getPrefixIntracomVAT()
	{
		$intracom_array = array(
			'AT'=>'AT',	//Austria
			'BE'=>'BE',	//Belgium
			'DK'=>'DK',	//Denmark
			'FI'=>'FI',	//Finland
			'FR'=>'FR',	//France
			'FX'=>'FR',	//France m�tropolitaine
			'DE'=>'DE',	//Germany
			'GR'=>'EL',	//Greece
			'IE'=>'IE',	//Irland
			'IT'=>'IT',	//Italy
			'LU'=>'LU',	//Luxembourg
			'NL'=>'NL',	//Netherlands
			'PT'=>'PT',	//Portugal
			'ES'=>'ES',	//Spain
			'SE'=>'SE',	//Sweden
			'GB'=>'GB',	//United Kingdom
			'CY'=>'CY',	//Cyprus
			'EE'=>'EE',	//Estonia
			'HU'=>'HU',	//Hungary
			'LV'=>'LV',	//Latvia
			'LT'=>'LT',	//Lithuania
			'MT'=>'MT',	//Malta
			'PL'=>'PL',	//Poland
			'SK'=>'SK',	//Slovakia
			'CZ'=>'CZ',	//Czech Republic
			'SI'=>'SI',	//Slovenia
			'RO'=>'RO', //Romania
			'BG'=>'BG'	//Bulgaria
		);
		return $intracom_array;
	}

	public static function isApplicable($id_country)
	{
		return (((int)$id_country AND in_array(Country::getIsoById($id_country), self::getPrefixIntracomVAT())) ? 1 : 0);
	}

	public static function WebServiceCheck($vatNumber)
	{
		if (empty($vatNumber))
			return array();
		$vatNumber = str_replace(' ', '', $vatNumber);
		$prefix = substr($vatNumber, 0, 2);
		if (array_search($prefix, self::getPrefixIntracomVAT()) === false)
			return array(Tools::displayError('Invalid VAT number'));
		$vat = substr($vatNumber, 2);
		$url = 'http://ec.europa.eu/taxation_customs/vies/viesquer.do?ms='.urlencode($prefix).'&iso='.urlencode($prefix).'&vat='.urlencode($vat);
		@ini_set('default_socket_timeout', 2);
		for ($i = 0; $i < 3; $i++)
		{
			if ($pageRes = file_get_contents($url))
			{
				if (preg_match('/invalid VAT number/i', $pageRes))
				{
					@ini_restore('default_socket_timeout');
					return array(Tools::displayError('VAT number not found'));
				}
				else if (preg_match('/valid VAT number/i', $pageRes))
				{
					@ini_restore('default_socket_timeout');
					return array();
				}
				else
					++$i;
			}
			else
				sleep(1);
		}
		@ini_restore('default_socket_timeout');
		return array(Tools::displayError('VAT number validation service unavailable'));
	}

	public function getContent()
	{
		$echo = '';

		if (Tools::isSubmit('submitVatNumber'))
		{
			if (Configuration::updateValue('VATNUMBER_COUNTRY', (int)(Tools::getValue('vatnumber_country'))))
				$echo .= $this->displayConfirmation($this->l('Your country has been updated.'));
			$check = (int)Tools::getValue('vatnumber_checking');
			if (Configuration::get('VATNUMBER_CHECKING') != $check AND Configuration::updateValue('VATNUMBER_CHECKING', $check))
				$echo .= ($check ? $this->displayConfirmation($this->l('The check of the VAT number with the WebService is now enabled.')) : $this->displayConfirmation($this->l('The check of the VAT number with the WebService is now disabled.')));
		}
		$echo .=  '
		<fieldset><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->displayName.'</legend>
			<form action="'.htmlentities($_SERVER['REQUEST_URI']).'" method="post">
				<label>'.$this->l('Your country').'</label>
				<div class="margin-form">
					<select name="vatnumber_country">
						<option value="0">'.$this->l('-- Choose a country --').'</option>';
		foreach (Country::getCountries($this->context->language->id) as $country)
			$echo .=  '		<option value="'.$country['id_country'].'" '.(Tools::getValue('VATNUMBER_COUNTRY', Configuration::get('VATNUMBER_COUNTRY')) == $country['id_country'] ? 'selected="selected"' : '').'>'.$country['name'].'</option>';
		$echo .=  '		</select>
				</div>
				<div class="clear">&nbsp;</div>
				<label>'.$this->l('Enable checking of the VAT number with the WebService').'</label>
				<div class="margin-form">
					<input type="checkbox" name="vatnumber_checking" '.(Configuration::get('VATNUMBER_CHECKING') ? 'checked="checked"' : '').' value="1"/>
					<p>'.$this->l('The verification by the webservice is slow. Enabling this option can slow down your shop.').'</p>
				</div>
				<div class="clear">&nbsp;</div>
				<div class="margin-form">
					<input type="submit" class="button" name="submitVatNumber" value="'.$this->l('   Save   ').'" />
				</div>
			</form>
		</fieldset>';
		return $echo;
	}
}
