<?php
/**
 * 2007-2016 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Core\Cldr\Update;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

/**
 * Class LocalizationPackCore
 */
class LocalizationPackCore
{
    public $name;
    public $version;

    protected $iso_code_lang;
    protected $iso_currency;
    protected $_errors = array();

    /**
     * Load LocalizationPack
     *
     * @param      $file
     * @param      $selection
     * @param bool $install_mode
     * @param null $iso_localization_pack
     *
     * @return bool
     *
     * @deprecated 1.7.0
     */
    public function loadLocalisationPack($file, $selection, $installMode = false, $isoLocalizationPack = null)
    {
        return $this->loadLocalizationPack($file, $selection, $installMode, $isoLocalizationPack);
    }

    /**
     * Load LocalizationPack
     *
     * @param string $file
     * @param array  $selection
     * @param bool   $installMode
     * @param null   $isoLocalizationPack
     *
     * @return bool
     *
     * @since 1.7.0
     */
    public function loadLocalizationPack($file, $selection, $installMode = false, $isoLocalizationPack = null)
    {
        if (!$xml = @simplexml_load_string($file)) {
            return false;
        }
        libxml_clear_errors();
        $mainAttributes = $xml->attributes();
        $this->name = (string) $mainAttributes['name'];
        $this->version = (string) $mainAttributes['version'];
        if ($isoLocalizationPack) {
            $idCountry = (int) Country::getByIso($isoLocalizationPack);

            if ($idCountry) {
                $country = new Country($idCountry);
            }
            if (!$idCountry || !Validate::isLoadedObject($country)) {
                $this->_errors[] = Tools::displayError(sprintf('Cannot load country : %1d', $idCountry));

                return false;
            }
            if (!$country->active) {
                $country->active = 1;
                if (!$country->update()) {
                    $this->_errors[] = Tools::displayError(sprintf('Cannot enable the associated country: %1s', $country->name));
                }
            }
        }

        $res = true;

        if (empty($selection)) {
            $res &= $this->installStates($xml);
            $res &= $this->installTaxes($xml);
            $res &= $this->installCurrencies($xml, $installMode);
            $res &= $this->installConfiguration($xml);
            $res &= $this->installModules($xml);
            $res &= $this->updateDefaultGroupDisplayMethod($xml);

            if (($res || $installMode) && isset($this->iso_code_lang)) {
                if (!($idLang = (int) Language::getIdByIso($this->iso_code_lang, true))) {
                    $idLang = 1;
                }
                if (!$installMode) {
                    Configuration::updateValue('PS_LANG_DEFAULT', $idLang);
                }
            } elseif (!isset($this->iso_code_lang) && $installMode) {
                $idLang = 1;
            }

            if (!Language::isInstalled(Language::getIsoById($idLang))) {
                $res &= $this->installLanguages($xml, $installMode);
                $res &= $this->installUnits($xml);
            }

            if ($installMode && $res && isset($this->iso_currency)) {
                Cache::clean('Currency::getIdByIsoCode_*');
                $res &= Configuration::updateValue('PS_CURRENCY_DEFAULT', (int) Currency::getIdByIsoCode($this->iso_currency));
                Currency::refreshCurrencies();
            }
        } else {
            foreach ($selection as $selected) {
                // No need to specify the install_mode because if the selection mode is used, then it's not the install
                $res &= Validate::isLocalizationPackSelection($selected) ? $this->{'_install'.$selected}($xml) : false;
            }
        }

        //get/update cldr datas for each language
        if ($isoLocalizationPack) {
            foreach ($xml->languages->language as $lang) {
                //use this to get correct language code ex : qc become fr
                $languageCode = explode('-', Language::getLanguageCodeByIso($lang['iso_code']));
                $isoCode = $languageCode[0].'-'.strtoupper($isoLocalizationPack);

                $cldrUpdate = new Update(_PS_TRANSLATIONS_DIR_);
                $cldrUpdate->fetchLocale($isoCode);
            }
        }

        return $res;
    }

    /**
     * @param SimpleXMLElement $xml
     *
     * @return mixed
     *
     * @deprecated 1.7.0
     */
    protected function _installStates(\SimpleXMLElement $xml)
    {
        return $this->installStates($xml);
    }

