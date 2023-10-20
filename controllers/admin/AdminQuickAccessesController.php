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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * @property QuickAccess $object
 */
class AdminQuickAccessesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'quick_access';
        $this->className = 'QuickAccess';
        $this->lang = true;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        parent::__construct();

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
            'id_quick_access' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->trans('Name', [], 'Admin.Global'),
            ],
            'link' => [
                'title' => $this->trans('Link', [], 'Admin.Navigation.Header'),
            ],
            'new_window' => [
                'title' => $this->trans('New window', [], 'Admin.Navigation.Header'),
                'align' => 'center',
                'type' => 'bool',
                'active' => 'new_window',
                'class' => 'fixed-width-sm',
            ],
        ];

        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Quick Access menu', [], 'Admin.Navigation.Header'),
                'icon' => 'icon-align-justify',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Name', [], 'Admin.Global'),
                    'name' => 'name',
                    'lang' => true,
                    'maxlength' => 32,
                    'required' => true,
                    'hint' => $this->trans('Forbidden characters:', [], 'Admin.Notifications.Info') . ' &lt;&gt;;=#{}',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('URL', [], 'Admin.Global'),
                    'name' => 'link',
                    'maxlength' => 128,
                    'required' => true,
                    'hint' => $this->trans('If it\'s a URL that comes from your back office, you MUST remove the security token.', [], 'Admin.Navigation.Header'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Open in new window', [], 'Admin.Navigation.Header'),
                    'name' => 'new_window',
                    'required' => false,
                    'values' => [
                        [
                            'id' => 'new_window_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'new_window_off',
                            'value' => 0,
                            'label' => $this->trans('No', [], 'Admin.Global'),
                        ],
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
        ];
    }

    public function getTabSlug()
    {
        return 'ROLE_MOD_TAB_ADMINACCESS_';
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_quick_access'] = [
                'href' => self::$currentIndex . '&addquick_access&token=' . $this->token,
                'desc' => $this->trans('Add new quick access', [], 'Admin.Navigation.Header'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    public function initProcess()
    {
        if ((isset($_GET['new_window' . $this->table]) || isset($_GET['new_window'])) && Tools::getValue($this->identifier)) {
            if ($this->access('edit')) {
                $this->action = 'newWindow';
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
            }
        }

        parent::initProcess();
    }

    public function getQuickAccessesList()
    {
        $links = QuickAccess::getQuickAccessesWithToken($this->context->language->id, (int) $this->context->employee->id);

        return json_encode($links);
    }

    public function addQuickLink()
    {
        if (empty($this->className)) {
            return false;
        }
        $this->validateRules();

        if (count($this->errors) <= 0) {
            $this->object = new $this->className();
            $this->copyFromPost($this->object, $this->table);
            $exists = Db::getInstance()->getValue('SELECT id_quick_access FROM ' . _DB_PREFIX_ . 'quick_access WHERE link = "' . pSQL($this->object->link) . '"');
            if ($exists) {
                return true;
            }
            $this->beforeAdd($this->object);

            if (method_exists($this->object, 'add') && !$this->object->add()) {
                $this->errors[] = $this->trans('An error occurred while creating an object.', [], 'Admin.Notifications.Error') .
                    ' <b>' . $this->table . ' (' . Db::getInstance()->getMsgError() . ')</b>';
            } elseif (($_POST[$this->identifier] = $this->object->id) && $this->postImage($this->object->id) && empty($this->errors) && $this->_redirect) {
                // voluntary do affectation here
                PrestaShopLogger::addLog($this->trans('%class_name% addition', ['%class_name%' => $this->className], 'Admin.Advparameters.Feature'), 1, null, $this->className, (int) $this->object->id, true, (int) $this->context->employee->id);
                $this->afterAdd($this->object);
            }
        }

        $this->errors = array_unique($this->errors);
        if (!empty($this->errors)) {
            $this->errors['has_errors'] = true;
            $this->ajaxRender(json_encode($this->errors));

            return false;
        }

        return $this->getQuickAccessesList();
    }

    public function processDelete()
    {
        parent::processDelete();

        return $this->getQuickAccessesList();
    }

    public function ajaxProcessGetUrl()
    {
        if (Tools::strtolower(Tools::getValue('method')) === 'add') {
            $params['new_window'] = 0;
            $params['name_' . (int) Configuration::get('PS_LANG_DEFAULT')] = Tools::getValue('name');
            $params['link'] = Tools::getValue('url');
            $params['submitAddquick_access'] = 1;
            unset($_POST['name']);
            $_POST = array_merge($_POST, $params);
            die($this->addQuickLink());
        } elseif (Tools::strtolower(Tools::getValue('method')) === 'remove') {
            $params['deletequick_access'] = 1;
            $_POST = array_merge($_POST, $params);
            die($this->processDelete());
        }
    }

    public function processNewWindow()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            /** @var QuickAccess $object */
            if ($object->toggleNewWindow()) {
                $this->redirect_after = self::$currentIndex . '&conf=5&token=' . $this->token;
            } else {
                $this->errors[] = $this->trans('An error occurred while updating new window property.', [], 'Admin.Navigation.Notification');
            }
        } else {
            $this->errors[] = $this->trans('An error occurred while updating the new window property for this object.', [], 'Admin.Navigation.Notification') .
                ' <b>' . $this->table . '</b> ' .
                $this->trans('(cannot load object)', [], 'Admin.Notifications.Error');
        }

        return $object;
    }
}
