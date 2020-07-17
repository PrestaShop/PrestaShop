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
 * @property Tab $object
 */
class AdminTabsControllerCore extends AdminController
{
    protected $position_identifier = 'id_tab';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->multishop_context = Shop::CONTEXT_ALL;
        $this->table = 'tab';
        $this->list_id = 'tab';
        $this->className = 'Tab';
        $this->lang = true;

        parent::__construct();

        $this->fieldImageSettings = [
            'name' => 'icon',
            'dir' => 't',
        ];
        $this->imageType = 'gif';
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Actions'),
                'icon' => 'icon-trash',
            ],
        ];
        $this->fields_list = [
            'id_tab' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->trans('Name', [], 'Admin.Global'),
            ],
            'class_name' => [
                'title' => $this->trans('Class', [], 'Admin.Global'),
            ],
            'module' => [
                'title' => $this->trans('Module', [], 'Admin.Global'),
            ],
            'active' => [
                'title' => $this->trans('Enabled', [], 'Admin.Global'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
            ],
            'position' => [
                'title' => $this->trans('Position', [], 'Admin.Global'),
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center',
                'class' => 'fixed-width-md',
            ],
        ];
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_title = $this->trans('Menus', [], 'Admin.Global');

        if ($this->display == 'details') {
            $this->page_header_toolbar_btn['back_to_list'] = [
                'href' => Context::getContext()->link->getAdminLink('AdminTabs'),
                'desc' => $this->trans('Back to list', [], 'Admin.Actions'),
                'icon' => 'process-icon-back',
            ];
        } elseif (empty($this->display)) {
            $this->page_header_toolbar_btn['new_menu'] = [
                'href' => self::$currentIndex . '&addtab&token=' . $this->token,
                'desc' => $this->trans('Add new menu', [], 'Admin.Actions'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    /**
     * AdminController::renderForm() override.
     *
     * @see AdminController::renderForm()
     */
    public function renderForm()
    {
        $tabs = Tab::getTabs($this->context->language->id, 0);
        // If editing, we clean itself
        if (Tools::isSubmit('id_tab')) {
            foreach ($tabs as $key => $tab) {
                if ($tab['id_tab'] == Tools::getValue('id_tab')) {
                    unset($tabs[$key]);
                }
            }
        }

        // added category "Home" in var $tabs
        $tab_zero = [
            'id_tab' => 0,
            'name' => $this->trans('Home', [], 'Admin.Global'),
        ];
        array_unshift($tabs, $tab_zero);

        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Menus', [], 'Admin.Global'),
                'icon' => 'icon-list-ul',
            ],
            'input' => [
                [
                    'type' => 'hidden',
                    'name' => 'position',
                    'required' => false,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Name', [], 'Admin.Global'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->trans('Invalid characters:', [], 'Admin.Notifications.Info') . ' &lt;&gt;;=#{}',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Class', [], 'Admin.Global'),
                    'name' => 'class_name',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Module', [], 'Admin.Global'),
                    'name' => 'module',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Status', [], 'Admin.Global'),
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
                    'hint' => $this->trans('Show or hide menu.', [], 'Admin.Actions'),
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
        ];

        $display_parent = true;
        if (Validate::isLoadedObject($this->object) && !class_exists($this->object->class_name . 'Controller')) {
            $display_parent = false;
        }

        if ($display_parent) {
            $this->fields_form['input'][] = [
                'type' => 'select',
                'label' => $this->trans('Parent', [], 'Admin.Global'),
                'name' => 'id_parent',
                'options' => [
                    'query' => $tabs,
                    'id' => 'id_tab',
                    'name' => 'name',
                ],
            ];
        }

        return parent::renderForm();
    }

    /**
     * AdminController::renderList() override.
     *
     * @see AdminController::renderList()
     */
    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('details');
        $this->addRowAction('delete');

        $this->_where = 'AND a.`id_parent` = 0';
        $this->_orderBy = 'position';

        return parent::renderList();
    }

    public function initProcess()
    {
        if (Tools::getIsset('details' . $this->table)) {
            $this->list_id = 'details';

            if (isset($_POST['submitReset' . $this->list_id])) {
                $this->processResetFilters();
            }
        } else {
            $this->list_id = 'tab';
        }

        return parent::initProcess();
    }

    public function renderDetails()
    {
        if (($id = Tools::getValue('id_tab'))) {
            $this->lang = false;
            $this->list_id = 'details';
            $this->addRowAction('edit');
            $this->addRowAction('delete');
            $this->toolbar_btn = [];

            /** @var Tab $tab */
            $tab = $this->loadObject($id);
            $this->toolbar_title = $tab->name[$this->context->employee->id_lang];

            $this->_select = 'b.*';
            $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'tab_lang` b ON (b.`id_tab` = a.`id_tab` AND b.`id_lang` = ' .
                (int) $this->context->language->id . ')';
            $this->_where = 'AND a.`id_parent` = ' . (int) $id;
            $this->_orderBy = 'position';
            $this->_use_found_rows = false;

            self::$currentIndex = self::$currentIndex . '&details' . $this->table;
            $this->processFilter();

            return parent::renderList();
        }
    }

    public function postProcess()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            $this->errors[] = $this->trans('This functionality has been disabled.', [], 'Admin.Notifications.Error');

            return;
        }
        /* PrestaShop demo mode*/

        if (($id_tab = (int) Tools::getValue('id_tab')) && ($direction = Tools::getValue('move')) && Validate::isLoadedObject($tab = new Tab($id_tab))) {
            if ($tab->move($direction)) {
                Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
            }
        } elseif (Tools::getValue('position') && !Tools::isSubmit('submitAdd' . $this->table)) {
            if ($this->access('edit') !== '1') {
                $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
            } elseif (!Validate::isLoadedObject($object = new Tab((int) Tools::getValue($this->identifier)))) {
                $this->errors[] = $this->trans('An error occurred while updating the status for an object.', [], 'Admin.Notifications.Error') .
                    ' <b>' . $this->table . '</b> ' . $this->trans('(cannot load object)', [], 'Admin.Notifications.Error');
            }
            if (!$object->updatePosition((int) Tools::getValue('way'), (int) Tools::getValue('position'))) {
                $this->errors[] = $this->trans('Failed to update the position.', [], 'Admin.Notifications.Error');
            } else {
                Tools::redirectAdmin(self::$currentIndex . '&conf=5&token=' . Tools::getAdminTokenLite('AdminTabs'));
            }
        } elseif (Tools::isSubmit('submitAdd' . $this->table) && Tools::getValue('id_tab') === Tools::getValue('id_parent')) {
            $this->errors[] = $this->trans('You can\'t put this menu inside itself. ', [], 'Admin.Advparameters.Notification');
        } elseif (Tools::isSubmit('submitAdd' . $this->table) && $id_parent = (int) Tools::getValue('id_parent')) {
            $this->redirect_after = self::$currentIndex . '&id_' . $this->table . '=' . $id_parent . '&details' . $this->table . '&conf=4&token=' . $this->token;
        } elseif (isset($_GET['details' . $this->table]) && is_array($this->bulk_actions)) {
            $submit_bulk_actions = array_merge([
                'enableSelection' => [
                    'text' => $this->trans('Enable selection', [], 'Admin.Actions'),
                    'icon' => 'icon-power-off text-success',
                ],
                'disableSelection' => [
                    'text' => $this->trans('Disable selection', [], 'Admin.Actions'),
                    'icon' => 'icon-power-off text-danger',
                ],
            ], $this->bulk_actions);
            foreach ($submit_bulk_actions as $bulk_action => $params) {
                if (Tools::isSubmit('submitBulk' . $bulk_action . $this->table) || Tools::isSubmit('submitBulk' . $bulk_action)) {
                    if ($this->access('edit')) {
                        $this->action = 'bulk' . $bulk_action;
                        $this->boxes = Tools::getValue($this->list_id . 'Box');
                    } else {
                        $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
                    }

                    break;
                } elseif (Tools::isSubmit('submitBulk')) {
                    if ($this->access('edit')) {
                        $this->action = 'bulk' . Tools::getValue('select_submitBulk');
                        $this->boxes = Tools::getValue($this->list_id . 'Box');
                    } else {
                        $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
                    }

                    break;
                }
            }
        } else {
            // Temporary add the position depend of the selection of the parent category
            if (!Tools::isSubmit('id_tab')) { // @todo Review
                $_POST['position'] = Tab::getNbTabs(Tools::getValue('id_parent'));
            }
        }

        if (!count($this->errors)) {
            parent::postProcess();
        }
    }

    protected function afterImageUpload()
    {
        /** @var Tab $obj */
        if (!($obj = $this->loadObject(true))) {
            return;
        }
        @rename(_PS_IMG_DIR_ . 't/' . $obj->id . '.gif', _PS_IMG_DIR_ . 't/' . $obj->class_name . '.gif');
    }

    public function ajaxProcessUpdatePositions()
    {
        $way = (int) (Tools::getValue('way'));
        $id_tab = (int) (Tools::getValue('id'));
        $positions = Tools::getValue('tab');

        // when changing positions in a tab sub-list, the first array value is empty and needs to be removed
        if (!$positions[0]) {
            unset($positions[0]);
            // reset indexation from 0
            $positions = array_merge($positions);
        }

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int) $pos[2] === $id_tab) {
                if ($tab = new Tab((int) $pos[2])) {
                    if (isset($position) && $tab->updatePosition($way, $position)) {
                        echo 'ok position ' . (int) $position . ' for tab ' . (int) $pos[1] . '\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update tab ' . (int) $id_tab . ' to position ' . (int) $position . ' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This tab (' . (int) $id_tab . ') can t be loaded"}';
                }

                break;
            }
        }
    }
}
