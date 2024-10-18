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
 * @property Profile $object
 */
class AdminAccessControllerCore extends AdminController
{
    /** @var array : Black list of id_tab that do not have access */
    public $accesses_black_list = [];

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
     * Render the access form.
     */
    public function renderForm()
    {
        $current_profile = (int) $this->getCurrentProfileId();
        $profiles = Profile::getProfiles($this->context->language->id);
        $tabs = Tab::getTabs($this->context->language->id);

        $accesses = [];
        foreach ($profiles as $profile) {
            $accesses[$profile['id_profile']] = Profile::getProfileAccesses($profile['id_profile']);
        }

        // Remove tabs without names and those in the blacklist
        foreach ($tabs as $key => $tab) {
            if (empty($tab['name']) || in_array($tab['id_tab'], $this->accesses_black_list)) {
                unset($tabs[$key]);
            }
        }

        // Get module access for each profile
        $modules = [];
        foreach ($profiles as $profile) {
            $modules[$profile['id_profile']] = Module::getModulesAccessesByIdProfile($profile['id_profile']);
            uasort($modules[$profile['id_profile']], [$this, 'sortModuleByName']);
        }

        // Prepare form variables
        $this->fields_form = [''];
        $this->tpl_form_vars = [
            'profiles' => $profiles,
            'accesses' => $accesses,
            'id_tab_parentmodule' => (int) Tab::getIdFromClassName('AdminParentModules'),
            'id_tab_module' => (int) Tab::getIdFromClassName('AdminModules'),
            'tabs' => $this->displayTabs($tabs),
            'current_profile' => $current_profile,
            'admin_profile' => (int) _PS_ADMIN_PROFILE_,
            'access_edit' => $this->access('edit'),
            'perms' => ['view', 'add', 'edit', 'delete'],
            'id_perms' => ['view' => 0, 'add' => 1, 'edit' => 2, 'delete' => 3, 'all' => 4],
            'modules' => $modules,
            'link' => $this->context->link,
            'employee_profile_id' => (int) $this->context->employee->id_profile,
        ];

        return parent::renderForm();
    }

    /**
     * Initialize content for the access management page.
     */
    public function initContent()
    {
        $this->display = 'edit';

        if (!$this->loadObject(true)) {
            return;
        }

        $this->content .= $this->renderForm();

        // Assign content to Smarty template
        $this->context->smarty->assign(['content' => $this->content]);
    }

    /**
     * Set the toolbar title for the admin page.
     */
    public function initToolbarTitle()
    {
        $this->toolbar_title = array_unique($this->breadcrumbs);
    }

    /**
     * Initialize the page header toolbar.
     */
    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        unset($this->page_header_toolbar_btn['cancel']);
    }

    /**
     * Process AJAX request to update access permissions.
     */
    public function ajaxProcessUpdateAccess()
    {
        if (_PS_MODE_DEMO_) {
            throw new PrestaShopException($this->trans('This functionality has been disabled.', [], 'Admin.Notifications.Error'));
        }
        if ($this->access('edit') != '1') {
            throw new PrestaShopException($this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error'));
        }

        if (Tools::isSubmit('submitAddAccess')) {
            $access = new Access();
            $perm = Tools::getValue('perm');

            // Validate the permission value
            if (!in_array($perm, ['view', 'add', 'edit', 'delete', 'all'])) {
                throw new PrestaShopException('Permission does not exist.');
            }

            // Retrieve parameters
            $enabled = (bool) Tools::getValue('enabled');
            $id_tab = (int) Tools::getValue('id_tab');
            $id_profile = (int) Tools::getValue('id_profile');
            $addFromParent = (bool) Tools::getValue('addFromParent');

            // Update access and return result
            die($access->updateLgcAccess($id_profile, $id_tab, $perm, $enabled, $addFromParent));
        }
    }

    /**
     * Process AJAX request to update module access permissions.
     */
    public function ajaxProcessUpdateModuleAccess()
    {
        if (_PS_MODE_DEMO_) {
            throw new PrestaShopException($this->trans('This functionality has been disabled.', [], 'Admin.Notifications.Error'));
        }
        if ($this->access('edit') != '1') {
            throw new PrestaShopException($this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error'));
        }

        if (Tools::isSubmit('changeModuleAccess')) {
            $access = new Access();
            $perm = Tools::getValue('perm');
            $enabled = (bool) Tools::getValue('enabled');
            $id_module = (int) Tools::getValue('id_module');
            $id_profile = (int) Tools::getValue('id_profile');

            // Validate the permission value
            if (!in_array($perm, ['view', 'configure', 'uninstall'])) {
                throw new PrestaShopException('Permission does not exist.');
            }

            // Update module access and return result
            die($access->updateLgcModuleAccess($id_profile, $id_module, $perm, $enabled));
        }
    }

    /**
     * Get the current profile ID from the request.
     *
     * @return int the $_GET['id_profile'] if valid, else 1 (the first profile ID)
     */
    public function getCurrentProfileId()
    {
        return (isset($_GET['id_profile']) && !empty($_GET['id_profile']) && is_numeric($_GET['id_profile'])) ? (int) $_GET['id_profile'] : 1;
    }

    /**
     * Sort modules by their name.
     *
     * @param array $a module data
     * @param array $b module data
     *
     * @return int
     */
    protected function sortModuleByName(array $a, array $b)
    {
        $moduleAName = isset($a['name']) ? $a['name'] : null;
        $moduleBName = isset($b['name']) ? $b['name'] : null;

        return strnatcmp($moduleAName, $moduleBName);
    }

    /**
     * Return human-readable tabs hierarchy for display.
     *
     * @param array $tabs
     * @return array
     */
    protected function displayTabs(array $tabs)
    {
        return $this->getChildrenTab($tabs);
    }

    /**
     * Get child tabs for the given parent tab ID.
     *
     * @param array $tabs
     * @param int $id_parent
     * @return array
     */
    protected function getChildrenTab(array &$tabs, int $id_parent = 0)
    {
        $children = [];
        foreach ($tabs as $tab) {
            $id = $tab['id_tab'];

            if ($tab['id_parent'] == $id_parent) {
                $children[$id] = $tab;
                $children[$id]['children'] = $this->getChildrenTab($tabs, $id);
            }
        }

        return $children;
    }
}
