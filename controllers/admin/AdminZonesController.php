<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * @property Zone $object
 */
class AdminZonesControllerCore extends AdminController
{
    public $asso_type = 'shop';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'zone';
        $this->className = 'Zone';
        $this->lang = false;

        parent::__construct();

        $this->fields_list = [
            'id_zone' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->trans('Zone', [], 'Admin.Global'),
            ],
            'active' => [
                'title' => $this->trans('Enabled', [], 'Admin.Global'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'class' => 'fixed-width-sm',
            ],
        ];
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ],
        ];
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_zone'] = [
                'href' => self::$currentIndex . '&addzone&token=' . $this->token,
                'desc' => $this->trans('Add new zone', [], 'Admin.International.Feature'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderForm()
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Zones', [], 'Admin.International.Feature'),
                'icon' => 'icon-globe',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Name', [], 'Admin.Global'),
                    'name' => 'name',
                    'required' => true,
                    'hint' => $this->trans('Zone name (e.g. Africa, West Coast, Neighboring Countries).', [], 'Admin.International.Help'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Active', [], 'Admin.Global'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', [], 'Admin.Global'),
                        ],
                    ],
                    'hint' => $this->trans('Allow or disallow shipping to this zone.', [], 'Admin.International.Help'),
                ],
            ],
        ];

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->trans('Shop association', [], 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            ];
        }

        $this->fields_form['submit'] = [
            'title' => $this->trans('Save', [], 'Admin.Actions'),
        ];

        return parent::renderForm();
    }

    /**
     * Get all zones displayed in a select input.
     */
    public function displayAjaxZones()
    {
        $this->context->smarty->assign([
            'zones' => Zone::getZones(),
        ]);

        $array = [
            'hasError' => false,
            'errors' => '',
            'data' => $this->context->smarty->fetch('controllers/zones/select.tpl'),
        ];
        $this->ajaxRender(json_encode($array));
    }
}
