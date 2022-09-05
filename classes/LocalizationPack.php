<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;

class LocalizationPackCore
{
    public $name;
    public $version;

    protected $iso_code_lang;
    protected $iso_currency;
    protected $_errors = [];

    /**
     * Loads localization pack.
     *
     * @param SimpleXMLElement|string $pack Localization pack as SimpleXMLElement or plain XML string
     * @param array $selection Content to import selection
     * @param bool $install_mode Whether mode is installation or not
     * @param string|null $iso_localization_pack Country Alpha-2 ISO code
     *
     * @return bool
     */
    public function loadLocalisationPack($pack, $selection, $install_mode = false, $iso_localization_pack = null)
    {
        if ($pack instanceof SimpleXMLElement) {
            $xml = $pack;
        } elseif (!$xml = @simplexml_load_string($pack)) {
            return false;
        }

        libxml_clear_errors();
        $main_attributes = $xml->attributes();
        $this->name = (string) $main_attributes['name'];
        $this->version = (string) $main_attributes['version'];
        if ($iso_localization_pack) {
            $id_country = (int) Country::getByIso($iso_localization_pack);

            if ($id_country) {
                $country = new Country($id_country);
            }
            if (!$id_country || !Validate::isLoadedObject($country)) {
                $this->_errors[] = Context::getContext()->getTranslator()->trans(
                    'Cannot load country: %d',
                    [$id_country],
                    'Admin.International.Notification'
                );

                return false;
            }
            if (!$country->active) {
                $country->active = true;
                if (!$country->update()) {
                    $this->_errors[] = Context::getContext()->getTranslator()->trans(
                        'Cannot enable the associated country: %s',
                        [$country->name],
                        'Admin.International.Notification'
                    );
                }
            }
        }

        $res = true;

        if (empty($selection)) {
            $res = $this->_installStates($xml);
            $res = $res && $this->_installTaxes($xml);
            $res = $res && $this->_installCurrencies($xml, $install_mode);
            $res = $res && $this->installConfiguration($xml);
            $res = $res && $this->installModules($xml);
            $res = $res && $this->updateDefaultGroupDisplayMethod($xml);

            if (($res || $install_mode) && isset($this->iso_code_lang)) {
                if (!($id_lang = (int) Language::getIdByIso($this->iso_code_lang, true))) {
                    $id_lang = 1;
                }
                if (!$install_mode) {
                    Configuration::updateValue('PS_LANG_DEFAULT', $id_lang);
                }
            } elseif (!isset($this->iso_code_lang) && $install_mode) {
                $id_lang = 1;
            }

            if (!empty($id_lang) && !Language::isInstalled(Language::getIsoById($id_lang))) {
                $res = $res && $this->_installLanguages($xml, $install_mode);
                $res = $res && $this->_installUnits($xml);
            }

            if ($install_mode && $res && isset($this->iso_currency)) {
                Cache::clean('Currency::getIdByIsoCode_*');
                $res = Configuration::updateValue('PS_CURRENCY_DEFAULT', (int) Currency::getIdByIsoCode($this->iso_currency));
                Currency::refreshCurrencies();
            }
        } else {
            foreach ($selection as $selected) {
                $res = $res && Validate::isLocalizationPackSelection($selected) ? $this->{'_install' . $selected}($xml, $install_mode) : false;
            }
        }

        return $res;
    }

