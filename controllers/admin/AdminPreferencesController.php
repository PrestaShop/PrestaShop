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

/**
 * @property Configuration $object
 */
class AdminPreferencesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'Configuration';
        $this->table = 'configuration';

        parent::__construct();

        // Prevent classes which extend AdminPreferences to load useless data
        if (get_class($this) == 'AdminPreferencesController') {
            $round_mode = array(
                array(
                    'value' => PS_ROUND_HALF_UP,
                    'name' => $this->trans('Round up away from zero, when it is half way there (recommended)', array(), 'Admin.Shopparameters.Feature')
                ),
                array(
                    'value' => PS_ROUND_HALF_DOWN,
                    'name' => $this->trans('Round down towards zero, when it is half way there', array(), 'Admin.Shopparameters.Feature')
                ),
                array(
                    'value' => PS_ROUND_HALF_EVEN,
                    'name' => $this->trans('Round towards the next even value', array(), 'Admin.Shopparameters.Feature')
                ),
                array(
                    'value' => PS_ROUND_HALF_ODD,
                    'name' => $this->trans('Round towards the next odd value', array(), 'Admin.Shopparameters.Feature')
                ),
                array(
                    'value' => PS_ROUND_UP,
                    'name' => $this->trans('Round up to the nearest value', array(), 'Admin.Shopparameters.Feature')
                ),
                array(
                    'value' => PS_ROUND_DOWN,
                    'name' => $this->trans('Round down to the nearest value', array(), 'Admin.Shopparameters.Feature')
                ),
            );
            $activities1 = array(
                0 => $this->trans('-- Please choose your main activity --', array(), 'Install'),
                2 => $this->trans('Animals and Pets', array(), 'Install'),
                3 => $this->trans('Art and Culture', array(), 'Install'),
                4 => $this->trans('Babies', array(), 'Install'),
                5 => $this->trans('Beauty and Personal Care', array(), 'Install'),
                6 => $this->trans('Cars', array(), 'Install'),
                7 => $this->trans('Computer Hardware and Software', array(), 'Install'),
                8 => $this->trans('Download', array(), 'Install'),
                9 => $this->trans('Fashion and accessories', array(), 'Install'),
                10 => $this->trans('Flowers, Gifts and Crafts', array(), 'Install'),
                11 => $this->trans('Food and beverage', array(), 'Install'),
                12 => $this->trans('HiFi, Photo and Video', array(), 'Install'),
                13 => $this->trans('Home and Garden', array(), 'Install'),
                14 => $this->trans('Home Appliances', array(), 'Install'),
                15 => $this->trans('Jewelry', array(), 'Install'),
                1 => $this->trans('Lingerie and Adult', array(), 'Install'),
                16 => $this->trans('Mobile and Telecom', array(), 'Install'),
                17 => $this->trans('Services', array(), 'Install'),
                18 => $this->trans('Shoes and accessories', array(), 'Install'),
                19 => $this->trans('Sport and Entertainment', array(), 'Install'),
                20 => $this->trans('Travel', array(), 'Install')
            );
            $activities2 = array();
            foreach ($activities1 as $value => $name) {
                $activities2[] = array('value' => $value, 'name' => $name);
            }

            $fields = array(
                'PS_SSL_ENABLED' => array(
                    'title' => $this->trans('Enable SSL', array(), 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('If you own an SSL certificate for your shop\'s domain name, you can activate SSL encryption (https://) for customer account identification and order processing.', array(), 'Admin.Shopparameters.Help'),
                    'hint' => $this->trans('If you want to enable SSL on all the pages of your shop, activate the "Enable on all the pages" option below.', array(), 'Admin.Shopparameters.Help'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => '0'
                ),
            );

            $fields['PS_SSL_ENABLED_EVERYWHERE'] = array(
                'title' => $this->trans('Enable SSL on all pages', array(), 'Admin.Shopparameters.Feature'),
                'desc' => $this->trans('When enabled, all the pages of your shop will be SSL-secured.', array(), 'Admin.Shopparameters.Help'),
                'validation' => 'isBool',
                'cast' => 'intval',
                'type' => 'bool',
                'default' => '0',
                'disabled' => (Tools::getValue('PS_SSL_ENABLED', Configuration::get('PS_SSL_ENABLED'))) ? false : true
            );

            $fields = array_merge($fields, array(
                'PS_TOKEN_ENABLE' => array(
                    'title' => $this->trans('Increase front office security', array(), 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('Enable or disable token in the Front Office to improve PrestaShop\'s security.', array(), 'Admin.Shopparameters.Help'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => '0',
                    'visibility' => Shop::CONTEXT_ALL
                ),
                'PS_ALLOW_HTML_IFRAME' => array(
                    'title' => $this->trans('Allow iframes on HTML fields', array(), 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('Allow iframes on text fields like product description. We recommend that you leave this option disabled.', array(), 'Admin.Shopparameters.Help'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => '0'
                ),
                'PS_USE_HTMLPURIFIER' => array(
                    'title' => $this->trans('Use HTMLPurifier Library', array(), 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('Clean the HTML content on text fields. We recommend that you leave this option enabled.', array(), 'Admin.Shopparameters.Help'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => '0'
                ),
                'PS_PRICE_ROUND_MODE' => array(
                    'title' => $this->trans('Round mode', array(), 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('You can choose among 6 different ways of rounding prices. "Round up away from zero ..." is the recommended behavior.', array(), 'Admin.Shopparameters.Help'),
                    'validation' => 'isInt',
                    'cast' => 'intval',
                    'type' => 'select',
                    'list' => $round_mode,
                    'identifier' => 'value'
                ),
                'PS_ROUND_TYPE' => array(
                    'title' => $this->trans('Round type', array(), 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('You can choose when to round prices: either on each item, each line or the total (of an invoice, for example).', array(), 'Admin.Shopparameters.Help'),
                    'cast' => 'intval',
                    'type' => 'select',
                    'list' => array(
                        array(
                            'name' => $this->trans('Round on each item', array(), 'Admin.Shopparameters.Feature'),
                            'id' => Order::ROUND_ITEM
                        ),
                        array(
                            'name' => $this->trans('Round on each line', array(), 'Admin.Shopparameters.Feature'),
                            'id' => Order::ROUND_LINE
                        ),
                        array(
                            'name' => $this->trans('Round on the total', array(), 'Admin.Shopparameters.Feature'),
                            'id' => Order::ROUND_TOTAL
                        ),
                    ),
                    'identifier' => 'id'
                ),
                'PS_PRICE_DISPLAY_PRECISION' => array(
                    'title' => $this->trans('Number of decimals', array(), 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('Choose how many decimals you want to display', array(), 'Admin.Shopparameters.Help'),
                    'validation' => 'isUnsignedInt',
                    'cast' => 'intval',
                    'type' => 'text',
                    'class' => 'fixed-width-xxl'
                ),
                'PS_DISPLAY_SUPPLIERS' => array(
                    'title' => $this->trans('Display brands and suppliers', array(), 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('Enable brands and suppliers pages on your front office even when their respective modules are disabled.', array(), 'Admin.Shopparameters.Help'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool'
                ),
                'PS_DISPLAY_BEST_SELLERS' => array(
                    'title' => $this->trans('Display best sellers', array(), 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('Enable best sellers page on your front office even when its respective module is disabled.', array(), 'Admin.Shopparameters.Help'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool'
                ),
                'PS_MULTISHOP_FEATURE_ACTIVE' => array(
                    'title' => $this->trans('Enable Multistore', array(), 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('The multistore feature allows you to manage several e-shops with one Back Office. If this feature is enabled, a "Multistore" page will be available in the "Advanced Parameters" menu.', array(), 'Admin.Shopparameters.Help'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'visibility' => Shop::CONTEXT_ALL
                ),
                'PS_SHOP_ACTIVITY' => array(
                    'title' => $this->trans('Main Shop Activity', array(), 'Admin.Shopparameters.Feature'),
                    'validation' => 'isInt',
                    'cast' => 'intval',
                    'type' => 'select',
                    'list' => $activities2,
                    'identifier' => 'value'
                ),
            ));

            // No HTTPS activation if you haven't already.
            if (!Tools::usingSecureMode() && !Configuration::get('PS_SSL_ENABLED')) {
                $requestUri = '';
                if (array_key_exists('REQUEST_URI', $_SERVER)) {
                    $requestUri = $_SERVER['REQUEST_URI'];
                }

                $fields['PS_SSL_ENABLED']['type'] = 'disabled';
                $fields['PS_SSL_ENABLED']['disabled'] = '<a class="btn btn-link" href="https://'.
                    Tools::getShopDomainSsl().
                    Tools::safeOutput($requestUri).'">'.
                    $this->trans('Please click here to check if your shop supports HTTPS.', array(), 'Admin.Shopparameters.Feature').'</a>';
            }

            $this->fields_options = array(
                'general' => array(
                    'title' =>    $this->trans('General', array(), 'Admin.Global'),
                    'icon' =>    'icon-cogs',
                    'fields' =>    $fields,
                    'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions')),
                ),
            );
        }
    }

    /**
     * Enable / disable multishop menu if multishop feature is activated
     *
     * @param string $value
     */
    public function updateOptionPsMultishopFeatureActive($value)
    {
        Configuration::updateValue('PS_MULTISHOP_FEATURE_ACTIVE', $value);

        $tab = Tab::getInstanceFromClassName('AdminShopGroup');
        if (Validate::isLoadedObject($tab)) {
            $tab->active = (bool)Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE');
            $tab->update();
        }
    }
}
