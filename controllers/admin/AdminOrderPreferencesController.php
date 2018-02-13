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
class AdminOrderPreferencesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'Configuration';
        $this->table = 'configuration';

        parent::__construct();

        // List of CMS tabs
        $cms_tab = array(0 => array(
            'id' => 0,
            'name' => $this->trans('None', array(), 'Admin.Global')
        ));
        foreach (CMS::listCms($this->context->language->id) as $cms_file) {
            $cms_tab[] = array('id' => $cms_file['id_cms'], 'name' => $cms_file['meta_title']);
        }

        $this->fields_options = array(
            'general' => array(
                'title' =>    $this->trans('General', array(), 'Admin.Global'),
                'icon' =>    'icon-cogs',
                'fields' =>    array(
                    'PS_FINAL_SUMMARY_ENABLED' => array(
                        'title' => $this->trans('Enable final summary', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Display an overview of the addresses, shipping method and cart just before the order button (required in some European countries).', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_GUEST_CHECKOUT_ENABLED' => array(
                        'title' => $this->trans('Enable guest checkout', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Allow guest visitors to place an order without registering.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_DISALLOW_HISTORY_REORDERING' => array(
                        'title' => $this->trans('Disable Reordering Option', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Disable the option to allow customers to reorder in one click from the order history page (required in some European countries).', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_PURCHASE_MINIMUM' => array(
                        'title' => $this->trans('Minimum purchase total required in order to validate the order', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Set to 0 to disable this feature.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isFloat',
                        'cast' => 'floatval',
                        'type' => 'price'
                    ),
                    'PS_ORDER_RECALCULATE_SHIPPING' => array(
                        'title' => $this->trans('Recalculate shipping costs after editing the order', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Automatically updates the shipping costs when you edit an order.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                     ),
                    'PS_ALLOW_MULTISHIPPING' => array(
                        'title' => $this->trans('Allow multishipping', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Allow the customer to ship orders to multiple addresses. This option will convert the customer\'s cart into one or more orders.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_SHIP_WHEN_AVAILABLE' => array(
                        'title' => $this->trans('Delayed shipping', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Allows you to delay shipping at your customers\' request.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_CONDITIONS' => array(
                        'title' => $this->trans('Terms of service', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Require customers to accept or decline terms of service before processing an order.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                        'js' => array(
                            'on' => 'onchange="changeCMSActivationAuthorization()"',
                            'off' => 'onchange="changeCMSActivationAuthorization()"'
                        )
                    ),
                    'PS_CONDITIONS_CMS_ID' => array(
                        'title' => $this->trans('Page for the Terms and conditions', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Choose the page which contains your store\'s terms and conditions of use.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isInt',
                        'type' => 'select',
                        'list' => $cms_tab,
                        'identifier' => 'id',
                        'cast' => 'intval'
                    )
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            ),
            'gift' => array(
                'title' =>    $this->trans('Gift options', array(), 'Admin.Shopparameters.Feature'),
                'icon' =>    'icon-gift',
                'fields' =>    array(
                    'PS_GIFT_WRAPPING' => array(
                        'title' => $this->trans('Offer gift wrapping', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Suggest gift-wrapping to customers.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_GIFT_WRAPPING_PRICE' => array(
                        'title' => $this->trans('Gift-wrapping price', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Set a price for gift wrapping.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isPrice',
                        'cast' => 'floatval',
                        'type' => 'price'
                    ),
                    'PS_GIFT_WRAPPING_TAX_RULES_GROUP' => array(
                        'title' => $this->trans('Gift-wrapping tax', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Set a tax for gift wrapping.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isInt',
                        'cast' => 'intval',
                        'type' => 'select',
                        'list' => array_merge(array(array('id_tax_rules_group' => 0, 'name' => $this->trans('None'))), TaxRulesGroup::getTaxRulesGroups(true)),
                        'identifier' => 'id_tax_rules_group'
                    ),
                    'PS_RECYCLABLE_PACK' => array(
                        'title' => $this->trans('Offer recycled packaging', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Suggest recycled packaging to customer.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions')),
            ),
        );

        if (!Configuration::get('PS_ALLOW_MULTISHIPPING')) {
            unset($this->fields_options['general']['fields']['PS_ALLOW_MULTISHIPPING']);
        }

        if (Configuration::get('PS_ATCP_SHIPWRAP')) {
            unset($this->fields_options['gift']['fields']['PS_GIFT_WRAPPING_TAX_RULES_GROUP']);
        }
    }

    /**
     * This method is called before we start to update options configuration
     */
    public function beforeUpdateOptions()
    {
        $sql = 'SELECT `id_cms` FROM `'._DB_PREFIX_.'cms`
				WHERE id_cms = '.(int)Tools::getValue('PS_CONDITIONS_CMS_ID');
        if (Tools::getValue('PS_CONDITIONS') && (Tools::getValue('PS_CONDITIONS_CMS_ID') == 0 || !Db::getInstance()->getValue($sql))) {
            $this->errors[] = $this->trans('Assign a valid page if you want it to be read.', array(), 'Admin.Shopparameters.Notification');
        }
    }
}
