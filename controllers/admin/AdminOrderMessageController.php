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
 * @property OrderMessage $object
 */
class AdminOrderMessageControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'order_message';
        $this->className = 'OrderMessage';
        $this->lang = true;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        parent::__construct();

        if (!Tools::getValue('realedit')) {
            $this->deleted = false;
        }

        parent::__construct();

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ],
        ];

        $this->fields_list = [
            'id_order_message' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
            ],
            'name' => [
                'title' => $this->trans('Name', [], 'Admin.Global'),
            ],
            'message' => [
                'title' => $this->trans('Message', [], 'Admin.Global'),
                'maxlength' => 300,
            ],
        ];

        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Order messages', [], 'Admin.Orderscustomers.Feature'),
                'icon' => 'icon-mail',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'lang' => true,
                    'label' => $this->trans('Name', [], 'Admin.Global'),
                    'name' => 'name',
                    'size' => 53,
                    'required' => true,
                ],
                [
                    'type' => 'textarea',
                    'lang' => true,
                    'label' => $this->trans('Message', [], 'Admin.Global'),
                    'name' => 'message',
                    'required' => true,
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
        ];
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_order_message'] = [
                'href' => self::$currentIndex . '&addorder_message&token=' . $this->token,
                'desc' => $this->trans('Add new order message', [], 'Admin.Orderscustomers.Feature'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }
}
