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
 * @property PrestaShopLogger $object
 */
class AdminLogsControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'log';
        $this->className = 'PrestaShopLogger';
        $this->lang = false;
        $this->noLink = true;

        parent::__construct();

        $this->fields_list = array(
            'id_log' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'employee' => array(
                'title' => $this->trans('Employee', array(), 'Admin.Global'),
                'havingFilter' => true,
                'callback' => 'displayEmployee',
                'callback_object' => $this
            ),
            'severity' => array(
                'title' => $this->trans('Severity (1-4)', array(), 'Admin.Advparameters.Feature'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'message' => array(
                'title' => $this->trans('Message', array(), 'Admin.Global')
            ),
            'object_type' => array(
                'title' => $this->trans('Object type', array(), 'Admin.Advparameters.Feature'),
                'class' => 'fixed-width-sm'
            ),
            'object_id' => array(
                'title' => $this->trans('Object ID', array(), 'Admin.Advparameters.Feature'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'error_code' => array(
                'title' => $this->trans('Error code', array(), 'Admin.Advparameters.Feature'),
                'align' => 'center',
                'prefix' => '0x',
                'class' => 'fixed-width-xs'
            ),
            'date_add' => array(
                'title' => $this->trans('Date', array(), 'Admin.Global'),
                'align' => 'right',
                'type' => 'datetime'
            )
        );

        $this->fields_options = array(
            'general' => array(
                'title' =>    $this->trans('Logs by email', array(), 'Admin.Advparameters.Feature'),
                'icon' => 'icon-envelope',
                'fields' =>    array(
                    'PS_LOGS_BY_EMAIL' => array(
                        'title' => $this->trans('Minimum severity level', array(), 'Admin.Advparameters.Feature'),
                        'hint' => Tools::safeOutput(
                            $this->trans('Enter "5" if you do not want to receive any emails.', array(), 'Admin.Advparameters.Help').
                            '<br>'.
                            $this->trans('Emails will be sent to the shop owner.', array(), 'Admin.Advparameters.Help'),
                            true
                        ),
                        'cast' => 'intval',
                        'type' => 'text',
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions')),
            ),
        );
        $this->list_no_link = true;
        $this->_select .= 'CONCAT(LEFT(e.firstname, 1), \'. \', e.lastname) employee';
        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'employee e ON (a.id_employee = e.id_employee)';
        $this->_use_found_rows = false;
    }

    public function processDelete()
    {
        if (PrestaShopLogger::eraseAllLogs()) {
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminLogs'));
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->toolbar_btn['delete'] = array(
            'short' => 'Erase',
            'desc' => $this->trans('Erase all', array(), 'Admin.Advparameters.Feature'),
            'js' => 'if (confirm(\''.$this->trans('Are you sure?', array(), 'Admin.Notifications.Warning').'\')) document.location = \''.Tools::safeOutput($this->context->link->getAdminLink('AdminLogs')).'&amp;token='.$this->token.'&amp;deletelog=1\';'
        );
        unset($this->toolbar_btn['new']);
    }

    public function displayEmployee($value, $tr)
    {
        $template = $this->context->smarty->createTemplate('controllers/logs/employee_field.tpl', $this->context->smarty);
        $employee = new Employee((int)$tr['id_employee']);
        $template->assign(array(
            'employee_image' => $employee->getImage(),
            'employee_name' => $value
        ));
        return $template->fetch();
    }
}
