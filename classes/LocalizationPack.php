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
*  @license	http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once(_PS_TOOL_DIR_.'tar/Archive_Tar.php');

class LocalizationPackCore
{
	public	$name;
	public	$version;

	protected $iso_code_lang;
	protected $iso_currency;
	protected	$_errors = array();

	public function loadLocalisationPack($file, $selection, $install_mode = false)
	{
		if (!$xml = simplexml_load_string($file))
			return false;
		$mainAttributes = $xml->attributes();
		$this->name = strval($mainAttributes['name']);
		$this->version = strval($mainAttributes['version']);
		if (empty($selection))
		{
			$res = true;
			$res &= $this->_installStates($xml);
			$res &= $this->_installTaxes($xml);
			$res &= $this->_installCurrencies($xml, $install_mode);
			$res &= $this->_installUnits($xml);

			if (!defined('_PS_MODE_DEV_') OR !_PS_MODE_DEV_)
				$res &= $this->_installLanguages($xml, $install_mode);

			if ($res AND isset($this->iso_code_lang))
				Configuration::updateValue('PS_LANG_DEFAULT', (int)Language::getIdByIso($this->iso_code_lang));

			if ($install_mode AND $res AND isset($this->iso_currency))
			{
				$res &= Configuration::updateValue('PS_CURRENCY_DEFAULT', (int)Currency::getIdByIsoCode($this->iso_currency));
				Currency::refreshCurrencies();
			}

			return $res;
		}
		foreach ($selection AS $selected)
			if (!Validate::isLocalizationPackSelection($selected) OR !$this->{'_install'.ucfirst($selected)}($xml))
				return false;

		return true;
	}

	protected function _installStates($xml)
	{
		if (isset($xml->states->state))
			foreach ($xml->states->state AS $data)
			{
				$attributes = $data->attributes();

				if (!$id_state = State::getIdByName($attributes['name']))
				{
					$state = new State();
					$state->name = strval($attributes['name']);
					$state->iso_code = strval($attributes['iso_code']);
					$state->id_country = Country::getByIso(strval($attributes['country']));
					$state->id_zone = (int)(Zone::getIdByName(strval($attributes['zone'])));

					if (!$state->validateFields())
					{
						$this->_errors[] = Tools::displayError('Invalid state properties.');
						return false;
					}

					$country = new Country($state->id_country);
					if (!$country->contains_states)
					{
						$country->contains_states = 1;
						if (!$country->update())
							$this->_errors[] = Tools::displayError('Cannot update the associated country: ').$country->name;
					}

					if (!$state->add())
					{
						$this->_errors[] = Tools::displayError('An error occurred while adding the state.');
						return false;
					}
				} else {
					$state = new State($id_state);
					if (!Validate::isLoadedObject($state))
					{
						$this->_errors[] = Tools::displayError('An error occurred while fetching the state.');
						return false;
					}
				}

				// Add counties
				foreach ($data->county AS $xml_county)
				{
					$county_attributes = $xml_county->attributes();
					if (!$id_county = County::getIdCountyByNameAndIdState($county_attributes['name'], $state->id))
					{
						$county = new County();
						$county->name = $county_attributes['name'];
						$county->id_state = (int)$state->id;
						$county->active = 1;

						if (!$county->validateFields())
						{
							$this->_errors[] = Tools::displayError('Invalid County properties');
							return false;
						}

						if (!$county->save())
						{
							$this->_errors[] = Tools::displayError('An error has occured while adding the county');
							return false;
						}
					} else {
						$county = new County((int)$id_county);
						if (!Validate::isLoadedObject($county))
						{
							$this->_errors[] = Tools::displayError('An error occurred while fetching the county.');
							return false;
						}
					}

					// add zip codes
					foreach ($xml_county->zipcode AS $xml_zipcode)
					{
							$zipcode_attributes = $xml_zipcode->attributes();

							$zipcodes = $zipcode_attributes['from'];
							if (isset($zipcode_attributes['to']))
								$zipcodes .= '-'.$zipcode_attributes['to'];

							if ($county->isZipCodeRangePresent($zipcodes))
								continue;

							if (!$county->addZipCodes($zipcodes))
							{
								$this->_errors[] = Tools::displayError('An error has occured while adding zipcodes');
								return false;
							}
					}
				}
			}


		return true;
	}

