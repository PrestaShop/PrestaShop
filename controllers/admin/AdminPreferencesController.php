<?php
/**
 * 2007-2018 PrestaShop.
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
            $round_mode = [
                [
                    'value' => PS_ROUND_HALF_UP,
                    'name' => $this->trans('Round up away from zero, when it is half way there (recommended)', [], 'Admin.Shopparameters.Feature'),
                ],
                [
                    'value' => PS_ROUND_HALF_DOWN,
                    'name' => $this->trans('Round down towards zero, when it is half way there', [], 'Admin.Shopparameters.Feature'),
                ],
                [
                    'value' => PS_ROUND_HALF_EVEN,
                    'name' => $this->trans('Round towards the next even value', [], 'Admin.Shopparameters.Feature'),
                ],
                [
                    'value' => PS_ROUND_HALF_ODD,
                    'name' => $this->trans('Round towards the next odd value', [], 'Admin.Shopparameters.Feature'),
                ],
                [
                    'value' => PS_ROUND_UP,
                    'name' => $this->trans('Round up to the nearest value', [], 'Admin.Shopparameters.Feature'),
                ],
                [
                    'value' => PS_ROUND_DOWN,
                    'name' => $this->trans('Round down to the nearest value', [], 'Admin.Shopparameters.Feature'),
                ],
            ];
            $activities1 = [
                0 => $this->trans('-- Please choose your main activity --', [], 'Install'),
                2 => $this->trans('Animals and Pets', [], 'Install'),
                3 => $this->trans('Art and Culture', [], 'Install'),
                4 => $this->trans('Babies', [], 'Install'),
                5 => $this->trans('Beauty and Personal Care', [], 'Install'),
                6 => $this->trans('Cars', [], 'Install'),
                7 => $this->trans('Computer Hardware and Software', [], 'Install'),
                8 => $this->trans('Download', [], 'Install'),
                9 => $this->trans('Fashion and accessories', [], 'Install'),
                10 => $this->trans('Flowers, Gifts and Crafts', [], 'Install'),
                11 => $this->trans('Food and beverage', [], 'Install'),
                12 => $this->trans('HiFi, Photo and Video', [], 'Install'),
                13 => $this->trans('Home and Garden', [], 'Install'),
                14 => $this->trans('Home Appliances', [], 'Install'),
                15 => $this->trans('Jewelry', [], 'Install'),
                1 => $this->trans('Lingerie and Adult', [], 'Install'),
                16 => $this->trans('Mobile and Telecom', [], 'Install'),
                17 => $this->trans('Services', [], 'Install'),
                18 => $this->trans('Shoes and accessories', [], 'Install'),
                19 => $this->trans('Sport and Entertainment', [], 'Install'),
                20 => $this->trans('Travel', [], 'Install'),
            ];
            $activities2 = [];
            foreach ($activities1 as $value => $name) {
                $activities2[] = ['value' => $value, 'name' => $name];
            }

            $fields = [
                'PS_SSL_ENABLED' => [
                    'title' => $this->trans('Enable SSL', [], 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('If you own an SSL certificate for your shop\'s domain name, you can activate SSL encryption (https://) for customer account identification and order processing.', [], 'Admin.Shopparameters.Help'),
                    'hint' => $this->trans('If you want to enable SSL on all the pages of your shop, activate the "Enable on all the pages" option below.', [], 'Admin.Shopparameters.Help'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => '0',
                ],
            ];

            $fields['PS_SSL_ENABLED_EVERYWHERE'] = [
                'title' => $this->trans('Enable SSL on all pages', [], 'Admin.Shopparameters.Feature'),
                'desc' => $this->trans('When enabled, all the pages of your shop will be SSL-secured.', [], 'Admin.Shopparameters.Help'),
                'validation' => 'isBool',
                'cast' => 'intval',
                'type' => 'bool',
                'default' => '0',
                'disabled' => (Tools::getValue('PS_SSL_ENABLED', Configuration::get('PS_SSL_ENABLED'))) ? false : true,
            ];

            $fields = array_merge($fields, [
                'PS_TOKEN_ENABLE' => [
                    'title' => $this->trans('Increase front office security', [], 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('Enable or disable token in the Front Office to improve PrestaShop\'s security.', [], 'Admin.Shopparameters.Help'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => '0',
                    'visibility' => Shop::CONTEXT_ALL,
                ],
                'PS_ALLOW_HTML_IFRAME' => [
                    'title' => $this->trans('Allow iframes on HTML fields', [], 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('Allow iframes on text fields like product description. We recommend that you leave this option disabled.', [], 'Admin.Shopparameters.Help'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => '0',
                ],
                'PS_USE_HTMLPURIFIER' => [
                    'title' => $this->trans('Use HTMLPurifier Library', [], 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('Clean the HTML content on text fields. We recommend that you leave this option enabled.', [], 'Admin.Shopparameters.Help'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => '0',
                ],
                'PS_PRICE_ROUND_MODE' => [
                    'title' => $this->trans('Round mode', [], 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('You can choose among 6 different ways of rounding prices. "Round up away from zero ..." is the recommended behavior.', [], 'Admin.Shopparameters.Help'),
                    'validation' => 'isInt',
                    'cast' => 'intval',
                    'type' => 'select',
                    'list' => $round_mode,
                    'identifier' => 'value',
                ],
                'PS_ROUND_TYPE' => [
                    'title' => $this->trans('Round type', [], 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('You can choose when to round prices: either on each item, each line or the total (of an invoice, for example).', [], 'Admin.Shopparameters.Help'),
                    'cast' => 'intval',
                    'type' => 'select',
                    'list' => [
                        [
                            'name' => $this->trans('Round on each item', [], 'Admin.Shopparameters.Feature'),
                            'id' => Order::ROUND_ITEM,
                        ],
                        [
                            'name' => $this->trans('Round on each line', [], 'Admin.Shopparameters.Feature'),
                            'id' => Order::ROUND_LINE,
                        ],
                        [
                            'name' => $this->trans('Round on the total', [], 'Admin.Shopparameters.Feature'),
                            'id' => Order::ROUND_TOTAL,
                        ],
                    ],
                    'identifier' => 'id',
                ],
                'PS_PRICE_DISPLAY_PRECISION' => [
                    'title' => $this->trans('Number of decimals', [], 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('Choose how many decimals you want to display', [], 'Admin.Shopparameters.Help'),
                    'validation' => 'isUnsignedInt',
                    'cast' => 'intval',
                    'type' => 'text',
                    'class' => 'fixed-width-xxl',
                ],
                'PS_DISPLAY_SUPPLIERS' => [
                    'title' => $this->trans('Display brands and suppliers', [], 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('Enable brands and suppliers pages on your front office even when their respective modules are disabled.', [], 'Admin.Shopparameters.Help'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                ],
                'PS_DISPLAY_BEST_SELLERS' => [
                    'title' => $this->trans('Display best sellers', [], 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('Enable best sellers page on your front office even when its respective module is disabled.', [], 'Admin.Shopparameters.Help'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                ],
                'PS_MULTISHOP_FEATURE_ACTIVE' => [
                    'title' => $this->trans('Enable Multistore', [], 'Admin.Shopparameters.Feature'),
                    'desc' => $this->trans('The multistore feature allows you to manage several e-shops with one Back Office. If this feature is enabled, a "Multistore" page will be available in the "Advanced Parameters" menu.', [], 'Admin.Shopparameters.Help'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'visibility' => Shop::CONTEXT_ALL,
                ],
                'PS_SHOP_ACTIVITY' => [
                    'title' => $this->trans('Main Shop Activity', [], 'Admin.Shopparameters.Feature'),
                    'validation' => 'isInt',
                    'cast' => 'intval',
                    'type' => 'select',
                    'list' => $activities2,
                    'identifier' => 'value',
                ],
            ]);

            // No HTTPS activation if you haven't already.
            if (!Tools::usingSecureMode() && !Configuration::get('PS_SSL_ENABLED')) {
                $requestUri = '';
                if (array_key_exists('REQUEST_URI', $_SERVER)) {
                    $requestUri = $_SERVER['REQUEST_URI'];
                }

                $fields['PS_SSL_ENABLED']['type'] = 'disabled';
                $fields['PS_SSL_ENABLED']['disabled'] = '<a class="btn btn-link" href="https://' .
                    Tools::getShopDomainSsl() .
                    Tools::safeOutput($requestUri) . '">' .
                    $this->trans('Please click here to check if your shop supports HTTPS.', [], 'Admin.Shopparameters.Feature') . '</a>';
            }

            $this->fields_options = [
                'general' => [
                    'title' => $this->trans('General', [], 'Admin.Global'),
                    'icon' => 'icon-cogs',
                    'fields' => $fields,
                    'submit' => ['title' => $this->trans('Save', [], 'Admin.Actions')],
                ],
            ];
        }
    }

    /**
     * Enable / disable multishop menu if multishop feature is activated.
     *
     * @param string $value
     */
    public function updateOptionPsMultishopFeatureActive($value)
    {
        Configuration::updateValue('PS_MULTISHOP_FEATURE_ACTIVE', $value);

        $tab = Tab::getInstanceFromClassName('AdminShopGroup');
        if (Validate::isLoadedObject($tab)) {
            $tab->active = (bool) Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE');
            $tab->update();
        }
    }
}
