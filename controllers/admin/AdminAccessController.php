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
 * @property Profile $object
 */
class AdminAccessControllerCore extends AdminController
{
    /* @var array : Black list of id_tab that do not have access */
    public $accesses_black_list = array();

    public function __construct()
    {
        $this->bootstrap = true;
        $this->show_toolbar = false;
        $this->table = 'access';
        $this->className = 'Profile';
        $this->multishop_context = Shop::CONTEXT_ALL;
        $this->lang = false;
        $this->context = Context::getContext();

        // Blacklist AdminLogin
        $this->accesses_black_list[] = Tab::getIdFromClassName('AdminLogin');

        parent::__construct();
    }

    /**
     * AdminController::renderForm() override
     * @see AdminController::renderForm()
     */
    public function renderForm()
    {
        $current_profile = (int)$this->getCurrentProfileId();
        $profiles = Profile::getProfiles($this->context->language->id);
        $tabs = Tab::getTabs($this->context->language->id);

        $accesses = array();
        foreach ($profiles as $profile) {
            $accesses[$profile['id_profile']] = Profile::getProfileAccesses($profile['id_profile']);
        }

        // Deleted id_tab that do not have access
        foreach ($tabs as $key => $tab) {
            // Don't allow permissions for unnamed tabs (ie. AdminLogin)
            if (empty($tab['name'])) {
                unset($tabs[$key]);
            }

            foreach ($this->accesses_black_list as $id_tab) {
                if ($tab['id_tab'] == (int)$id_tab) {
                    unset($tabs[$key]);
                }
            }
        }

        $modules = array();
        foreach ($profiles as $profile) {
            $modules[$profile['id_profile']] = Module::getModulesAccessesByIdProfile($profile['id_profile']);
            uasort($modules[$profile['id_profile']], array($this, 'sortModuleByName'));
        }

        $this->fields_form = array('');
        $this->tpl_form_vars = array(
            'profiles' => $profiles,
            'accesses' => $accesses,
            'id_tab_parentmodule' => (int)Tab::getIdFromClassName('AdminParentModules'),
            'id_tab_module' => (int)Tab::getIdFromClassName('AdminModules'),
            'tabs' => $this->displayTabs($tabs),
            'current_profile' => (int)$current_profile,
            'admin_profile' => (int)_PS_ADMIN_PROFILE_,
            'access_edit' => $this->access('edit'),
            'perms' => array('view', 'add', 'edit', 'delete'),
            'id_perms' => array('view' => 0, 'add' => 1, 'edit' => 2, 'delete' => 3, 'all' => 4),
            'modules' => $modules,
            'link' => $this->context->link,
            'employee_profile_id' => (int) $this->context->employee->id_profile,
        );

        return parent::renderForm();
    }

    /**
     * AdminController::initContent() override
     * @see AdminController::initContent()
     */
    public function initContent()
    {
        $this->display = 'edit';

        if (!$this->loadObject(true)) {
            return;
        }

        $this->content .= $this->renderForm();

        $this->context->smarty->assign(array(
            'content' => $this->content,
        ));
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = array_unique($this->breadcrumbs);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        unset($this->page_header_toolbar_btn['cancel']);
    }

    public function ajaxProcessUpdateAccess()
    {
        if (_PS_MODE_DEMO_) {
            throw new PrestaShopException($this->trans('This functionality has been disabled.', array(), 'Admin.Notifications.Error'));
        }
        if ($this->access('edit') != '1') {
            throw new PrestaShopException($this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error'));
        }

        if (Tools::isSubmit('submitAddAccess')) {
            $access = new Access;
            $perm = Tools::getValue('perm');
            if (!in_array($perm, array('view', 'add', 'edit', 'delete', 'all'))) {
                throw new PrestaShopException('permission does not exist');
            }

            $enabled = (int)Tools::getValue('enabled');
            $id_tab = (int)Tools::getValue('id_tab');
            $id_profile = (int)Tools::getValue('id_profile');
            $addFromParent = (int)Tools::getValue('addFromParent');

            die($access->updateLgcAccess((int)$id_profile, $id_tab, $perm, $enabled, $addFromParent));
        }
    }

    public function ajaxProcessUpdateModuleAccess()
    {
        if (_PS_MODE_DEMO_) {
            throw new PrestaShopException($this->trans('This functionality has been disabled.', array(), 'Admin.Notifications.Error'));
        }
        if ($this->access('edit') != '1') {
            throw new PrestaShopException($this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error'));
        }

        if (Tools::isSubmit('changeModuleAccess')) {
            $access = new Access;
            $perm = Tools::getValue('perm');
            $enabled = (int)Tools::getValue('enabled');
            $id_module = (int)Tools::getValue('id_module');
            $id_profile = (int)Tools::getValue('id_profile');

            if (!in_array($perm, array('view', 'configure', 'uninstall'))) {
                throw new PrestaShopException('permission does not exist');
            }

            die($access->updateLgcModuleAccess((int)$id_profile, $id_module, $perm, $enabled));
        }
    }

    /**
    * Get the current profile id
    *
    * @return int the $_GET['profile'] if valid, else 1 (the first profile id)
    */
    public function getCurrentProfileId()
    {
        return (isset($_GET['id_profile']) && !empty($_GET['id_profile']) && is_numeric($_GET['id_profile'])) ? (int)$_GET['id_profile'] : 1;
    }

    private function sortModuleByName($a, $b)
    {
        return strnatcmp($a['name'], $b['name']);
    }

    /**
     * return human readable Tabs hierarchy for display
     *
     */
    private function displayTabs(array $tabs)
    {
        $tabsTree = $this->getChildrenTab($tabs);

        return $tabsTree;
    }

    private function getChildrenTab(array &$tabs, $id_parent = 0)
    {
        $children = [];
        foreach ($tabs as &$tab) {
            $id = $tab['id_tab'];

            if ($tab['id_parent'] == $id_parent) {
                $children[$id] = $tab;
                $children[$id]['children'] = $this->getChildrenTab($tabs, $id);
            }
        }
        return $children;
    }
}
