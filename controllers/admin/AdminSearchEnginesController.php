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
 * @property SearchEngine $object
 */
class AdminSearchEnginesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'search_engine';
        $this->className = 'SearchEngine';
        $this->lang = false;

        parent::__construct();

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        if (!Tools::getValue('realedit')) {
            $this->deleted = false;
        }

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ],
        ];

        $this->fields_list = [
            'id_search_engine' => ['title' => $this->trans('ID', [], 'Admin.Global'), 'width' => 25],
            'server' => ['title' => $this->trans('Server', [], 'Admin.Shopparameters.Feature')],
            'getvar' => ['title' => $this->trans('GET variable', [], 'Admin.Shopparameters.Feature'), 'width' => 100],
        ];

        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Referrer', [], 'Admin.Shopparameters.Feature'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Server', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'server',
                    'size' => 20,
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('$_GET variable', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'getvar',
                    'size' => 40,
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
            $this->page_header_toolbar_btn['new_search_engine'] = [
                'href' => self::$currentIndex . '&addsearch_engine&token=' . $this->token,
                'desc' => $this->trans('Add new search engine', [], 'Admin.Shopparameters.Feature'),
                'icon' => 'process-icon-new',
            ];
        }

        $this->identifier_name = 'server';

        parent::initPageHeaderToolbar();
    }
}