	protected function _installTaxes($xml)
	{
		if (isset($xml->taxes->tax))
		{
			$available_behavior = array(PS_PRODUCT_TAX, PS_STATE_TAX, PS_BOTH_TAX);
			$assoc_taxes = array();
			foreach ($xml->taxes->tax AS $taxData)
			{
				$attributes = $taxData->attributes();
				if (Tax::getTaxIdByName($attributes['name']))
					continue;
				$tax = new Tax();
				$tax->name[(int)(Configuration::get('PS_LANG_DEFAULT'))] = strval($attributes['name']);
				$tax->rate = (float)($attributes['rate']);
				$tax->active = 1;

				if (!$tax->validateFields())
				{
					$this->_errors[] = Tools::displayError('Invalid tax properties.');
					return false;
				}

				if (!$tax->add())
				{
					$this->_errors[] = Tools::displayError('An error occurred while importing the tax: ').strval($attributes['name']);
					return false;
				}

				$assoc_taxes[(int)$attributes['id']] = $tax->id;
			}

			foreach ($xml->taxes->taxRulesGroup AS $group)
			{
				$group_attributes = $group->attributes();
				if (!Validate::isGenericName($group_attributes['name']))
					continue;

				 if (TaxRulesGroup::getIdByName($group['name']))
					continue;

				$trg = new TaxRulesGroup();
				$trg->name = $group['name'];
				$trg->active = 1;

				if (!$trg->save())
				{
					$this->_errors = Tools::displayError('This tax rule can\'t be saved.');
					return false;
				}

				foreach($group->taxRule as $rule)
				{
					$rule_attributes = $rule->attributes();

					// Validation
					if (!isset($rule_attributes['iso_code_country']))
						continue;

					$id_country = Country::getByIso(strtoupper($rule_attributes['iso_code_country']));
					if (!$id_country)
						continue;

					if (!isset($rule_attributes['id_tax']) || !array_key_exists(strval($rule_attributes['id_tax']), $assoc_taxes))
						continue;

					// Default values
					$id_state = (int) isset($rule_attributes['iso_code_state']) ? State::getIdByIso(strtoupper($rule_attributes['iso_code_state'])) : 0;
					$id_county = 0;
					$state_behavior = 0;
					$county_behavior = 0;

					if ($id_state)
					{
						if (isset($rule_attributes['state_behavior']) && in_array($rule_attributes['state_behavior'], $available_behavior))
							$state_behavior = (int)$rule_attributes['state_behavior'];

						if (isset($rule_attributes['county_name']))
						{
							$id_county = County::getIdCountyByNameAndIdState($rule_attributes['county_name'], (int)$id_state);
							if (!$id_county)
								continue;
						}

						if (isset($rule_attributes['county_behavior']) && in_array($rule_attributes['state_behavior'], $available_behavior))
							$county_behavior = (int)$rule_attributes['county_behavior'];
					}


					// Creation
					$tr = new TaxRule();
					$tr->id_tax_rules_group = $trg->id;
					$tr->id_country = $id_country;
					$tr->id_state = $id_state;
					$tr->id_county = $id_county;
					$tr->state_behavior = $state_behavior;
					$tr->county_behavior = $county_behavior;
					$tr->id_tax = $assoc_taxes[strval($rule_attributes['id_tax'])];
					$tr->save();
				}
			}
		}

		return true;
	}

	protected function _installCurrencies($xml, $install_mode = false)
	{
		if (isset($xml->currencies->currency))
		{
			if (!$feed = @simplexml_load_file('http://www.prestashop.com/xml/currencies.xml') AND !$feed = @simplexml_load_file(dirname(__FILE__).'/../localization/currencies.xml'))
			{
				$this->_errors[] = Tools::displayError('Cannot parse the currencies XML feed.');
				return false;
			}

			foreach ($xml->currencies->currency AS $data)
			{
				$attributes = $data->attributes();
				if(Currency::exists($attributes['iso_code']))
					continue;
				$currency = new Currency();
				$currency->name = strval($attributes['name']);
				$currency->iso_code = strval($attributes['iso_code']);
				$currency->iso_code_num = (int)($attributes['iso_code_num']);
				$currency->sign = strval($attributes['sign']);
				$currency->blank = (int)($attributes['blank']);
				$currency->conversion_rate = 1; // This value will be updated if the store is online
				$currency->format = (int)($attributes['format']);
				$currency->decimals = (int)($attributes['decimals']);
				$currency->active = $install_mode;
				if (!$currency->validateFields())
				{
					$this->_errors[] = Tools::displayError('Invalid currency properties.');
					return false;
				}
				if (!Currency::exists($currency->iso_code))
				{
					if (!$currency->add())
					{
						$this->_errors[] = Tools::displayError('An error occurred while importing the currency: ').strval($attributes['name']);
						return false;
					}
				}
			}

			Currency::refreshCurrencies();

			if (!sizeof($this->_errors) AND $install_mode AND isset($attributes['iso_code']) AND sizeof($xml->currencies->currency) == 1)
				$this->iso_currency = $attributes['iso_code'];
		}

		return true;
	}