    /**
     * @param SimpleXMLElement $xml
     *
     * @return bool
     *
     * @throws PrestaShopException
     */
    protected function _installStates($xml)
    {
        if (isset($xml->states->state)) {
            foreach ($xml->states->state as $data) {
                /** @var SimpleXMLElement $data */
                $attributes = $data->attributes();
                $id_country = ($attributes['country']) ? (int) Country::getByIso((string) ($attributes['country'])) : false;
                $id_state = ($id_country) ? State::getIdByIso($attributes['iso_code'], $id_country) : State::getIdByName($attributes['name']);

                if (!$id_state) {
                    $state = new State();
                    $state->name = (string) ($attributes['name']);
                    $state->iso_code = (string) ($attributes['iso_code']);
                    $state->id_country = $id_country;

                    $id_zone = (int) Zone::getIdByName((string) ($attributes['zone']));
                    if (!$id_zone) {
                        $zone = new Zone();
                        $zone->name = (string) $attributes['zone'];
                        $zone->active = true;

                        if (!$zone->add()) {
                            $this->_errors[] = Context::getContext()->getTranslator()->trans('Invalid Zone name.', [], 'Admin.International.Notification');

                            return false;
                        }

                        $id_zone = $zone->id;
                    }

                    $state->id_zone = $id_zone;

                    if (!$state->validateFields()) {
                        $this->_errors[] = Context::getContext()->getTranslator()->trans('Invalid state properties.', [], 'Admin.International.Notification');

                        return false;
                    }

                    $country = new Country($state->id_country);
                    if (!$country->contains_states) {
                        $country->contains_states = true;
                        if (!$country->update()) {
                            $this->_errors[] = Context::getContext()->getTranslator()->trans('Cannot update the associated country: %s', [$country->name], 'Admin.International.Notification');
                        }
                    }

                    if (!$state->add()) {
                        $this->_errors[] = Context::getContext()->getTranslator()->trans('An error occurred while adding the state.', [], 'Admin.International.Notification');

                        return false;
                    }
                } else {
                    $state = new State($id_state);
                    if (!Validate::isLoadedObject($state)) {
                        $this->_errors[] = Context::getContext()->getTranslator()->trans('An error occurred while fetching the state.', [], 'Admin.International.Notification');

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
     * @return bool
     *
     * @throws PrestaShopException
     */
    protected function _installTaxes($xml)
    {
        if (isset($xml->taxes->tax)) {
            $assoc_taxes = [];
            foreach ($xml->taxes->tax as $taxData) {
                /** @var SimpleXMLElement $taxData */
                $attributes = $taxData->attributes();
                if (($id_tax = Tax::getTaxIdByName($attributes['name']))) {
                    $assoc_taxes[(int) $attributes['id']] = $id_tax;

                    continue;
                }
                $tax = new Tax();
                $tax->name[(int) Configuration::get('PS_LANG_DEFAULT')] = (string) $attributes['name'];
                $tax->rate = (float) $attributes['rate'];
                $tax->active = true;

                if (($error = $tax->validateFields(false, true)) !== true || ($error = $tax->validateFieldsLang(false, true)) !== true) {
                    $this->_errors[] = Context::getContext()->getTranslator()->trans('Invalid tax properties.', [], 'Admin.International.Notification') . ' ' . $error;

                    return false;
                }

                if (!$tax->add()) {
                    $this->_errors[] = Context::getContext()->getTranslator()->trans('An error occurred while importing the tax: %s', [(string) $attributes['name']], 'Admin.International.Notification');

                    return false;
                }

                $assoc_taxes[(int) $attributes['id']] = $tax->id;
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
                $trg->active = true;

                if (!$trg->save()) {
                    $this->_errors[] = Context::getContext()->getTranslator()->trans('This tax rule cannot be saved.', [], 'Admin.International.Notification');

                    return false;
                }

                foreach ($group->taxRule as $rule) {
                    /** @var SimpleXMLElement $rule */
                    $rule_attributes = $rule->attributes();

                    // Validation
                    if (!isset($rule_attributes['iso_code_country'])) {
                        continue;
                    }

                    $id_country = (int) Country::getByIso(strtoupper($rule_attributes['iso_code_country']));
                    if (!$id_country) {
                        continue;
                    }

                    if (!isset($rule_attributes['id_tax']) || !array_key_exists((int) $rule_attributes['id_tax'], $assoc_taxes)) {
                        continue;
                    }

                    // Default values
                    $id_state = (int) isset($rule_attributes['iso_code_state']) ? State::getIdByIso(strtoupper($rule_attributes['iso_code_state'])) : 0;
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
                    $tr->behavior = (string) $behavior;
                    $tr->description = '';
                    $tr->id_tax = $assoc_taxes[(int) $rule_attributes['id_tax']];
                    $tr->save();
                }
            }
        }

        return true;
    }

    /**
     * @param SimpleXMLElement $xml
     * @param bool $install_mode
     *
     * @return bool
     *
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

                $sfContainer = SymfonyContainer::getInstance();
                $commandBus = $sfContainer->get('prestashop.core.command_bus');

                $command = new AddCurrencyCommand(
                    (string) $attributes['iso_code'],
                    (float) 1,
                    true
                );

                /* @var CurrencyId $currencyId */
                try {
                    $currencyId = $commandBus->handle($command);
                } catch (CurrencyException $e) {
                    $this->_errors[] = null;
                    Context::getContext()->getTranslator()->trans(
                        'An error occurred while importing the currency: %s',
                        [(string) ($attributes['name'])],
                        'Admin.International.Notification'
                    );

                    return false;
                }

                Cache::clear();

                PaymentModule::addCurrencyPermissions($currencyId->getValue());
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
     * @return LocaleRepository
     *
     * @throws Exception
     */
    protected function getCldrLocaleRepository()
    {
        $context = Context::getContext();
        $container = isset($context->controller) ? $context->controller->getContainer() : null;
        if (null === $container) {
            $container = SymfonyContainer::getInstance();
        }

        /** @var LocaleRepository $localeRepoCLDR */
        $localeRepoCLDR = $container->get('prestashop.core.localization.cldr.locale_repository');

        return $localeRepoCLDR;
    }

    /**
     * @param SimpleXMLElement $xml
     * @param bool $install_mode
     *
     * @return bool
     */
    protected function _installLanguages($xml, $install_mode = false)
    {
        $attributes = [];
        if (isset($xml->languages->language)) {
            foreach ($xml->languages->language as $data) {
                /** @var SimpleXMLElement $data */
                $attributes = $data->attributes();
                // if we are not in an installation context or if the pack is not available in the local directory
                if (Language::getIdByIso($attributes['iso_code']) && !$install_mode) {
                    continue;
                }

                $freshInstall = empty(Language::getIdByIso($attributes['iso_code']));
                $errors = Language::downloadAndInstallLanguagePack($attributes['iso_code'], $attributes['version'], $attributes, $freshInstall);
                if ($errors !== true && is_array($errors)) {
                    $this->_errors = array_merge($this->_errors, $errors);
                }
            }
        }

        // change the default language if there is only one language in the localization pack
        if (!count($this->_errors) && $install_mode && isset($attributes['iso_code']) && count($xml->languages->language) == 1) {
            $this->iso_code_lang = $attributes['iso_code'];
        }

        // refreshed localized currency data
        $this->refreshLocalizedCurrenciesData();

        return !count($this->_errors);
    }

    /**
     * This method aims to update localized data in currencies from CLDR reference.
     * Eg currency symbol used depends on language, so it has to be updated when adding a new language
     * Use-case: adding a new language should trigger all currencies update
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    protected function refreshLocalizedCurrenciesData()
    {
        /** @var Currency[] $currencies */
        $currencies = Currency::getCurrencies(true, false, true);
        $languages = Language::getLanguages();
        $localeRepoCLDR = $this->getCldrLocaleRepository();
        foreach ($currencies as $currency) {
            $currency->refreshLocalizedCurrencyData($languages, $localeRepoCLDR);
            $currency->save();
        }
    }

    /**
     * @param SimpleXMLElement $xml
     *
     * @return bool
     */
    protected function _installUnits($xml)
    {
        $varNames = ['weight' => 'PS_WEIGHT_UNIT', 'volume' => 'PS_VOLUME_UNIT', 'short_distance' => 'PS_DIMENSION_UNIT', 'base_distance' => 'PS_BASE_DISTANCE_UNIT', 'long_distance' => 'PS_DISTANCE_UNIT'];
        if (isset($xml->units->unit)) {
            foreach ($xml->units->unit as $data) {
                /** @var SimpleXMLElement $data */
                $attributes = $data->attributes();
                if (!isset($varNames[(string) ($attributes['type'])])) {
                    $this->_errors[] = Context::getContext()->getTranslator()->trans('Localization pack corrupted: wrong unit type.', [], 'Admin.International.Notification');

                    return false;
                }
                if (!Configuration::updateValue($varNames[(string) ($attributes['type'])], (string) ($attributes['value']))) {
                    $this->_errors[] = Context::getContext()->getTranslator()->trans('An error occurred while setting the units.', [], 'Admin.International.Notification');

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Install/Uninstall a module from a localization file
     * <modules>
     *     <module name="module_name" [install="0|1"] />.
     *
     * @param SimpleXMLElement $xml
     *
     * @return bool
     */
    protected function installModules($xml)
    {
        if (isset($xml->modules)) {
            foreach ($xml->modules->module as $data) {
                /** @var SimpleXMLElement $data */
                $attributes = $data->attributes();
                $name = (string) $attributes['name'];
                if ($module = Module::getInstanceByName($name)) {
                    $install = ($attributes['install'] == 1) ? true : false;
                    $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
                    $moduleManager = $moduleManagerBuilder->build();

                    if ($install) {
                        if (!$moduleManager->isInstalled($name)) {
                            if (!$module->install()) {
                                $this->_errors[] = Context::getContext()->getTranslator()->trans('An error occurred while installing the module: %s', [$name], 'Admin.International.Notification');
                            }
                        }
                    } elseif ($moduleManager->isInstalled($name)) {
                        if (!$module->uninstall()) {
                            $this->_errors[] = Context::getContext()->getTranslator()->trans('An error occurred while uninstalling the module: %s', [$name], 'Admin.International.Notification');
                        }
                    }

                    unset($module);
                } else {
                    $this->_errors[] = Context::getContext()->getTranslator()->trans('An error has occurred, this module does not exist: %s', [$name], 'Admin.International.Notification');
                }
            }
        }

        return true;
    }

    /**
     * Update a configuration variable from a localization file
     * <configuration>
     * <configuration name="variable_name" value="variable_value" />.
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

                if (isset($attributes['value']) && Configuration::get($name) !== false) {
                    if (!Configuration::updateValue($name, (string) $attributes['value'])) {
                        $this->_errors[] = Context::getContext()->getTranslator()->trans(
                            'An error occurred during the configuration setup: %1$s',
                            [$name],
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
     *
     * @return bool
     */
    protected function _installGroups($xml)
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
            if (isset($attributes['price_display_method']) && in_array((int) $attributes['price_display_method'], [0, 1])) {
                Configuration::updateValue('PRICE_DISPLAY_METHOD', (int) $attributes['price_display_method']);

                foreach ([(int) Configuration::get('PS_CUSTOMER_GROUP'), (int) Configuration::get('PS_GUEST_GROUP'), (int) Configuration::get('PS_UNIDENTIFIED_GROUP')] as $id_group) {
                    $group = new Group((int) $id_group);
                    $group->price_display_method = (int) $attributes['price_display_method'];
                    if (!$group->save()) {
                        $this->_errors[] = Context::getContext()->getTranslator()->trans('An error occurred during the default group update', [], 'Admin.International.Notification');
                    }
                }
            } else {
                $this->_errors[] = Context::getContext()->getTranslator()->trans('An error has occurred during the default group update', [], 'Admin.International.Notification');
            }
        }

        return true;
    }

    public function getErrors()
    {
        return $this->_errors;
    }
}
