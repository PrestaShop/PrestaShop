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
class AdminCustomerPreferencesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'Configuration';
        $this->table = 'configuration';

        parent::__construct();

        $this->fields_options = array(
            'general' => array(
                'title' =>    $this->trans('General', array(), 'Admin.Global'),
                'icon' =>    'icon-cogs',
                'fields' =>    array(
                    'PS_CART_FOLLOWING' => array(
                        'title' => $this->trans('Re-display cart at login', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('After a customer logs in, you can recall and display the content of his/her last shopping cart.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_CUSTOMER_CREATION_EMAIL' => array(
                        'title' => $this->trans('Send an email after registration', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Send an email with summary of the account information (email, password) after registration.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_PASSWD_TIME_FRONT' => array(
                        'title' => $this->trans('Password reset delay', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Minimum time required between two requests for a password reset.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isUnsignedInt',
                        'cast' => 'intval',
                        'size' => 5,
                        'type' => 'text',
                        'suffix' => $this->trans('minutes', array(), 'Admin.Shopparameters.Feature')
                    ),
                    'PS_B2B_ENABLE' => array(
                        'title' => $this->trans('Enable B2B mode', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Activate or deactivate B2B mode. When this option is enabled, B2B features will be made available.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_CUSTOMER_BIRTHDATE' => array(
                        'title' => $this->trans('Ask for birth date', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Display or not the birth date field.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_CUSTOMER_OPTIN' => array(
                        'title' => $this->trans('Enable partner offers', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Display or not the partner offers tick box, to receive offers from the store\'s partners.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions')),
            ),
        );
    }

    /**
     * Update PS_B2B_ENABLE and enables / disables the associated tabs
     * @param $value integer Value of option
     */
    public function updateOptionPsB2bEnable($value)
    {
        $value = (int)$value;

        $tabs_class_name = array('AdminOutstanding');
        if (!empty($tabs_class_name)) {
            foreach ($tabs_class_name as $tab_class_name) {
                $tab = Tab::getInstanceFromClassName($tab_class_name);
                if (Validate::isLoadedObject($tab)) {
                    $tab->active = $value;
                    $tab->save();
                }
            }
        }
        Configuration::updateValue('PS_B2B_ENABLE', $value);
    }
}
