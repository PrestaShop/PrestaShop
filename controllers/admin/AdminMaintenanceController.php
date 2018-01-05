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

/**
 * @property Configuration $object
 */
class AdminMaintenanceControllerCore extends AdminController
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
                'fields' =>    array(
                    'PS_SHOP_ENABLE' => array(
                        'title' => $this->trans('Enable Shop', array(), 'Admin.Shopparameters.Feature'),
                        'desc' => $this->trans('Activate or deactivate your shop (It is a good idea to deactivate your shop while you perform maintenance. Please note that the webservice will not be disabled).', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_MAINTENANCE_IP' => array(
                        'title' => $this->trans('Maintenance IP', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('IP addresses allowed to access the front office even if the shop is disabled. Please use a comma to separate them (e.g. 42.24.4.2,127.0.0.1,99.98.97.96)', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isGenericName',
                        'type' => 'maintenance_ip',
                        'default' => ''
                    ),
                    'PS_MAINTENANCE_TEXT' => array(
                        'title' => $this->trans('Custom maintenance text', array(), 'Admin.Shopparameters.Feature'),
                        'hint' => $this->trans('Custom text displayed on maintenance page while shop is deactivated.', array(), 'Admin.Shopparameters.Help'),
                        'validation' => 'isCleanHtml',
                        'type' => 'textareaLang',
                        'autoload_rte' => true,
                        'default' => ''
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            ),
        );
    }
}
