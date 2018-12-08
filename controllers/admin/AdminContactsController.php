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
 * @property Contact $object
 */
class AdminContactsControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'contact';
        $this->className = 'Contact';
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        parent::__construct();

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ],
        ];

        $this->fields_list = [
            'id_contact' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->trans('Title', [], 'Admin.Global'),
                'maxlength' => 30,
            ],
            'email' => [
                'title' => $this->trans('Email address', [], 'Admin.Global'),
                'maxlength' => 50,
            ],
            'description' => [
                'title' => $this->trans('Description', [], 'Admin.Global'),
            ],
        ];
    }

    public function renderForm()
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Contacts', [], 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-envelope-alt',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Title', [], 'Admin.Global'),
                    'name' => 'name',
                    'required' => true,
                    'lang' => true,
                    'col' => 4,
                    'hint' => $this->trans('Contact name (e.g. Customer Support).', [], 'Admin.Shopparameters.Help'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Email address', [], 'Admin.Global'),
                    'name' => 'email',
                    'required' => false,
                    'col' => 4,
                    'hint' => $this->trans('Emails will be sent to this address.', [], 'Admin.Shopparameters.Help'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Save messages?', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'customer_service',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'hint' => $this->trans('If enabled, all messages will be saved in the "Customer Service" page under the "Customer" menu.', [], 'Admin.Shopparameters.Help'),
                    'values' => [
                        [
                            'id' => 'customer_service_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'customer_service_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', [], 'Admin.Global'),
                        ],
                    ],
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->trans('Description', [], 'Admin.Global'),
                    'name' => 'description',
                    'required' => false,
                    'lang' => true,
                    'col' => 6,
                    'hint' => $this->trans('Further information regarding this contact.', [], 'Admin.Shopparameters.Help'),
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
        ];

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->trans('Shop association', [], 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            ];
        }

        return parent::renderForm();
    }

    public function initPageHeaderToolbar()
    {
        $this->initToolbar();
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_contact'] = [
                'href' => self::$currentIndex . '&addcontact&token=' . $this->token,
                'desc' => $this->trans('Add new contact', [], 'Admin.Shopparameters.Feature'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }
}
