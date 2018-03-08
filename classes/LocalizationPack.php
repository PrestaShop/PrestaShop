<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Core\Cldr\Update;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class LocalizationPackCore
{
    public $name;
    public $version;

    protected $iso_code_lang;
    protected $iso_currency;
    protected $_errors = array();

    public function loadLocalisationPack($file, $selection, $install_mode = false, $iso_localization_pack = null)
    {
        if (!$xml = @simplexml_load_string($file)) {
            return false;
        }
        libxml_clear_errors();
        $main_attributes = $xml->attributes();
        $this->name = (string)$main_attributes['name'];
        $this->version = (string)$main_attributes['version'];
        if ($iso_localization_pack) {
            $id_country = (int)Country::getByIso($iso_localization_pack);

            if ($id_country) {
                $country = new Country($id_country);
            }
            if (!$id_country || !Validate::isLoadedObject($country)) {
                $this->_errors[] = Context::getContext()->getTranslator()->trans('Cannot load country: %d',
                    array($id_country),
                    'Admin.International.Notification'
                );
                return false;
            }
            if (!$country->active) {
                $country->active = 1;
                if (!$country->update()) {
                    $this->_errors[] = Context::getContext()->getTranslator()->trans(
                        'Cannot enable the associated country: %s',
                        array($country->name),
                        'Admin.International.Notification'
                    );
                }
            }
        }

        $res = true;

        if (empty($selection)) {
            $res &= $this->_installStates($xml);
            $res &= $this->_installTaxes($xml);
            $res &= $this->_installCurrencies($xml, $install_mode);
            $res &= $this->installConfiguration($xml);
            $res &= $this->installModules($xml);
            $res &= $this->updateDefaultGroupDisplayMethod($xml);

            if (($res || $install_mode) && isset($this->iso_code_lang)) {
                if (!($id_lang = (int)Language::getIdByIso($this->iso_code_lang, true))) {
                    $id_lang = 1;
                }
                if (!$install_mode) {
                    Configuration::updateValue('PS_LANG_DEFAULT', $id_lang);
                }
            } elseif (!isset($this->iso_code_lang) && $install_mode) {
                $id_lang = 1;
            }

            if (!Language::isInstalled(Language::getIsoById($id_lang))) {
                $res &= $this->_installLanguages($xml, $install_mode);
                $res &= $this->_installUnits($xml);
            }

            if ($install_mode && $res && isset($this->iso_currency)) {
                Cache::clean('Currency::getIdByIsoCode_*');
                $res &= Configuration::updateValue('PS_CURRENCY_DEFAULT', (int)Currency::getIdByIsoCode($this->iso_currency));
                Currency::refreshCurrencies();
            }
        } else {
            foreach ($selection as $selected) {
                // No need to specify the install_mode because if the selection mode is used, then it's not the install
                $res &= Validate::isLocalizationPackSelection($selected) ? $this->{'_install'.$selected}($xml) : false;
            }
        }

        //get/update cldr datas for each language
        if ($iso_localization_pack) {
            foreach ($xml->languages->language as $lang) {
                //use this to get correct language code ex : qc become fr
                $languageCode = explode('-', Language::getLanguageCodeByIso($lang['iso_code']));
                $isoCode = $languageCode[0].'-'.strtoupper($iso_localization_pack);

                $cldrUpdate = new Update(_PS_TRANSLATIONS_DIR_);
                $cldrUpdate->fetchLocale($isoCode);
            }
        }

        return $res;
    }

    /**
     * @param SimpleXMLElement $xml
     * @return bool
     * @throws PrestaShopException
     */
    protected function _installStates($xml)
    {
        if (isset($xml->states->state)) {
            foreach ($xml->states->state as $data) {
                /** @var SimpleXMLElement $data */
                $attributes = $data->attributes();
                $id_country = ($attributes['country']) ? (int)Country::getByIso(strval($attributes['country'])) : false;
                $id_state = ($id_country) ? State::getIdByIso($attributes['iso_code'], $id_country) : State::getIdByName($attributes['name']);

                if (!$id_state) {
                    $state = new State();
                    $state->name = strval($attributes['name']);
                    $state->iso_code = strval($attributes['iso_code']);
                    $state->id_country = $id_country;

                    $id_zone = (int)Zone::getIdByName(strval($attributes['zone']));
                    if (!$id_zone) {
                        $zone = new Zone();
                        $zone->name = (string)$attributes['zone'];
                        $zone->active = true;

                        if (!$zone->add()) {
                            $this->_errors[] = Context::getContext()->getTranslator()->trans('Invalid Zone name.', array(), 'Admin.International.Notification');
                            return false;
                        }

                        $id_zone = $zone->id;
                    }

                    $state->id_zone = $id_zone;

                    if (!$state->validateFields()) {
                        $this->_errors[] = Context::getContext()->getTranslator()->trans('Invalid state properties.', array(), 'Admin.International.Notification');
                        return false;
                    }

                    $country = new Country($state->id_country);
                    if (!$country->contains_states) {
                        $country->contains_states = 1;
                        if (!$country->update()) {
                            $this->_errors[] = Context::getContext()->getTranslator()->trans('Cannot update the associated country: %s', array($country->name), 'Admin.International.Notification');
                        }
                    }

                    if (!$state->add()) {
                        $this->_errors[] = Context::getContext()->getTranslator()->trans('An error occurred while adding the state.', array(), 'Admin.International.Notification');
                        return false;
                    }
                } else {
                    $state = new State($id_state);
                    if (!Validate::isLoadedObject($state)) {
                        $this->_errors[] = Context::getContext()->getTranslator()->trans('An error occurred while fetching the state.', array(), 'Admin.International.Notification');
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param SimpleXMLElement $xml
     * @return bool
     * @throws PrestaShopException
     */
    protected function _installTaxes($xml)
    {
        if (isset($xml->taxes->tax)) {
            $assoc_taxes = array();
            foreach ($xml->taxes->tax as $taxData) {
                /** @var SimpleXMLElement $taxData */
                $attributes = $taxData->attributes();
                if (($id_tax = Tax::getTaxIdByName($attributes['name']))) {
                    $assoc_taxes[(int)$attributes['id']] = $id_tax;
                    continue;
                }
                $tax = new Tax();
                $tax->name[(int)Configuration::get('PS_LANG_DEFAULT')] = (string)$attributes['name'];
                $tax->rate = (float)$attributes['rate'];
                $tax->active = 1;

                if (($error = $tax->validateFields(false, true)) !== true || ($error = $tax->validateFieldsLang(false, true)) !== true) {
                    $this->_errors[] = Context::getContext()->getTranslator()->trans('Invalid tax properties.', array(), 'Admin.International.Notification').' '.$error;
                    return false;
                }

                if (!$tax->add()) {
                    $this->_errors[] = Context::getContext()->getTranslator()->trans('An error occurred while importing the tax: %s', array((string)$attributes['name']), 'Admin.International.Notification');
                    return false;
                }

                $assoc_taxes[(int)$attributes['id']] = $tax->id;
            }

            foreach ($xml->taxes->taxRulesGroup as $group) {
                /** @var SimpleXMLElement $group */
                $group_attributes = $group->attributes();
                if (!Validate::isGenericName($group_attributes['name'])) {
                    continue;
                }

                if (TaxRulesGroup::getIdByName($group['name'])) {
                    continue;
                }

                $trg = new TaxRulesGroup();
                $trg->name = $group['name'];
                $trg->active = 1;

                if (!$trg->save()) {
                    $this->_errors[] = Context::getContext()->getTranslator()->trans('This tax rule cannot be saved.', array(), 'Admin.International.Notification');
                    return false;
                }

                foreach ($group->taxRule as $rule) {
                    /** @var SimpleXMLElement $rule */
                    $rule_attributes = $rule->attributes();

                    // Validation
                    if (!isset($rule_attributes['iso_code_country'])) {
                        continue;
                    }

                    $id_country = (int)Country::getByIso(strtoupper($rule_attributes['iso_code_country']));
                    if (!$id_country) {
                        continue;
                    }

                    if (!isset($rule_attributes['id_tax']) || !array_key_exists(strval($rule_attributes['id_tax']), $assoc_taxes)) {
                        continue;
                    }

                    // Default values
                    $id_state = (int)isset($rule_attributes['iso_code_state']) ? State::getIdByIso(strtoupper($rule_attributes['iso_code_state'])) : 0;
                    $id_county = 0;
                    $zipcode_from = 0;
                    $zipcode_to = 0;
                    $behavior = $rule_attributes['behavior'];

                    if (isset($rule_attributes['zipcode_from'])) {
                        $zipcode_from = $rule_attributes['zipcode_from'];
                        if (isset($rule_attributes['zipcode_to'])) {
                            $zipcode_to = $rule_attributes['zipcode_to'];
                        }
                    }

                    // Creation
                    $tr = new TaxRule();
                    $tr->id_tax_rules_group = $trg->id;
                    $tr->id_country = $id_country;
                    $tr->id_state = $id_state;
                    $tr->id_county = $id_county;
                    $tr->zipcode_from = $zipcode_from;
                    $tr->zipcode_to = $zipcode_to;
                    $tr->behavior = $behavior;
                    $tr->description = '';
                    $tr->id_tax = $assoc_taxes[strval($rule_attributes['id_tax'])];
                    $tr->save();
                }
            }
        }
        return true;
    }

    /**
     * @param SimpleXMLElement $xml
     * @param bool $install_mode
     * @return bool
     * @throws PrestaShopException
     */
    protected function _installCurrencies($xml, $install_mode = false)
    {
        if (isset($xml->currencies->currency)) {
            foreach ($xml->currencies->currency as $data) {
                /** @var SimpleXMLElement $data */
                $attributes = $data->attributes();
                if (Currency::exists($attributes['iso_code'])) {
                    continue;
                }

                $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');

                $currency                   = new Currency(null, $defaultLang);
                $currency->name             = (string)$attributes['name'];
                $currency->iso_code         = (string)$attributes['iso_code'];
                $currency->iso_code_num     = (int)$attributes['iso_code_num'];
                $currency->numeric_iso_code = (string)$attributes['iso_code_num'];
                $currency->sign             = (string)$attributes['sign'];
                $currency->symbol           = (string)$attributes['sign'];
                $currency->blank            = (int)$attributes['blank'];
                $currency->conversion_rate  = 1; // This value will be updated if the store is online
                $currency->format           = (int)$attributes['format'];
                $currency->decimals         = (int)$attributes['decimals'];
                $currency->precision        = (int)$attributes['decimals'];
                $currency->active           = true;
                if (!$currency->validateFields()) {
                    $this->_errors[] = Context::getContext()->getTranslator()->trans('Invalid currency properties.', array(), 'Admin.International.Notification');
                    return false;
                }
                if (!Currency::exists($currency->iso_code)) {
                    if (!$currency->add()) {
                        $this->_errors[] = Context::getContext()->getTranslator()->trans('An error occurred while importing the currency: %s', array(strval($attributes['name'])), 'Admin.International.Notification');
                        return false;
                    }

                    PaymentModule::addCurrencyPermissions($currency->id);
                }
            }

            $error = Currency::refreshCurrencies();
            if (!empty($error)) {
                $this->_errors[] = $error;
            }

            if (!count($this->_errors) && $install_mode && isset($attributes['iso_code']) && count($xml->currencies->currency) == 1) {
                $this->iso_currency = $attributes['iso_code'];
            }
        }

        return true;
    }



    /**
     * @param SimpleXMLElement $xml
     * @param bool $install_mode
     * @return bool
     */
    protected function _installLanguages($xml, $install_mode = false)
    {
        $attributes = array();
        if (isset($xml->languages->language)) {
            foreach ($xml->languages->language as $data) {
                /** @var SimpleXMLElement $data */
                $attributes = $data->attributes();
                // if we are not in an installation context or if the pack is not available in the local directory
                if (Language::getIdByIso($attributes['iso_code']) && !$install_mode) {
                    continue;
                }

                $errors = Language::downloadAndInstallLanguagePack($attributes['iso_code'], $attributes['version'], $attributes);
                if ($errors !== true && is_array($errors)) {
                    $this->_errors = array_merge($this->_errors, $errors);
                }
            }
        }

        // change the default language if there is only one language in the localization pack
        if (!count($this->_errors) && $install_mode && isset($attributes['iso_code']) && count($xml->languages->language) == 1) {
            $this->iso_code_lang = $attributes['iso_code'];
        }

        return !count($this->_errors);
    }

    /**
     * @param SimpleXMLElement $xml
     * @return bool
     */
    protected function _installUnits($xml)
    {
        $varNames = array('weight' => 'PS_WEIGHT_UNIT', 'volume' => 'PS_VOLUME_UNIT', 'short_distance' => 'PS_DIMENSION_UNIT', 'base_distance' => 'PS_BASE_DISTANCE_UNIT', 'long_distance' => 'PS_DISTANCE_UNIT');
        if (isset($xml->units->unit)) {
            foreach ($xml->units->unit as $data) {
                /** @var SimpleXMLElement $data */
                $attributes = $data->attributes();
                if (!isset($varNames[strval($attributes['type'])])) {
                    $this->_errors[] = Context::getContext()->getTranslator()->trans('Localization pack corrupted: wrong unit type.', array(), 'Admin.International.Notification');
                    return false;
                }
                if (!Configuration::updateValue($varNames[strval($attributes['type'])], strval($attributes['value']))) {
                    $this->_errors[] = Context::getContext()->getTranslator()->trans('An error occurred while setting the units.', array(), 'Admin.International.Notification');
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Install/Uninstall a module from a localization file
     * <modules>
     *     <module name="module_name" [install="0|1"] />
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
                $name = (string)$attributes['name'];
                if (isset($name) && $module = Module::getInstanceByName($name)) {
                    $install = ($attributes['install'] == 1) ? true : false;
                    $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
                    $moduleManager = $moduleManagerBuilder->build();


                    if ($install) {
                        if (!$moduleManager->isInstalled($name)) {
                            if (!$module->install()) {
                                $this->_errors[] = Context::getContext()->getTranslator()->trans('An error occurred while installing the module: %s', array($name), 'Admin.International.Notification');
                            }
                        }
                    } elseif ($moduleManager->isInstalled($name)) {
                        if (!$module->uninstall()) {
                            $this->_errors[] = Context::getContext()->getTranslator()->trans('An error occurred while uninstalling the module: %s', array($name), 'Admin.International.Notification');
                        }
                    }

                    unset($module);
                } else {
                    $this->_errors[] = Context::getContext()->getTranslator()->trans('An error has occurred, this module does not exist: %s', array($name), 'Admin.International.Notification');
                }
            }
        }

        return true;
    }

    /**
     * Update a configuration variable from a localization file
     * <configuration>
     * <configuration name="variable_name" value="variable_value" />
     *
     * @param SimpleXMLElement $xml
     * @return bool
     */
    protected function installConfiguration($xml)
    {
        if (isset($xml->configurations)) {
            foreach ($xml->configurations->configuration as $data) {
                /** @var SimpleXMLElement $data */
                $attributes = $data->attributes();
                $name = (string)$attributes['name'];

                if (isset($name) && isset($attributes['value']) && Configuration::get($name) !== false) {
                    if (!Configuration::updateValue($name, (string)$attributes['value'])) {
                        $this->_errors[] = Context::getContext()->getTranslator()->trans(
                            'An error occurred during the configuration setup: %1$s',
                            array($name),
                            'Admin.International.Notification'
                        );
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param SimpleXMLElement $xml
     * @return bool
     */
    protected function _installGroups($xml)
    {
        return $this->updateDefaultGroupDisplayMethod($xml);
    }

    /**
     * @param SimpleXMLElement $xml
     * @return bool
     */
    protected function updateDefaultGroupDisplayMethod($xml)
    {
        if (isset($xml->group_default)) {
            $attributes = $xml->group_default->attributes();
            if (isset($attributes['price_display_method']) && in_array((int)$attributes['price_display_method'], array(0, 1))) {
                Configuration::updateValue('PRICE_DISPLAY_METHOD', (int)$attributes['price_display_method']);

                foreach (array((int)Configuration::get('PS_CUSTOMER_GROUP'), (int)Configuration::get('PS_GUEST_GROUP'), (int)Configuration::get('PS_UNIDENTIFIED_GROUP')) as $id_group) {
                    $group = new Group((int)$id_group);
                    $group->price_display_method = (int)$attributes['price_display_method'];
                    if (!$group->save()) {
                        $this->_errors[] = Context::getContext()->getTranslator()->trans('An error occurred during the default group update', array(), 'Admin.International.Notification');
                    }
                }
            } else {
                $this->_errors[] = Context::getContext()->getTranslator()->trans('An error has occurred during the default group update', array(), 'Admin.International.Notification');
            }
        }

        return true;
    }

    public function getErrors()
    {
        return $this->_errors;
    }
}