    /**
     * @param SimpleXMLElement $xml
     * @return bool
     * @throws PrestaShopException
     *
     * @since 1.7.0
     */
    protected function installStates(\SimpleXMLElement $xml)
    {
        if (isset($xml->states->state)) {
            foreach ($xml->states->state as $data) {
                /** @var SimpleXMLElement $data */
                $attributes = $data->attributes();
                $idCountry = ($attributes['country']) ? (int)Country::getByIso(strval($attributes['country'])) : false;
                $idState = ($idCountry) ? State::getIdByIso($attributes['iso_code'], $idCountry) : State::getIdByName($attributes['name']);

                if (!$idState) {
                    $state = new State();
                    $state->name = strval($attributes['name']);
                    $state->iso_code = strval($attributes['iso_code']);
                    $state->id_country = $idCountry;

                    $idZone = (int)Zone::getIdByName(strval($attributes['zone']));
                    if (!$idZone) {
                        $zone = new Zone();
                        $zone->name = (string)$attributes['zone'];
                        $zone->active = true;

                        if (!$zone->add()) {
                            $this->_errors[] = Tools::displayError('Invalid Zone name.');
                            return false;
                        }

                        $idZone = $zone->id;
                    }

                    $state->id_zone = $idZone;

                    if (!$state->validateFields()) {
                        $this->_errors[] = Tools::displayError('Invalid state properties.');

                        return false;
                    }

                    $country = new Country($state->id_country);
                    if (!$country->contains_states) {
                        $country->contains_states = 1;
                        if (!$country->update()) {
                            $this->_errors[] = Tools::displayError('Cannot update the associated country: ').$country->name;
                        }
                    }

                    if (!$state->add()) {
                        $this->_errors[] = Tools::displayError('An error occurred while adding the state.');

                        return false;
                    }
                } else {
                    $state = new State($idState);
                    if (!Validate::isLoadedObject($state)) {
                        $this->_errors[] = Tools::displayError('An error occurred while fetching the state.');

                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param SimpleXMLElement $xml
     *
     * @return mixed
     *
     * @deprecated 1.7.0
     */
    protected function _installTaxes(\SimpleXMLElement $xml)
    {
        return $this->installTaxes($xml);
    }

    /**
     * @param SimpleXMLElement $xml
     * @return bool
     * @throws PrestaShopException
     *
     * @since 1.7.0
     */
    protected function installTaxes($xml)
    {
        if (isset($xml->taxes->tax)) {
            $assocTaxes = array();
            foreach ($xml->taxes->tax as $taxData) {
                /** @var SimpleXMLElement $taxData */
                $attributes = $taxData->attributes();
                if (($id_tax = Tax::getTaxIdByName($attributes['name']))) {
                    $assocTaxes[(int) $attributes['id']] = $id_tax;
                    continue;
                }
                $tax = new Tax();
                $tax->name[(int) Configuration::get('PS_LANG_DEFAULT')] = (string) $attributes['name'];
                $tax->rate = (float) $attributes['rate'];
                $tax->active = 1;

                if (($error = $tax->validateFields(false, true)) !== true || ($error = $tax->validateFieldsLang(false, true)) !== true) {
                    $this->_errors[] = Tools::displayError('Invalid tax properties.').' '.$error;

                    return false;
                }

                if (!$tax->add()) {
                    $this->_errors[] = Tools::displayError('An error occurred while importing the tax: ').(string) $attributes['name'];

                    return false;
                }

                $assocTaxes[(int) $attributes['id']] = $tax->id;
            }

            foreach ($xml->taxes->taxRulesGroup as $group) {
                /** @var SimpleXMLElement $group */
                $groupAttributes = $group->attributes();
                if (!Validate::isGenericName($groupAttributes['name'])) {
                    continue;
                }

                if (TaxRulesGroup::getIdByName($group['name'])) {
                    continue;
                }

                $trg = new TaxRulesGroup();
                $trg->name = $group['name'];
                $trg->active = 1;

                if (!$trg->save()) {
                    $this->_errors[] = Tools::displayError('This tax rule cannot be saved.');

                    return false;
                }

                foreach ($group->taxRule as $rule) {
                    /** @var SimpleXMLElement $rule */
                    $ruleAttributes = $rule->attributes();

                    // Validation
                    if (!isset($ruleAttributes['iso_code_country'])) {
                        continue;
                    }

                    $idCountry = (int)Country::getByIso(strtoupper($ruleAttributes['iso_code_country']));
                    if (!$idCountry) {
                        continue;
                    }

                    if (!isset($ruleAttributes['id_tax']) || !array_key_exists(strval($ruleAttributes['id_tax']), $assocTaxes)) {
                        continue;
                    }

                    // Default values
                    $idState = (int) isset($ruleAttributes['iso_code_state']) ? State::getIdByIso(strtoupper($ruleAttributes['iso_code_state'])) : 0;
                    $idCounty = 0;
                    $zipcodeFrom = 0;
                    $zipcodeTo = 0;
                    $behavior = $ruleAttributes['behavior'];

                    if (isset($ruleAttributes['zipcode_from'])) {
                        $zipcodeFrom = $ruleAttributes['zipcode_from'];
                        if (isset($ruleAttributes['zipcode_to'])) {
                            $zipcodeTo = $ruleAttributes['zipcode_to'];
                        }
                    }

                    // Creation
                    $tr = new TaxRule();
                    $tr->id_tax_rules_group = $trg->id;
                    $tr->id_country = $idCountry;
                    $tr->id_state = $idState;
                    $tr->id_county = $idCounty;
                    $tr->zipcode_from = $zipcodeFrom;
                    $tr->zipcode_to = $zipcodeTo;
                    $tr->behavior = $behavior;
                    $tr->description = '';
                    $tr->id_tax = $assocTaxes[strval($ruleAttributes['id_tax'])];
                    $tr->save();
                }
            }
        }

        return true;
    }

    /**
     * @param SimpleXMLElement $xml
     * @param bool             $installMode
     *
     * @return bool
     *
     * @deprecated 1.7.0
     */
    protected function _installCurrencies(\SimpleXMLElement $xml, $installMode = false)
    {
        return $this->installCurrencies($xml, $installMode);
    }

    /**
     * @param SimpleXMLElement $xml
     * @param bool             $installMode
     *
     * @return bool
     * @throws PrestaShopException
     *
     * @since 1.7.0
     */
    protected function installCurrencies($xml, $installMode = false)
    {
        if (isset($xml->currencies->currency)) {
            foreach ($xml->currencies->currency as $data) {
                /** @var SimpleXMLElement $data */
                $attributes = $data->attributes();
                if (Currency::exists($attributes['iso_code'])) {
                    continue;
                }
                $currency = new Currency();
                $currency->name = (string) $attributes['name'];
                $currency->iso_code = (string) $attributes['iso_code'];
                $currency->iso_code_num = (int) $attributes['iso_code_num'];
                $currency->sign = (string) $attributes['sign'];
                $currency->blank = (int) $attributes['blank'];
                $currency->conversion_rate = 1; // This value will be updated if the store is online
                $currency->format = (int) $attributes['format'];
                $currency->decimals = (int) $attributes['decimals'];
                $currency->active = true;
                if (!$currency->validateFields()) {
                    $this->_errors[] = Tools::displayError('Invalid currency properties.');

                    return false;
                }
                if (!Currency::exists($currency->iso_code)) {
                    if (!$currency->add()) {
                        $this->_errors[] = Tools::displayError('An error occurred while importing the currency: ').strval($attributes['name']);

                        return false;
                    }

                    PaymentModule::addCurrencyPermissions($currency->id);
                }
            }

            $error = Currency::refreshCurrencies();
            if (!empty($error)) {
                $this->_errors[] = $error;
            }

            if (!count($this->_errors) && $installMode && isset($attributes['iso_code']) && count($xml->currencies->currency) == 1) {
                $this->iso_currency = $attributes['iso_code'];
            }
        }

        return true;
    }

    /**
     * @param SimpleXMLElement $xml
     * @param bool             $installMode
     *
     * @return bool
     *
     * @deprecated 1.7.0
     */
    protected function _installLanguages(\SimpleXMLElement $xml, $installMode = false)
    {
        return $this->installLanguages($xml, $installMode);
    }

    /**
     * @param SimpleXMLElement $xml
     * @param bool             $installMode
     *
     * @return bool
     *
     * @since 1.7.0
     */
    protected function installLanguages(\SimpleXMLElement $xml, $installMode = false)
    {
        $attributes = array();
        if (isset($xml->languages->language)) {
            foreach ($xml->languages->language as $data) {
                /** @var SimpleXMLElement $data */
                $attributes = $data->attributes();
                // if we are not in an installation context or if the pack is not available in the local directory
                if (Language::getIdByIso($attributes['iso_code']) && !$installMode) {
                    continue;
                }

                $errors = Language::downloadAndInstallLanguagePack($attributes['iso_code'], $attributes['version'], $attributes);
                if ($errors !== true && is_array($errors)) {
                    $this->_errors = array_merge($this->_errors, $errors);
                }
            }
        }

        // change the default language if there is only one language in the localization pack
        if (!count($this->_errors) && $installMode && isset($attributes['iso_code']) && count($xml->languages->language) == 1) {
            $this->iso_code_lang = $attributes['iso_code'];
        }

        return !count($this->_errors);
    }

    /**
     * @param SimpleXMLElement $xml
     *
     * @return mixed
     *
     * @deprecated 1.7.0
     */
    protected function _installUnits(\SimpleXMLElement $xml)
    {
        return $this->installUnits($xml);
    }

    /**
     * @param SimpleXMLElement $xml
     *
     * @return bool
     *
     * @since 1.7.0
     */
    protected function installUnits($xml)
    {
        $varNames = array('weight' => 'PS_WEIGHT_UNIT', 'volume' => 'PS_VOLUME_UNIT', 'short_distance' => 'PS_DIMENSION_UNIT', 'base_distance' => 'PS_BASE_DISTANCE_UNIT', 'long_distance' => 'PS_DISTANCE_UNIT');
        if (isset($xml->units->unit)) {
            foreach ($xml->units->unit as $data) {
                /** @var SimpleXMLElement $data */
                $attributes = $data->attributes();
                if (!isset($varNames[strval($attributes['type'])])) {
                    $this->_errors[] = Tools::displayError('Localization pack corrupted: wrong unit type.');

                    return false;
                }
                if (!Configuration::updateValue($varNames[strval($attributes['type'])], strval($attributes['value']))) {
                    $this->_errors[] = Tools::displayError('An error occurred while setting the units.');

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Install/Uninstall a module from a localization file
     * ```xml
     * <modules>
     *   <module name="module_name" [install="0|1"] />
     * </modules>
     * ```
     *
     * @param SimpleXMLElement $xml
     * @return bool
     */
    protected function installModules($xml)
    {
        if (isset($xml->modules)) {
            foreach ($xml->modules->module as $data) {
                /** @var SimpleXMLElement $data */
                $attributes = $data->attributes();
                $name = (string) $attributes['name'];
                if (isset($name) && $module = Module::getInstanceByName($name)) {
                    $install = ($attributes['install'] == 1) ? true : false;
                    $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
                    $moduleManager = $moduleManagerBuilder->build();


                    if ($install) {
                        if (!$moduleManager->isInstalled($name)) {
                            if (!$module->install()) {
                                $this->_errors[] = Tools::displayError('An error occurred while installing the module:').$name;
                            }
                        }
                    } elseif ($moduleManager->isInstalled($name)) {
                        if (!$module->uninstall()) {
                            $this->_errors[] = Tools::displayError('An error occurred while uninstalling the module:').$name;
                        }
                    }

                    unset($module);
                } else {
                    $this->_errors[] = Tools::displayError('An error has occurred, this module does not exist:').$name;
                }
            }
        }

        return true;
    }

    /**
     * Update a configuration variable from a localization file
     *
     * ```xml
     * <configuration>
     *   <configuration name="variable_name" value="variable_value" />
     * </configuration>
     * ```
     *
     * @param SimpleXMLElement $xml
     *
     * @return bool
     */
    protected function installConfiguration($xml)
    {
        if (isset($xml->configurations)) {
            foreach ($xml->configurations->configuration as $data) {
                /** @var SimpleXMLElement $data */
                $attributes = $data->attributes();
                $name = (string) $attributes['name'];

                if (isset($name) && isset($attributes['value']) && Configuration::get($name) !== false) {
                    if (!Configuration::updateValue($name, (string) $attributes['value'])) {
                        $this->_errors[] = Tools::displayError('An error occurred during the configuration setup: '.$name);
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param SimpleXMLElement $xml
     *
     * @return bool
     *
     * @deprecated 1.7.0
     */
    protected function _installGroups(\SimpleXMLElement $xml)
    {
        return $this->installGroups($xml);
    }

    /**
     * @param SimpleXMLElement $xml
     *
     * @return bool
     *
     * @since 1.7.0
     */
    protected function installGroups($xml)
    {
        return $this->updateDefaultGroupDisplayMethod($xml);
    }

    /**
     * @param SimpleXMLElement $xml
     *
     * @return bool
     */
    protected function updateDefaultGroupDisplayMethod($xml)
    {
        if (isset($xml->group_default)) {
            $attributes = $xml->group_default->attributes();
            if (isset($attributes['price_display_method']) && in_array((int) $attributes['price_display_method'], array(0, 1))) {
                Configuration::updateValue('PRICE_DISPLAY_METHOD', (int) $attributes['price_display_method']);

                foreach (array((int)Configuration::get('PS_CUSTOMER_GROUP'), (int) Configuration::get('PS_GUEST_GROUP'), (int) Configuration::get('PS_UNIDENTIFIED_GROUP')) as $idGroup) {
                    $group = new Group((int) $idGroup);
                    $group->price_display_method = (int) $attributes['price_display_method'];
                    if (!$group->save()) {
                        $this->_errors[] = Tools::displayError('An error occurred during the default group update');
                    }
                }
            } else {
                $this->_errors[] = Tools::displayError('An error has occurred during the default group update');
            }
        }

        return true;
    }

    /**
     * Get errors
     *
     * @return array Error messages
     */
    public function getErrors()
    {
        return $this->_errors;
    }
}