	protected function _installLanguages($xml, $install_mode = false)
	{
		$attributes = array();
		if (isset($xml->languages->language))
			foreach ($xml->languages->language AS $data)
			{
				$attributes = $data->attributes();
				if (Language::getIdByIso($attributes['iso_code']))
					continue;
				$native_lang = Language::getLanguages();
				$native_iso_code = array();
				foreach ($native_lang AS $lang)
					$native_iso_code[] = $lang['iso_code'];
				if ((in_array((string)$attributes['iso_code'], $native_iso_code) AND !$install_mode) OR !in_array((string)$attributes['iso_code'], $native_iso_code))
					if(@fsockopen('www.prestashop.com', 80, $errno = 0, $errstr = '', 10))
					{
						if ($lang_pack = Tools::jsonDecode(Tools::file_get_contents('http://www.prestashop.com/download/lang_packs/get_language_pack.php?version='._PS_VERSION_.'&iso_lang='.$attributes['iso_code'])))
						{
							if ($content = file_get_contents('http://www.prestashop.com/download/lang_packs/gzip/'.$lang_pack->version.'/'.$attributes['iso_code'].'.gzip'))
							{
								$file = _PS_TRANSLATIONS_DIR_.$attributes['iso_code'].'.gzip';
								if (file_put_contents($file, $content))
								{
									$gz = new Archive_Tar($file, true);

									if (!$gz->extract(_PS_TRANSLATIONS_DIR_.'../', false))
									{
										$this->_errors[] = Tools::displayError('Cannot decompress the translation file of the language: ').(string)$attributes['iso_code'];
										return false;
									}

									if (!Language::checkAndAddLanguage((string)$attributes['iso_code']))
									{
										$this->_errors[] = Tools::displayError('An error occurred while creating the language: ').(string)$attributes['iso_code'];
										return false;
									}

									@unlink($file);
								}
								else
									$this->_errors[] = Tools::displayError('Server does not have permissions for writing.');
							}
						}
						else
							$this->_errors[] = Tools::displayError('Error occurred when language was checked according to your Prestashop version.');
					}
					else
						$this->_errors[] = Tools::displayError('Archive cannot be downloaded from prestashop.com.');
			}

		// change the default language if there is only one language in the localization pack
		if (!sizeof($this->_errors) AND $install_mode AND isset($attributes['iso_code']) AND sizeof($xml->languages->language) == 1)
			$this->iso_code_lang = $attributes['iso_code'];

		return true;
	}

	protected function _installUnits($xml)
	{
		$varNames = array('weight' => 'PS_WEIGHT_UNIT', 'volume' => 'PS_VOLUME_UNIT', 'short_distance' => 'PS_DIMENSION_UNIT', 'base_distance' => 'PS_BASE_DISTANCE_UNIT', 'long_distance' => 'PS_DISTANCE_UNIT');
		if (isset($xml->units->unit))
			foreach ($xml->units->unit AS $data)
			{
				$attributes = $data->attributes();
				if (!isset($varNames[strval($attributes['type'])]))
				{
					$this->_errors[] = Tools::displayError('Pack corrupted: wrong unit type.');
					return false;
				}
				if (!Configuration::updateValue($varNames[strval($attributes['type'])], strval($attributes['value'])))
				{
					$this->_errors[] = Tools::displayError('An error occurred while setting the units.');
					return false;
				}
			}
		return true;
	}

	public function getErrors()
	{
		return $this->_errors;
	}
}

