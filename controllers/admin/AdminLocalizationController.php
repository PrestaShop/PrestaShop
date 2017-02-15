<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class AdminLocalizationControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        $this->fields_options = array(
            'general' => array(
                'title' =>    $this->trans('Configuration', array(), 'Admin.Global'),
                'fields' =>    array(
                    'PS_LANG_DEFAULT' => array(
                        'title' => $this->trans('Default language', array(), 'Admin.International.Feature'),
                        'hint' => $this->trans('The default language used in your shop.', array(), 'Admin.International.Help'),
                        'cast' => 'intval',
                        'type' => 'select',
                        'identifier' => 'id_lang',
                        'list' => Language::getLanguages(false)
                    ),
                    'PS_DETECT_LANG' => array(
                        'title' => $this->trans('Set language from browser', array(), 'Admin.International.Feature'),
                        'desc' => $this->trans('Set browser language as default language', array(), 'Admin.International.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                        'default' => '1'
                    ),
                    'PS_COUNTRY_DEFAULT' => array(
                        'title' => $this->trans('Default country', array(), 'Admin.International.Feature'),
                        'hint' => $this->trans('The default country used in your shop.', array(), 'Admin.International.Help'),
                        'cast' => 'intval',
                        'type' => 'select',
                        'class' => 'chosen',
                        'identifier' => 'id_country',
                        'list' => Country::getCountries($this->context->language->id)
                    ),
                    'PS_DETECT_COUNTRY' => array(
                        'title' => $this->trans('Set default country from browser language', array(), 'Admin.International.Feature'),
                        'desc' => $this->trans('Set country corresponding to browser language', array(), 'Admin.International.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                        'default' => '1'
                    ),
                    'PS_CURRENCY_DEFAULT' => array(
                        'title' => $this->trans('Default currency', array(), 'Admin.International.Feature'),
                        'hint' =>
                            $this->trans('The default currency used in your shop.', array(), 'Admin.International.Help').' - '.$this->trans('If you change the default currency, you will have to manually edit every product price.', array(), 'Admin.International.Help'),
                        'cast' => 'intval',
                        'type' => 'select',
                        'identifier' => 'id_currency',
                        'list' => Currency::getCurrencies(false, true, true)
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            ),
            'localization' => array(
                'title' =>    $this->trans('Local units', array(), 'Admin.International.Feature'),
                'icon' =>    'icon-globe',
                'fields' =>    array(
                    'PS_WEIGHT_UNIT' => array(
                        'title' => $this->trans('Weight unit', array(), 'Admin.International.Feature'),
                        'hint' => $this->trans('The default weight unit for your shop (e.g. "kg" for kilograms, "lbs" for pound-mass, etc.).', array(), 'Admin.International.Help'),
                        'validation' => 'isWeightUnit',
                        'required' => true,
                        'type' => 'text',
                        'class' => 'fixed-width-sm'
                    ),
                    'PS_DISTANCE_UNIT' => array(
                        'title' => $this->trans('Distance unit', array(), 'Admin.International.Feature'),
                        'hint' => $this->trans('The default distance unit for your shop (e.g. "km" for kilometer, "mi" for mile, etc.).', array(), 'Admin.International.Help'),
                        'validation' => 'isDistanceUnit',
                        'required' => true,
                        'type' => 'text',
                        'class' => 'fixed-width-sm'
                    ),
                    'PS_VOLUME_UNIT' => array(
                        'title' => $this->trans('Volume unit', array(), 'Admin.International.Feature'),
                        'hint' => $this->trans('The default volume unit for your shop (e.g. "L" for liter, "gal" for gallon, etc.).', array(), 'Admin.International.Help'),
                        'validation' => 'isWeightUnit',
                        'required' => true,
                        'type' => 'text',
                        'class' => 'fixed-width-sm'
                    ),
                    'PS_DIMENSION_UNIT' => array(
                        'title' => $this->trans('Dimension unit', array(), 'Admin.International.Feature'),
                        'hint' => $this->trans('The default dimension unit for your shop (e.g. "cm" for centimeter, "in" for inch, etc.).', array(), 'Admin.International.Help'),
                        'validation' => 'isDistanceUnit',
                        'required' => true,
                        'type' => 'text',
                        'class' => 'fixed-width-sm'
                    )
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            ),
            'options' => array(
                'title' =>    $this->trans('Advanced', array(), 'Admin.Global'),
                'fields' =>    array(
                    'PS_LOCALE_LANGUAGE' => array(
                        'title' => $this->trans('Language identifier', array(), 'Admin.International.Feature'),
                        'hint' => $this->trans('The ISO 639-1 identifier for the language of the country where your web server is located (en, fr, sp, ru, pl, nl, etc.).', array(), 'Admin.International.Help'),
                        'validation' => 'isLanguageIsoCode',
                        'type' => 'text',
                        'visibility' => Shop::CONTEXT_ALL,
                        'class' => 'fixed-width-sm'
                    ),
                    'PS_LOCALE_COUNTRY' => array(
                        'title' => $this->trans('Country identifier', array(), 'Admin.International.Feature'),
                        'hint' => $this->trans('The ISO 3166-1 alpha-2 identifier for the country/region where your web server is located, in lowercase (us, gb, fr, sp, ru, pl, nl, etc.).', array(), 'Admin.International.Help'),
                        'validation' => 'isLanguageIsoCode',
                        'type' => 'text',
                        'visibility' => Shop::CONTEXT_ALL,
                        'class' => 'fixed-width-sm'
                    )
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            )
        );

        if (function_exists('date_default_timezone_set')) {
            $this->fields_options['general']['fields']['PS_TIMEZONE'] = array(
                'title' => $this->trans('Time zone', array(), 'Admin.International.Feature'),
                'validation' => 'isAnything',
                'type' => 'select',
                'class' => 'chosen',
                'list' => Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT name FROM '._DB_PREFIX_.'timezone'),
                'identifier' => 'name',
                'visibility' => Shop::CONTEXT_ALL
            );
        }
    }



    public function postProcess()
    {
        if (_PS_MODE_DEMO_) {
            $this->errors[] = $this->trans('This functionality has been disabled.', array(), 'Admin.Notifications.Error');
            return;
        }

        if (!extension_loaded('openssl')) {
            $this->displayWarning($this->trans('Importing a new language may fail without the OpenSSL module. Please enable "openssl.so" on your server configuration.', array(), 'Admin.International.Notification'));
        }

        if (Tools::isSubmit('submitLocalizationPack')) {
            $version = str_replace('.', '', _PS_VERSION_);
            $version = substr($version, 0, 2);

            if (($iso_localization_pack = Tools::getValue('iso_localization_pack')) && Validate::isFileName($iso_localization_pack)) {
                if (Tools::getValue('download_updated_pack') == '1' || defined('_PS_HOST_MODE_')) {
                    $pack = @Tools::file_get_contents(_PS_API_URL_.'/localization/'.$version.'/'.$iso_localization_pack.'.xml');
                } else {
                    $pack = false;
                }

                if (defined('_PS_HOST_MODE_')) {
                    $path = _PS_CORE_DIR_.'/localization/'.$iso_localization_pack.'.xml';
                } else {
                    $path = _PS_ROOT_DIR_.'/localization/'.$iso_localization_pack.'.xml';
                }

                if (!$pack && !($pack = @Tools::file_get_contents($path))) {
                    $this->errors[] = $this->trans('Cannot load the localization pack.', array(), 'Admin.International.Notification');
                }

                if (!$selection = Tools::getValue('selection')) {
                    $this->errors[] = $this->trans('Please select at least one item to import.', array(), 'Admin.International.Notification');
                } else {
                    foreach ($selection as $selected) {
                        if (!Validate::isLocalizationPackSelection($selected)) {
                            $this->errors[] = $this->trans('Invalid selection', array(), 'Admin.Notifications.Error');
                            return;
                        }
                    }
                    $localization_pack = new LocalizationPack();
                    if (!$localization_pack->loadLocalisationPack($pack, $selection, false, $iso_localization_pack)) {
                        $this->errors = array_merge($this->errors, $localization_pack->getErrors());
                    } else {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=23&token='.$this->token);
                    }
                }
            }
        }

        // Remove the module list cache if the default country changed
        if (Tools::isSubmit('submitOptionsconfiguration') && file_exists(Module::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST)) {
            @unlink(Module::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST);
        }
        parent::postProcess();
    }

    public function sortLocalizationsPack($a, $b)
    {
        return $a['name'] > $b['name'];
    }

    public function renderForm()
    {
        $localizations_pack = false;
        $this->tpl_option_vars['options_content'] = $this->renderOptions();

        $xml_localization = Tools::simplexml_load_file(_PS_API_URL_.'/rss/localization.xml');
        if (!$xml_localization) {
            $localization_file = _PS_ROOT_DIR_.'/localization/localization.xml';
            if (file_exists($localization_file)) {
                $xml_localization = @simplexml_load_file($localization_file);
            }
        }

        // Array to hold the list of country ISOs that have a localization pack hosted on prestashop.com
        $remote_isos = array();

        $i = 0;
        if ($xml_localization) {
            foreach ($xml_localization->pack as $key => $pack) {
                $remote_isos[(string)$pack->iso] = true;
                $localizations_pack[$i]['iso_localization_pack'] = (string)$pack->iso;
                $localizations_pack[$i]['name'] = (string)$pack->name;
                $i++;
            }
        }

        if (!$localizations_pack) {
            return $this->displayWarning($this->trans('Cannot connect to '._PS_API_URL_));
        }

        // Add local localization .xml files to the list if they are not already there
        foreach (scandir(_PS_ROOT_DIR_.'/localization/') as $entry) {
            $m = array();
            if (preg_match('/^([a-z]{2})\.xml$/', $entry, $m)) {
                $iso = $m[1];
                if (empty($remote_isos[$iso])) {
                    // if the pack is only there locally and not on prestashop.com

                    $xml_pack = @simplexml_load_file(_PS_ROOT_DIR_.'/localization/'.$entry);
                    if (!$xml_pack) {
                        return $this->displayWarning($this->trans('%language% could not be loaded', array('%language%' => $entry),  'Admin.International.Notification'));
                    }
                    $localizations_pack[$i]['iso_localization_pack'] = $iso;
                    $localizations_pack[$i]['name'] = $this->trans('%s (local)', array((string)$xml_pack['name']), 'Admin.International.Feature');
                    $i++;
                }
            }
        }

        usort($localizations_pack, array($this, 'sortLocalizationsPack'));

        $selection_import = array(
            array(
                'id' => 'states',
                'val' => 'states',
                'name' => $this->trans('States', array(), 'Admin.International.Feature')
            ),
            array(
                'id' => 'taxes',
                'val' => 'taxes',
                'name' => $this->trans('Taxes', array(), 'Admin.Global')
            ),
            array(
                'id' => 'currencies',
                'val' => 'currencies',
                'name' => $this->trans('Currencies', array(), 'Admin.Global')
            ),
            array(
                'id' => 'languages',
                'val' => 'languages',
                'name' => $this->trans('Languages', array(), 'Admin.Global')
            ),
            array(
                'id' => 'units',
                'val' => 'units',
                'name' => $this->trans('Units (e.g. weight, volume, distance)', array(), 'Admin.International.Feature')
            ),
            array(
                'id' => 'groups',
                'val' => 'groups',
                'name' => $this->trans('Change the behavior of the price display for groups', array(), 'Admin.International.Feature')
            )
        );

        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->trans('Import a localization pack', array(), 'Admin.International.Feature'),
                'icon' => 'icon-globe'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'class' => 'chosen',
                    'label' => $this->trans('Localization pack you want to import', array(), 'Admin.International.Feature'),
                    'name' => 'iso_localization_pack',
                    'options' => array(
                        'query' => $localizations_pack,
                        'id' => 'iso_localization_pack',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'checkbox',
                    'label' => $this->trans('Content to import', array(), 'Admin.International.Feature'),
                    'name' => 'selection[]',
                    'lang' => true,
                    'values' => array(
                        'query' => $selection_import,
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type'     => 'radio',
                    'label'  => $this->trans('Download pack data', array(), 'Admin.International.Feature'),
                    'desc'     => $this->trans('If set to yes then the localization pack will be downloaded from prestashop.com. Otherwise the local xml file found in the localization folder of your PrestaShop installation will be used.', array(), 'Admin.International.Help'),
                    'name'     => 'download_updated_pack',
                    'is_bool'=> true,
                    'values' => array(
                        array(
                            'id'    => 'download_updated_pack_yes',
                            'value'    => 1,
                            'label' => $this->trans('Yes', array(), 'Admin.Global')
                        ),
                        array(
                            'id'    => 'download_updated_pack_no',
                            'value'    => 0,
                            'label' => $this->trans('No', array(), 'Admin.Global')
                        )
                    )
                )
            ),
            'submit' => array(
                'title' => $this->trans('Import', array(), 'Admin.Actions'),
                'icon' => 'process-icon-import',
                'name' => 'submitLocalizationPack'
            ),
        );

        $this->fields_value = array(
            'selection[]_states' => true,
            'selection[]_taxes' => true,
            'selection[]_currencies' => true,
            'selection[]_languages' => true,
            'selection[]_units' => true,
            'download_updated_pack' => 1
        );

        $this->show_toolbar = true;
        return parent::renderForm();
    }

    public function initContent()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        $this->context->smarty->assign(array(
            'localization_form' => $this->renderForm(),
            'localization_options' => $this->renderOptions(),
        ));
    }

    public function display()
    {
        $this->initContent();
        parent::display();
    }

    public function beforeUpdateOptions()
    {
        $lang = new Language((int)Tools::getValue('PS_LANG_DEFAULT'));

        if (!$lang->active) {
            $lang->active = 1;
            $lang->save();
        }
    }

    public function updateOptionPsCurrencyDefault($value)
    {
        if ($value == Configuration::get('PS_CURRENCY_DEFAULT')) {
            return;
        }
        Configuration::updateValue('PS_CURRENCY_DEFAULT', $value);

        /* Set conversion rate of default currency to 1 */
        ObjectModel::updateMultishopTable('Currency', array('conversion_rate' => 1), 'a.id_currency');

        $tmp_context = Shop::getContext();
        if ($tmp_context == Shop::CONTEXT_GROUP) {
            $tmp_shop = Shop::getContextShopGroupID();
        } else {
            $tmp_shop = (int)Shop::getContextShopID();
        }

        foreach (Shop::getContextListShopID() as $id_shop) {
            Shop::setContext(Shop::CONTEXT_SHOP, (int)$id_shop);
            Currency::refreshCurrencies();
        }
        Shop::setContext($tmp_context, $tmp_shop);
    }
}
