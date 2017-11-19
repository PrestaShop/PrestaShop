<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class AdminGeolocationControllerCore extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->bootstrap = true;
        $this->fields_options = array(
            'geolocationConfiguration' => array(
                'title' =>    $this->trans('Geolocation by IP address', array(), 'Admin.International.Feature'),
                'icon' =>    'icon-map-marker',
                'fields' =>    array(
                    'PS_GEOLOCATION_ENABLED' => array(
                        'title' => $this->trans('Geolocation by IP address', array(), 'Admin.International.Feature'),
                        'hint' => $this->trans('This option allows you, among other things, to restrict access to your shop for certain countries. See below.', array(), 'Admin.International.Help'),
                        'validation' => 'isUnsignedId',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            ),
            'geolocationCountries' => array(
                'title' =>    $this->trans('Options', array(), 'Admin.Global'),
                'icon' =>    'icon-map-marker',
                'description' => $this->trans('The following features are only available if you enable the Geolocation by IP address feature.', array(), 'Admin.International.Feature'),
                'fields' =>    array(
                    'PS_GEOLOCATION_BEHAVIOR' => array(
                        'title' => $this->trans('Geolocation behavior for restricted countries', array(), 'Admin.International.Feature'),
                        'type' => 'select',
                        'identifier' => 'key',
                        'list' => array(array('key' => _PS_GEOLOCATION_NO_CATALOG_, 'name' => $this->trans('Visitors cannot see your catalog.', array(), 'Admin.International.Feature')),
                                        array('key' => _PS_GEOLOCATION_NO_ORDER_, 'name' => $this->trans('Visitors can see your catalog but cannot place an order.', array(), 'Admin.International.Feature'))),
                    ),
                    'PS_GEOLOCATION_NA_BEHAVIOR' => array(
                        'title' => $this->trans('Geolocation behavior for other countries', array(), 'Admin.International.Feature'),
                        'type' => 'select',
                        'identifier' => 'key',
                        'list' => array(array('key' => '-1', 'name' => $this->trans('All features are available', array(), 'Admin.International.Feature')),
                                        array('key' => _PS_GEOLOCATION_NO_CATALOG_, 'name' => $this->trans('Visitors cannot see your catalog.', array(), 'Admin.International.Feature')),
                                        array('key' => _PS_GEOLOCATION_NO_ORDER_, 'name' => $this->trans('Visitors can see your catalog but cannot place an order.', array(), 'Admin.International.Feature')))
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            ),
            'geolocationWhitelist' => array(
                'title' =>    $this->trans('IP address whitelist', array(), 'Admin.International.Feature'),
                'icon' =>    'icon-sitemap',
                'description' => $this->trans('You can add IP addresses that will always be allowed to access your shop (e.g. Google bots\' IP).', array(), 'Admin.International.Help'),
                'fields' =>    array(
                    'PS_GEOLOCATION_WHITELIST' => array('title' => $this->trans('Whitelisted IP addresses', array(), 'Admin.International.Feature'), 'type' => 'textarea_newlines', 'cols' => 15, 'rows' => 30),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            ),
        );
    }

    /**
     * @see AdminController::processUpdateOptions()
     */
    public function processUpdateOptions()
    {
        if ($this->isGeoLiteCityAvailable()) {
            Configuration::updateValue('PS_GEOLOCATION_ENABLED', (int)Tools::getValue('PS_GEOLOCATION_ENABLED'));
        } elseif (Tools::getValue('PS_GEOLOCATION_ENABLED')) {
            // stop processing if geolocation is set to yes but geolite pack is not available
            $this->errors[] = $this->trans('The geolocation database is unavailable.', array(), 'Admin.International.Notification');
        }

        if (empty($this->errors)) {
            if (!is_array(Tools::getValue('countries')) || !count(Tools::getValue('countries'))) {
                $this->errors[] = $this->trans('Country selection is invalid.', array(), 'Admin.International.Notification');
            } else {
                Configuration::updateValue(
                    'PS_GEOLOCATION_BEHAVIOR',
                    (!(int)Tools::getValue('PS_GEOLOCATION_BEHAVIOR') ? _PS_GEOLOCATION_NO_CATALOG_ : _PS_GEOLOCATION_NO_ORDER_)
                );
                Configuration::updateValue('PS_GEOLOCATION_NA_BEHAVIOR', (int)Tools::getValue('PS_GEOLOCATION_NA_BEHAVIOR'));
                Configuration::updateValue('PS_ALLOWED_COUNTRIES', implode(';', Tools::getValue('countries')));
            }

            if (!Validate::isCleanHtml(Tools::getValue('PS_GEOLOCATION_WHITELIST'))) {
                $this->errors[] = $this->trans('Invalid whitelist', array(), 'Admin.International.Notification');
            } else {
                Configuration::updateValue(
                    'PS_GEOLOCATION_WHITELIST',
                    str_replace("\n", ';', str_replace("\r", '', Tools::getValue('PS_GEOLOCATION_WHITELIST')))
                );
            }
        }

        return parent::processUpdateOptions();
    }

    public function renderOptions()
    {
        // This field is not declared in class constructor because we want it to be manually post processed
        $this->fields_options['geolocationCountries']['fields']['countries'] = array(
                                'title' => $this->trans('Select the countries from which your store is accessible', array(), 'Admin.International.Feature'),
                                'type' => 'checkbox_table',
                                'identifier' => 'iso_code',
                                'list' => Country::getCountries($this->context->language->id),
                                'auto_value' => false
                            );

        $this->tpl_option_vars = array('allowed_countries' => explode(';', Configuration::get('PS_ALLOWED_COUNTRIES')));

        return parent::renderOptions();
    }

    public function initContent()
    {
        $this->display = 'options';
        if (!$this->isGeoLiteCityAvailable()) {
            $this->displayWarning($this->trans('In order to use Geolocation, please download [1]this file[/1] and extract it (using Winrar or Gzip) into the /app/Resources/geoip/ directory.',
                array(
                    '[1]' => '<a href="http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz" target="_blank">',
                    '[/1]' => '</a>'
                ),
                'Admin.International.Feature'
            ));
            Configuration::updateValue('PS_GEOLOCATION_ENABLED', 0);
        }

        parent::initContent();
    }

    protected function isGeoLiteCityAvailable()
    {
        if (@filemtime(_PS_GEOIP_DIR_._PS_GEOIP_CITY_FILE_)) {
            return true;
        }

        return false;
    }
}
