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
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class AdminModulesControllerCore extends AdminController
{
    /*
    ** @var array map with $_GET keywords and their callback
    */
    protected $map = [
        'install' => 'install',
        'uninstall' => 'uninstall',
        'configure' => 'getContent',
        'update' => 'update',
        'delete' => 'delete',
        'updateAll' => 'updateAll',
    ];

    protected $list_modules_categories = [];
    /** @var array */
    protected $list_partners_modules = [];
    /** @var array */
    protected $list_natives_modules = [];

    protected $nb_modules_total = 0;
    protected $nb_modules_installed = 0;
    protected $nb_modules_activated = 0;

    protected $serial_modules = '';
    protected $modules_authors = [];

    protected $id_employee;
    protected $iso_default_country;
    protected $filter_configuration = [];

    protected $xml_modules_list = _PS_API_MODULES_LIST_16_;

    /**
     * Admin Modules Controller Constructor
     * Init list modules categories
     * Load id employee
     * Load filter configuration
     * Load cache file.
     */
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        // Rely on new module controller for right management
        $this->id = Tab::getIdFromClassName('AdminModulesSf');
        $this->template = 'content-legacy.tpl';

        register_shutdown_function('displayFatalError');

        // Set the modules categories
        $this->list_modules_categories['administration']['name'] = $this->trans('Administration', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['advertising_marketing']['name'] = $this->trans('Advertising & Marketing', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['analytics_stats']['name'] = $this->trans('Analytics & Stats', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['billing_invoicing']['name'] = $this->trans('Taxes & Invoicing', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['checkout']['name'] = $this->trans('Checkout', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['content_management']['name'] = $this->trans('Content Management', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['customer_reviews']['name'] = $this->trans('Customer Reviews', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['export']['name'] = $this->trans('Export', [], 'Admin.Actions');
        $this->list_modules_categories['front_office_features']['name'] = $this->trans('Front office Features', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['i18n_localization']['name'] = $this->trans('Internationalization & Localization', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['merchandizing']['name'] = $this->trans('Merchandising', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['migration_tools']['name'] = $this->trans('Migration Tools', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['payments_gateways']['name'] = $this->trans('Payments & Gateways', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['payment_security']['name'] = $this->trans('Site certification & Fraud prevention', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['pricing_promotion']['name'] = $this->trans('Pricing & Promotion', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['quick_bulk_update']['name'] = $this->trans('Quick / Bulk update', [], 'Admin.Modules.Feature');
        /* 		$this->list_modules_categories['search_filter']['name'] = $this->trans('Search & Filter', [], 'Admin.Modules.Feature'); */
        $this->list_modules_categories['seo']['name'] = $this->trans('SEO', [], 'Admin.Catalog.Feature');
        $this->list_modules_categories['shipping_logistics']['name'] = $this->trans('Shipping & Logistics', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['slideshows']['name'] = $this->trans('Slideshows', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['smart_shopping']['name'] = $this->trans('Comparison site & Feed management', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['market_place']['name'] = $this->trans('Marketplace', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['others']['name'] = $this->trans('Other Modules', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['mobile']['name'] = $this->trans('Mobile', [], 'Admin.Global');
        $this->list_modules_categories['dashboard']['name'] = $this->trans('Dashboard', [], 'Admin.Global');
        $this->list_modules_categories['i18n_localization']['name'] = $this->trans('Internationalization & Localization', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['emailing']['name'] = $this->trans('Emailing & SMS', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['social_networks']['name'] = $this->trans('Social Networks', [], 'Admin.Modules.Feature');
        $this->list_modules_categories['social_community']['name'] = $this->trans('Social & Community', [], 'Admin.Modules.Feature');

        uasort($this->list_modules_categories, [$this, 'checkCategoriesNames']);

        // Set Id Employee, Iso Default Country and Filter Configuration
        $this->id_employee = (int) $this->context->employee->id;
        $this->iso_default_country = $this->context->country->iso_code;
        $this->filter_configuration = Configuration::getMultiple([
            'PS_SHOW_TYPE_MODULES_' . (int) $this->id_employee,
            'PS_SHOW_COUNTRY_MODULES_' . (int) $this->id_employee,
            'PS_SHOW_INSTALLED_MODULES_' . (int) $this->id_employee,
            'PS_SHOW_ENABLED_MODULES_' . (int) $this->id_employee,
            'PS_SHOW_CAT_MODULES_' . (int) $this->id_employee,
        ]);
    }

    public function checkCategoriesNames($a, $b)
    {
        if ($a['name'] === $this->trans('Other Modules')) {
            return true;
        }

        return (bool) ($a['name'] > $b['name']);
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryPlugin(['autocomplete', 'fancybox', 'tablefilter']);

        if ($this->context->mode == Context::MODE_HOST && Tools::isSubmit('addnewmodule')) {
            $this->addJS(_PS_JS_DIR_ . 'admin/addons.js');
        }
    }

    public function displayAjaxRefreshModuleList()
    {
        echo json_encode(['status' => $this->status]);
    }

    public function ajaxProcessReloadModulesList()
    {
        if (Tools::getValue('filterCategory')) {
            Configuration::updateValue('PS_SHOW_CAT_MODULES_' . (int) $this->id_employee, Tools::getValue('filterCategory'));
        }
        if (Tools::getValue('unfilterCategory')) {
            Configuration::updateValue('PS_SHOW_CAT_MODULES_' . (int) $this->id_employee, '');
        }

        $this->initContent();
        $this->smartyOutputContent('controllers/modules/list.tpl');
        exit;
    }

    public function ajaxProcessGetTabModulesList()
    {
        $tab_modules_list = Tools::getValue('tab_modules_list');
        $back = Tools::getValue('back_tab_modules_list');
        if ($back) {
            $back .= '&tab_modules_open=1';
        }
        $modules_list_unsort = [];
        if ($tab_modules_list) {
            $tab_modules_list = explode(',', $tab_modules_list);
            $modules_list_unsort = $this->getModulesByInstallation($tab_modules_list, Tools::getValue('admin_list_from_source'));
        }

        $installed = $uninstalled = [];
        foreach ($tab_modules_list as $value) {
            $continue = 0;
            foreach ($modules_list_unsort['installed'] as $mod_in) {
                if ($mod_in->name == $value) {
                    $continue = 1;
                    $installed[] = $mod_in;
                }
            }
            if ($continue) {
                continue;
            }
            foreach ($modules_list_unsort['not_installed'] as $mod_in) {
                if ($mod_in->name == $value) {
                    $uninstalled[] = $mod_in;
                }
            }
        }

        $modules_list_sort = [
            'installed' => $installed,
            'not_installed' => $uninstalled,
        ];

        $this->context->smarty->assign([
            'currentIndex' => self::$currentIndex,
            'tab_modules_list' => $modules_list_sort,
            'admin_module_favorites_view' => $this->context->link->getAdminLink('AdminModules') . '&select=favorites',
            'lang_iso' => $this->context->language->iso_code,
        ]);

        if ($admin_list_from_source = Tools::getValue('admin_list_from_source')) {
            $this->context->smarty->assign('admin_list_from_source', $admin_list_from_source);
        }

        $this->smartyOutputContent('controllers/modules/tab_modules_list.tpl');
        exit;
    }

    public function ajaxProcessSetFilter()
    {
        $this->setFilterModules(Tools::getValue('module_type'), Tools::getValue('country_module_value'), Tools::getValue('module_install'), Tools::getValue('module_status'));
        die('OK');
    }

    public function ajaxProcessSaveFavoritePreferences()
    {
        $action = Tools::getValue('action_pref');
        $value = Tools::getValue('value_pref');
        $module = Tools::getValue('module_pref');
        $id_module_preference = (int) Db::getInstance()->getValue('SELECT `id_module_preference` FROM `' . _DB_PREFIX_ . 'module_preference` WHERE `id_employee` = ' . (int) $this->id_employee . ' AND `module` = \'' . pSQL($module) . '\'');
        if ($id_module_preference > 0) {
            $update = [];
            if ($action == 'i') {
                $update['interest'] = ($value == '' ? null : (int) $value);
            }
            if ($action == 'f') {
                $update['favorite'] = ($value == '' ? null : (int) $value);
            }
            Db::getInstance()->update('module_preference', $update, '`id_employee` = ' . (int) $this->id_employee . ' AND `module` = \'' . pSQL($module) . '\'', 0, true);
        } else {
            $insert = ['id_employee' => (int) $this->id_employee, 'module' => pSQL($module), 'interest' => null, 'favorite' => null];
            if ($action == 'i') {
                $insert['interest'] = ($value == '' ? null : (int) $value);
            }
            if ($action == 'f') {
                $insert['favorite'] = ($value == '' ? null : (int) $value);
            }
            Db::getInstance()->insert('module_preference', $insert, true);
        }
        die('OK');
    }

    public function ajaxProcessSaveTabModulePreferences()
    {
        $values = Tools::getValue('value_pref');
        $module = Tools::getValue('module_pref');
        if (Validate::isModuleName($module)) {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'tab_module_preference` WHERE `id_employee` = ' . (int) $this->id_employee . ' AND `module` = \'' . pSQL($module) . '\'');
            if (is_array($values) && count($values)) {
                foreach ($values as $value) {
                    Db::getInstance()->execute('
                        INSERT INTO `' . _DB_PREFIX_ . 'tab_module_preference` (`id_tab_module_preference`, `id_employee`, `id_tab`, `module`)
                        VALUES (NULL, ' . (int) $this->id_employee . ', ' . (int) $value . ', \'' . pSQL($module) . '\');');
                }
            }
        }
        die('OK');
    }

    /*
    ** Get current URL
    **
    ** @param array $remove List of keys to remove from URL
    ** @return string
    */
    protected function getCurrentUrl($remove = [])
    {
        $url = $_SERVER['REQUEST_URI'];
        if (!$remove) {
            return $url;
        }

        if (!is_array($remove)) {
            $remove = [$remove];
        }

        $url = preg_replace('#(?<=&|\?)(' . implode('|', $remove) . ')=.*?(&|$)#i', '', $url);
        $len = strlen($url);
        if ($url[$len - 1] == '&') {
            $url = substr($url, 0, $len - 1);
        }

        return $url;
    }

    protected function extractArchive($file, $redirect = true)
    {
        $zip_folders = [];
        $tmp_folder = _PS_MODULE_DIR_ . md5((string) time());

        $success = false;
        if (substr($file, -4) == '.zip') {
            if (Tools::ZipExtract($file, $tmp_folder)) {
                $zip_folders = scandir($tmp_folder, SCANDIR_SORT_NONE);
                if (Tools::ZipExtract($file, _PS_MODULE_DIR_)) {
                    $success = true;
                }
            }
        } else {
            $archive = new Archive_Tar($file);
            if ($archive->extract($tmp_folder)) {
                $zip_folders = scandir($tmp_folder, SCANDIR_SORT_NONE);
                if ($archive->extract(_PS_MODULE_DIR_)) {
                    $success = true;
                }
            }
        }

        if (!$success) {
            $this->errors[] = $this->trans('There was an error while extracting the module (file may be corrupted).', [], 'Admin.Modules.Notification');
        } else {
            //check if it's a real module
            foreach ($zip_folders as $folder) {
                if (!in_array($folder, ['.', '..', '.svn', '.git', '__MACOSX']) && !Module::getInstanceByName($folder)) {
                    $this->errors[] = $this->trans('The module %1$s that you uploaded is not a valid module.', [$folder], 'Admin.Modules.Notification');
                    $this->recursiveDeleteOnDisk(_PS_MODULE_DIR_ . $folder);
                }
            }
        }

        @unlink($file);
        $this->recursiveDeleteOnDisk($tmp_folder);

        if ($success && $redirect) {
            Tools::redirectAdmin(self::$currentIndex . '&conf=8&anchor=' . ucfirst($folder ?? '') . '&token=' . $this->token);
        }

        return $success;
    }

    protected function recursiveDeleteOnDisk($dir)
    {
        if (strpos(realpath($dir), realpath(_PS_MODULE_DIR_)) === false) {
            return;
        }
        if (is_dir($dir)) {
            $objects = scandir($dir, SCANDIR_SORT_NONE);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($dir . '/' . $object) == 'dir') {
                        $this->recursiveDeleteOnDisk($dir . '/' . $object);
                    } else {
                        unlink($dir . '/' . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /*
    ** Filter Configuration Methods
    ** Set and reset filter configuration
    */

    protected function setFilterModules($module_type, $country_module_value, $module_install, $module_status)
    {
        Configuration::updateValue('PS_SHOW_TYPE_MODULES_' . (int) $this->id_employee, $module_type);
        Configuration::updateValue('PS_SHOW_COUNTRY_MODULES_' . (int) $this->id_employee, $country_module_value);
        Configuration::updateValue('PS_SHOW_INSTALLED_MODULES_' . (int) $this->id_employee, $module_install);
        Configuration::updateValue('PS_SHOW_ENABLED_MODULES_' . (int) $this->id_employee, $module_status);
    }

    protected function resetFilterModules()
    {
        Configuration::updateValue('PS_SHOW_TYPE_MODULES_' . (int) $this->id_employee, 'allModules');
        Configuration::updateValue('PS_SHOW_COUNTRY_MODULES_' . (int) $this->id_employee, 0);
        Configuration::updateValue('PS_SHOW_INSTALLED_MODULES_' . (int) $this->id_employee, 'installedUninstalled');
        Configuration::updateValue('PS_SHOW_ENABLED_MODULES_' . (int) $this->id_employee, 'enabledDisabled');
        Configuration::updateValue('PS_SHOW_CAT_MODULES_' . (int) $this->id_employee, '');
    }

    /*
    ** Post Process Filter
    **
    */

    public function postProcessFilterModules()
    {
        $this->setFilterModules(Tools::getValue('module_type'), Tools::getValue('country_module_value'), Tools::getValue('module_install'), Tools::getValue('module_status'));
        Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
    }

    public function postProcessResetFilterModules()
    {
        $this->resetFilterModules();
        Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
    }

    public function postProcessFilterCategory()
    {
        // Save configuration and redirect employee
        Configuration::updateValue('PS_SHOW_CAT_MODULES_' . (int) $this->id_employee, Tools::getValue('filterCategory'));
        Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
    }

    public function postProcessUnfilterCategory()
    {
        // Save configuration and redirect employee
        Configuration::updateValue('PS_SHOW_CAT_MODULES_' . (int) $this->id_employee, '');
        Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
    }

    /*
    ** Post Process Module CallBack
    **
    */

    public function postProcessReset()
    {
        if ($this->access('edit')) {
            $module = Module::getInstanceByName(Tools::getValue('module_name'));
            if (Validate::isLoadedObject($module)) {
                if (!$module->getPermission('configure')) {
                    $this->errors[] = $this->trans('You do not have the permission to use this module.', [], 'Admin.Modules.Notification');
                } else {
                    if (Tools::getValue('keep_data') == '1' && method_exists($module, 'reset')) {
                        if ($module->reset()) {
                            Tools::redirectAdmin(self::$currentIndex . '&conf=21&token=' . $this->token . '&tab_module=' . $module->tab . '&module_name=' . $module->name . '&anchor=' . ucfirst($module->name));
                        } else {
                            $this->errors[] = $this->trans('Cannot reset this module.', [], 'Admin.Modules.Notification');
                        }
                    } else {
                        if ($module->uninstall()) {
                            if ($module->install()) {
                                Tools::redirectAdmin(self::$currentIndex . '&conf=21&token=' . $this->token . '&tab_module=' . $module->tab . '&module_name=' . $module->name . '&anchor=' . ucfirst($module->name));
                            } else {
                                $this->errors[] = $this->trans('Cannot install this module.', [], 'Admin.Modules.Notification');
                            }
                        } else {
                            $this->errors[] = $this->trans('Cannot uninstall this module.', [], 'Admin.Modules.Notification');
                        }
                    }
                }
            } else {
                $this->errors[] = $this->trans('Cannot load the module\'s object.', [], 'Admin.Modules.Notification');
            }

            if (($errors = $module->getErrors()) && is_array($errors)) {
                $this->errors = array_merge($this->errors, $errors);
            }
        } else {
            $this->errors[] = $this->trans('You do not have permission to add this.', [], 'Admin.Notifications.Error');
        }
    }

    public function postProcessDownload()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_ || ($this->context->mode == Context::MODE_HOST)) {
            $this->errors[] = $this->trans('This functionality has been disabled.', [], 'Admin.Notifications.Error');

            return;
        }

        // Try to upload and unarchive the module
        if ($this->access('add')) {
            // UPLOAD_ERR_OK: 0
            // UPLOAD_ERR_INI_SIZE: 1
            // UPLOAD_ERR_FORM_SIZE: 2
            // UPLOAD_ERR_NO_TMP_DIR: 6
            // UPLOAD_ERR_CANT_WRITE: 7
            // UPLOAD_ERR_EXTENSION: 8
            // UPLOAD_ERR_PARTIAL: 3

            if (isset($_FILES['file']['error']) && $_FILES['file']['error'] != UPLOAD_ERR_OK) {
                switch ($_FILES['file']['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $this->errors[] = $this->trans('File too large (limit of %s bytes).', [Tools::getMaxUploadSize()], 'Admin.Notifications.Error');

                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $this->errors[] = $this->trans('File upload was not completed.', [], 'Admin.Notifications.Error');

                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $this->errors[] = $this->trans('No file was uploaded.', [], 'Admin.Notifications.Error');

                        break;
                    default:
                        $this->errors[] = $this->trans('Internal error #%s', [$_FILES['newfile']['error']], 'Admin.Notifications.Error');

                        break;
                }
            } elseif (!isset($_FILES['file']['tmp_name']) || empty($_FILES['file']['tmp_name'])) {
                $this->errors[] = $this->trans('No file has been selected', [], 'Admin.Notifications.Error');
            } elseif (substr($_FILES['file']['name'], -4) != '.tar' && substr($_FILES['file']['name'], -4) != '.zip'
                && substr($_FILES['file']['name'], -4) != '.tgz' && substr($_FILES['file']['name'], -7) != '.tar.gz') {
                $this->errors[] = $this->trans('Unknown archive type.', [], 'Admin.Modules.Notification');
            } elseif (!move_uploaded_file($_FILES['file']['tmp_name'], _PS_MODULE_DIR_ . $_FILES['file']['name'])) {
                $this->errors[] = $this->trans('An error occurred while copying the archive to the module directory.', [], 'Admin.Modules.Notification');
            } else {
                $this->extractArchive(_PS_MODULE_DIR_ . $_FILES['file']['name']);
            }
        } else {
            $this->errors[] = $this->trans('You do not have permission to add this.', [], 'Admin.Notifications.Error');
        }
    }

    public function postProcessEnable()
    {
        if ($this->access('edit')) {
            $module = Module::getInstanceByName(Tools::getValue('module_name'));
            if (Validate::isLoadedObject($module)) {
                if (!$module->getPermission('configure')) {
                    $this->errors[] = $this->trans('You do not have the permission to use this module.', [], 'Admin.Modules.Notification');
                } else {
                    if (Tools::getValue('enable')) {
                        $module->enable();
                    } else {
                        $module->disable();
                    }
                    Tools::redirectAdmin($this->getCurrentUrl('enable'));
                }
            } else {
                $this->errors[] = $this->trans('Cannot load the module\'s object.', [], 'Admin.Modules.Notification');
            }
        } else {
            $this->errors[] = $this->trans('You do not have permission to add this.', [], 'Admin.Notifications.Error');
        }
    }

    public function postProcessEnable_Device()
    {
        if ($this->access('edit')) {
            $module = Module::getInstanceByName(Tools::getValue('module_name'));
            if (Validate::isLoadedObject($module)) {
                if (!$module->getPermission('configure')) {
                    $this->errors[] = $this->trans('You do not have the permission to use this module.', [], 'Admin.Modules.Notification');
                } else {
                    $module->enableDevice((int) Tools::getValue('enable_device'));
                    Tools::redirectAdmin($this->getCurrentUrl('enable_device'));
                }
            } else {
                $this->errors[] = $this->trans('Cannot load the module\'s object.', [], 'Admin.Modules.Notification');
            }
        } else {
            $this->errors[] = $this->trans('You do not have permission to add this.', [], 'Admin.Notifications.Error');
        }
    }

    public function postProcessDisable_Device()
    {
        if ($this->access('edit')) {
            $module = Module::getInstanceByName(Tools::getValue('module_name'));
            if (Validate::isLoadedObject($module)) {
                if (!$module->getPermission('configure')) {
                    $this->errors[] = $this->trans('You do not have the permission to use this module.', [], 'Admin.Modules.Notification');
                } else {
                    $module->disableDevice((int) Tools::getValue('disable_device'));
                    Tools::redirectAdmin($this->getCurrentUrl('disable_device'));
                }
            } else {
                $this->errors[] = $this->trans('Cannot load the module\'s object.', [], 'Admin.Modules.Notification');
            }
        } else {
            $this->errors[] = $this->trans('You do not have permission to add this.', [], 'Admin.Notifications.Error');
        }
    }

    public function postProcessDelete()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            $this->errors[] = $this->trans('This functionality has been disabled.', [], 'Admin.Notifications.Error');

            return;
        }

        if ($this->access('delete')) {
            if (Tools::getValue('module_name') != '') {
                $module = Module::getInstanceByName(Tools::getValue('module_name'));
                if (Validate::isLoadedObject($module) && !$module->getPermission('configure')) {
                    $this->errors[] = $this->trans('You do not have the permission to use this module.', [], 'Admin.Modules.Notification');
                } else {
                    // Uninstall the module before deleting the files, but do not block the process if uninstall returns false
                    $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
                    $moduleManager = $moduleManagerBuilder->build();

                    if ($moduleManager->isInstalled($module->name)) {
                        $module->uninstall();
                    }
                    $moduleDir = _PS_MODULE_DIR_ . str_replace(['.', '/', '\\'], ['', '', ''], Tools::getValue('module_name'));
                    $this->recursiveDeleteOnDisk($moduleDir);
                    if (!file_exists($moduleDir)) {
                        Tools::redirectAdmin(self::$currentIndex . '&conf=22&token=' . $this->token . '&tab_module=' . Tools::getValue('tab_module') . '&module_name=' . Tools::getValue('module_name'));
                    } else {
                        $this->errors[] = $this->trans('Sorry, the module cannot be deleted. Please check if you have the right permissions on this folder.', [], 'Admin.Modules.Notification');
                    }
                }
            }
        } else {
            $this->errors[] = $this->trans('You do not have permission to delete this.', [], 'Admin.Notifications.Error');
        }
    }

    public function postProcessCallback()
    {
        $return = false;
        $installed_modules = [];
        $key = '';

        foreach ($this->map as $key => $method) {
            if (!Tools::getValue($key)) {
                continue;
            }

            /* PrestaShop demo mode */
            if (_PS_MODE_DEMO_) {
                $this->errors[] = $this->trans('This functionality has been disabled.', [], 'Admin.Notifications.Error');

                return;
            }

            $modules = Tools::getValue($key);
            if ($key != 'updateAll') {
                if (strpos($modules, '|')) {
                    $modules_list_save = $modules;
                    $modules = explode('|', $modules);
                }

                if (!is_array($modules)) {
                    $modules = (array) $modules;
                }
            } else {
                $allModules = Module::getModulesOnDisk(true, $this->context->employee->id);

                $modules = [];

                foreach ($allModules as $km => $moduleToUpdate) {
                    if ($moduleToUpdate->installed && isset($moduleToUpdate->version_addons) && $moduleToUpdate->version_addons) {
                        $modules[] = (string) $moduleToUpdate->name;
                    }
                }
            }

            $module_errors = [];
            foreach ($modules as $name) {
                $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
                $moduleManager = $moduleManagerBuilder->build();

                // Check potential error
                if (!($module = Module::getInstanceByName(urldecode($name)))) {
                    $this->errors[] = $this->trans('Module not found');
                } elseif (($this->context->mode >= Context::MODE_HOST_CONTRIB) && in_array($module->name, Module::$hosted_modules_blacklist)) {
                    $this->errors[] = $this->trans('You do not have permission to access this module.', [], 'Admin.Modules.Notification');
                } elseif ($key == 'install' && !$this->access('add')) {
                    $this->errors[] = $this->trans('You do not have permission to install this module.', [], 'Admin.Modules.Notification');
                } elseif ($key == 'delete' && (!$this->access('delete') || !$module->getPermission('configure'))) {
                    $this->errors[] = $this->trans('You do not have permission to delete this module.', [], 'Admin.Modules.Notification');
                } elseif ($key == 'configure' && (!$this->access('edit') || !$module->getPermission('configure') || !$moduleManager->isInstalled(urldecode($name)))) {
                    $this->errors[] = $this->trans('You do not have permission to configure this module.', [], 'Admin.Modules.Notification');
                } elseif ($key == 'install' && $moduleManager->isInstalled($module->name)) {
                    $this->errors[] = $this->trans('This module is already installed: %s.', [$module->name], 'Admin.Modules.Notification');
                } elseif ($key == 'uninstall' && !$moduleManager->isInstalled($module->name)) {
                    $this->errors[] = $this->trans('This module has already been uninstalled: %s.', [$module->name], 'Admin.Modules.Notification');
                } elseif ($key == 'update' && !$moduleManager->isInstalled($module->name)) {
                    $this->errors[] = $this->trans('This module needs to be installed in order to be updated: %s.', [$module->name], 'Admin.Modules.Notification');
                } else {
                    // If we install a module, force temporary global context for multishop
                    if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL && $method != 'getContent') {
                        $shop_id = (int) Context::getContext()->shop->id;
                        Context::getContext()->tmpOldShop = clone Context::getContext()->shop;
                        if ($shop_id) {
                            Context::getContext()->shop = new Shop($shop_id);
                        }
                    }

                    //retrocompatibility
                    if (Tools::getValue('controller') != '') {
                        $_POST['tab'] = Tools::safeOutput(Tools::getValue('controller'));
                    }

                    $echo = '';
                    if ($key != 'update' && $key != 'updateAll') {
                        // We check if method of module exists
                        if (!method_exists($module, $method)) {
                            throw new PrestaShopException('Method of module cannot be found');
                        }

                        if ($key == 'uninstall' && !Module::getPermissionStatic($module->id, 'uninstall')) {
                            $this->errors[] = $this->trans('You do not have permission to uninstall this module.', [], 'Admin.Modules.Notification');
                        }

                        if (count($this->errors)) {
                            continue;
                        }
                        // Get the return value of current method
                        $echo = $module->{$method}();

                        // After a successful install of a single module that has a configuration method, to the configuration page
                        if ($key == 'install' && $echo === true && strpos(Tools::getValue('install'), '|') === false && method_exists($module, 'getContent')) {
                            Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token . '&configure=' . $module->name . '&conf=12');
                        }
                    }

                    // If the method called is "configure" (getContent method), we show the html code of configure page
                    if ($key == 'configure' && $moduleManager->isInstalled($module->name)) {
                        $this->bootstrap = (bool) $module->bootstrap;
                        if (isset($module->multishop_context)) {
                            $this->multishop_context = $module->multishop_context;
                        }

                        $back_link = self::$currentIndex . '&token=' . $this->token . '&tab_module=' . $module->tab . '&module_name=' . $module->name;
                        $hook_link = 'index.php?tab=AdminModulesPositions&token=' . Tools::getAdminTokenLite('AdminModulesPositions') . '&show_modules=' . (int) $module->id;
                        $trad_link = 'index.php?tab=AdminTranslations&token=' . Tools::getAdminTokenLite('AdminTranslations') . '&type=modules&lang=';
                        $rtl_link = 'index.php?tab=AdminModules&token=' . Tools::getAdminTokenLite('AdminModules') . '&configure=' . $module->name . '&generate_rtl=1';
                        $disable_link = $this->context->link->getAdminLink('AdminModules') . '&module_name=' . $module->name . '&enable=0&tab_module=' . $module->tab;
                        $uninstall_link = $this->context->link->getAdminLink('AdminModules') . '&module_name=' . $module->name . '&uninstall=' . $module->name . '&tab_module=' . $module->tab;
                        $reset_link = $this->context->link->getAdminLink('AdminModules') . '&module_name=' . $module->name . '&reset&tab_module=' . $module->tab;

                        $is_reset_ready = false;
                        if (method_exists($module, 'reset')) {
                            $is_reset_ready = true;
                        }

                        $this->context->smarty->assign(
                            [
                                'module_name' => $module->name,
                                'module_display_name' => $module->displayName,
                                'back_link' => $back_link,
                                'module_hook_link' => $hook_link,
                                'module_disable_link' => $disable_link,
                                'module_uninstall_link' => $uninstall_link,
                                'module_reset_link' => $reset_link,
                                'trad_link' => $trad_link,
                                'module_rtl_link' => ($this->context->language->is_rtl ? $rtl_link : null),
                                'module_languages' => Language::getLanguages(false),
                                'theme_language_dir' => _THEME_LANG_DIR_,
                                'page_header_toolbar_title' => $this->page_header_toolbar_title,
                                'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
                                'add_permission' => $this->access('add'),
                                'is_reset_ready' => $is_reset_ready,
                            ]
                        );

                        // Display checkbox in toolbar if multishop
                        if (Shop::isFeatureActive()) {
                            if (Shop::getContext() == Shop::CONTEXT_SHOP) {
                                $shop_context = 'shop <strong>' . $this->context->shop->name . '</strong>';
                            } elseif (Shop::getContext() == Shop::CONTEXT_GROUP) {
                                $shop_group = new ShopGroup((int) Shop::getContextShopGroupID());
                                $shop_context = 'all shops of group shop <strong>' . $shop_group->name . '</strong>';
                            } else {
                                $shop_context = 'all shops';
                            }

                            $this->context->smarty->assign([
                                'module' => $module,
                                'display_multishop_checkbox' => true,
                                'current_url' => $this->getCurrentUrl('enable'),
                                'shop_context' => $shop_context,
                            ]);
                        }

                        $this->context->smarty->assign([
                            'is_multishop' => Shop::isFeatureActive(),
                            'multishop_context' => Shop::CONTEXT_ALL | Shop::CONTEXT_GROUP | Shop::CONTEXT_SHOP,
                        ]);

                        if (Shop::isFeatureActive() && isset(Context::getContext()->tmpOldShop)) {
                            Context::getContext()->shop = clone Context::getContext()->tmpOldShop;
                            unset(Context::getContext()->tmpOldShop);
                        }

                        // Display module configuration
                        $header = $this->context->smarty->fetch('controllers/modules/configure.tpl');
                        $configuration_bar = $this->context->smarty->fetch('controllers/modules/configuration_bar.tpl');

                        $output = $header . $echo;

                        $this->context->smarty->assign('module_content', $output . $configuration_bar);
                    } elseif ($echo === true) {
                        $return = 13;
                        if ($method == 'install') {
                            $return = 12;
                            $installed_modules[] = $module->id;
                        }
                    } elseif ($echo === false) {
                        $module_errors[] = ['name' => $name, 'message' => $module->getErrors()];
                    }

                    if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL && isset(Context::getContext()->tmpOldShop)) {
                        Context::getContext()->shop = clone Context::getContext()->tmpOldShop;
                        unset(Context::getContext()->tmpOldShop);
                    }
                }
                if ($key != 'configure' && Tools::getIsset('bpay')) {
                    Tools::redirectAdmin('index.php?tab=AdminPayment&token=' . Tools::getAdminToken('AdminPayment' . (int) Tab::getIdFromClassName('AdminPayment') . (int) $this->id_employee));
                }
            }

            if (count($module_errors)) {
                // If error during module installation, no redirection
                $html_error = $this->generateHtmlMessage($module_errors);
                if ($key == 'uninstall') {
                    $this->errors[] = $this->trans('The following module(s) could not be uninstalled properly: %s.', [$html_error], 'Admin.Modules.Notification');
                } else {
                    $this->errors[] = $this->trans('The following module(s) could not be installed properly: %s.', [$html_error], 'Admin.Modules.Notification');
                }
                $this->context->smarty->assign('error_module', 'true');
            }
        }

        if ($return) {
            $params = (count($installed_modules)) ? '&installed_modules=' . implode('|', $installed_modules) : '';
            $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
            $moduleManager = $moduleManagerBuilder->build();

            // If redirect parameter is present and module installed with success, we redirect on configuration module page
            if (Tools::getValue('redirect') == 'config' && Tools::getValue('module_name') != '' && $return == '12' && $moduleManager->isInstalled(pSQL(Tools::getValue('module_name')))) {
                Tools::redirectAdmin('index.php?controller=adminmodules&configure=' . Tools::getValue('module_name') . '&token=' . Tools::getValue('token') . '&module_name=' . Tools::getValue('module_name') . $params);
            }
            if (isset($module)) {
                Tools::redirectAdmin(self::$currentIndex . '&conf=' . $return . '&token=' . $this->token . '&tab_module=' . $module->tab . '&module_name=' . $module->name . '&anchor=' . ucfirst($module->name) . (isset($modules_list_save) ? '&modules_list=' . $modules_list_save : '') . $params);
            }
        }

        if (Tools::getValue('update') || Tools::getValue('updateAll')) {
            if ($key == 'updateAll') {
                Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token . '&allUpdated=1');
            } elseif (isset($modules_list_save)) {
                Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token . '&updated=1&module_name=' . $modules_list_save);
            } elseif (isset($module)) {
                Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token . '&updated=1&tab_module=' . $module->tab . '&module_name=' . $module->name . '&anchor=' . ucfirst($module->name) . (isset($modules_list_save) ? '&modules_list=' . $modules_list_save : ''));
            }
        }
    }

    /**
     * @param array|null $tab_modules_list
     * @param false $install_source_tracking
     *
     * @return array<string, array<Module>>
     */
    protected function getModulesByInstallation($tab_modules_list = null, $install_source_tracking = false)
    {
        $all_modules = Module::getModulesOnDisk(true, $this->id_employee);
        $all_unik_modules = [];
        $modules_list = ['installed' => [], 'not_installed' => []];

        foreach ($all_modules as $mod) {
            if (!isset($all_unik_modules[$mod->name])) {
                $all_unik_modules[$mod->name] = $mod;
            }
        }

        $all_modules = $all_unik_modules;

        foreach ($all_modules as $module) {
            if (!isset($tab_modules_list) || in_array($module->name, $tab_modules_list)) {
                $perm = true;
                if ($module->id) {
                    $perm &= Module::getPermissionStatic($module->id, 'configure');
                } else {
                    $id_admin_module = Tab::getIdFromClassName('AdminModules');
                    $access = Profile::getProfileAccess($this->context->employee->id_profile, $id_admin_module);
                    if (!$access['edit']) {
                        $perm &= false;
                    }
                }

                if ($perm) {
                    $this->fillModuleData($module, 'array', null, $install_source_tracking);
                    if ($module->id) {
                        $modules_list['installed'][] = $module;
                    } else {
                        $modules_list['not_installed'][] = $module;
                    }
                }
            }
        }

        return $modules_list;
    }

    public function postProcess()
    {
        if (!Tools::getIsset('configure') && !Tools::getIsset('module_name')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModulesSf'));
        }

        // Parent Post Process
        parent::postProcess();

        // Get the list of installed module ans prepare it for ajax call.
        if (($list = Tools::getValue('installed_modules'))) {
            Context::getContext()->smarty->assign('installed_modules', json_encode(explode('|', $list)));
        }

        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();

        // If redirect parameter is present and module already installed, we redirect on configuration module page
        if (Tools::getValue('redirect') == 'config' && Tools::getValue('module_name') != '' && $moduleManager->isInstalled(pSQL(Tools::getValue('module_name')))) {
            Tools::redirectAdmin('index.php?controller=adminmodules&configure=' . Tools::getValue('module_name') . '&token=' . Tools::getValue('token') . '&module_name=' . Tools::getValue('module_name'));
        }

        // Execute filter or callback methods
        $filter_methods = ['filterModules', 'resetFilterModules', 'filterCategory', 'unfilterCategory'];
        $callback_methods = ['reset', 'download', 'enable', 'delete', 'enable_device', 'disable_device'];
        $post_process_methods_list = array_merge((array) $filter_methods, (array) $callback_methods);
        foreach ($post_process_methods_list as $ppm) {
            if (Tools::isSubmit($ppm)) {
                $ppm = 'postProcess' . ucfirst($ppm);
                if (method_exists($this, $ppm)) {
                    $ppm_return = $this->$ppm();
                }
            }
        }

        // Call appropriate module callback
        if (!isset($ppm_return)) {
            $this->postProcessCallback();
        }

        if (Tools::getValue('generate_rtl') && Tools::getValue('configure') != '') {
            Language::getRtlStylesheetProcessor()
                ->setProcessPaths([
                    _PS_MODULE_DIR_ . Tools::getValue('configure'),
                ])
                ->process();
            Tools::redirectAdmin('index.php?controller=adminmodules&configure=' . Tools::getValue('configure') . '&token=' . Tools::getValue('token') . '&conf=6');
        }

        if ($back = Tools::getValue('back')) {
            Tools::redirectAdmin($back);
        }
    }

    /**
     * Generate html errors for a module process.
     *
     * @param array $module_errors
     *
     * @return string
     */
    protected function generateHtmlMessage($module_errors)
    {
        $html_error = '';

        if (count($module_errors)) {
            $html_error = '<ul>';
            foreach ($module_errors as $module_error) {
                $html_error_description = '';
                if (count($module_error['message']) > 0) {
                    foreach ($module_error['message'] as $e) {
                        $html_error_description .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;' . $e;
                    }
                }
                $html_error .= '<li><b>' . $module_error['name'] . '</b> : ' . $html_error_description . '</li>';
            }
            $html_error .= '</ul>';
        }

        return $html_error;
    }

    public function initModulesList(&$modules)
    {
        foreach ($modules as $k => $module) {
            // Check add permissions, if add permissions not set, addons modules and uninstalled modules will not be displayed
            if (!$this->access('add') && isset($module->type) && ($module->type != 'addonsNative' || $module->type != 'addonsBought')) {
                unset($modules[$k]);
            } elseif (!$this->access('add') && (!isset($module->id) || $module->id < 1)) {
                unset($modules[$k]);
            } elseif ($module->id && !Module::getPermissionStatic($module->id, 'view') && !Module::getPermissionStatic($module->id, 'configure')) {
                unset($modules[$k]);
            } else {
                // Init serial and modules author list
                if (!in_array($module->name, $this->list_natives_modules)) {
                    $this->serial_modules .= $module->name . ' ' . $module->version . '-' . ($module->active ? 'a' : 'i') . "\n";
                }
                $module_author = $module->author;
                if (!empty($module_author) && ($module_author != '')) {
                    $this->modules_authors[strtolower($module_author)] = 'notselected';
                }
            }
        }
        $this->serial_modules = urlencode($this->serial_modules);
    }

    /**
     * @param Module $module
     */
    public function makeModulesStats($module)
    {
        // Count Installed Modules
        if (isset($module->id) && $module->id > 0) {
            ++$this->nb_modules_installed;
        }

        // Count Activated Modules
        if (isset($module->id) && $module->id > 0 && $module->active > 0) {
            ++$this->nb_modules_activated;
        }

        // Count Modules By Category
        if (isset($this->list_modules_categories[$module->tab]['nb'])) {
            ++$this->list_modules_categories[$module->tab]['nb'];
        } else {
            ++$this->list_modules_categories['others']['nb'];
        }
    }

    public function isModuleFiltered($module)
    {
        // If action on module, we display it
        if (Tools::getValue('module_name') != '' && Tools::getValue('module_name') == $module->name) {
            return false;
        }

        // Filter on module name
        $filter_name = Tools::getValue('filtername');
        if (!empty($filter_name)) {
            if (stristr($module->name, $filter_name) === false && stristr($module->displayName, $filter_name) === false && stristr($module->description, $filter_name) === false) {
                return true;
            }

            return false;
        }

        // Filter on interest
        if ($module->interest !== '') {
            if ($module->interest === '0') {
                return true;
            }
        } elseif ((int) Db::getInstance()->getValue('SELECT `id_module_preference` FROM `' . _DB_PREFIX_ . 'module_preference` WHERE `module` = \'' . pSQL($module->name) . '\' AND `id_employee` = ' . (int) $this->id_employee . ' AND `interest` = 0') > 0) {
            return true;
        }

        // Filter on favorites
        if (Configuration::get('PS_SHOW_CAT_MODULES_' . (int) $this->id_employee) == 'favorites') {
            if ((int) Db::getInstance()->getValue('SELECT `id_module_preference` FROM `' . _DB_PREFIX_ . 'module_preference` WHERE `module` = \'' . pSQL($module->name) . '\' AND `id_employee` = ' . (int) $this->id_employee . ' AND `favorite` = 1 AND (`interest` = 1 OR `interest` IS NULL)') < 1) {
                return true;
            }
        } else {
            // Handle "others" category
            if (!isset($this->list_modules_categories[$module->tab])) {
                $module->tab = 'others';
            }

            // Filter on module category
            $category_filtered = [];
            $filter_categories = explode('|', Configuration::get('PS_SHOW_CAT_MODULES_' . (int) $this->id_employee));
            foreach ($filter_categories as $fc) {
                if (!empty($fc)) {
                    $category_filtered[$fc] = 1;
                }
            }
            if (count($category_filtered) > 0 && !isset($category_filtered[$module->tab])) {
                return true;
            }
        }

        // Filter on module type and author
        $show_type_modules = $this->filter_configuration['PS_SHOW_TYPE_MODULES_' . (int) $this->id_employee];
        if ($show_type_modules == 'addonsModules' && (!isset($module->type) || $module->type != 'addonsBought')) {
            return true;
        } elseif ($show_type_modules == 'mustHaveModules' && (!isset($module->type) || $module->type != 'addonsMustHave')) {
            return true;
        } elseif (strpos($show_type_modules, 'authorModules[') !== false) {
            // setting selected author in authors set
            $author_selected = substr(str_replace(['authorModules[', "\'"], ['', "'"], $show_type_modules), 0, -1);
            $this->modules_authors[$author_selected] = 'selected';
            if (empty($module->author) || strtolower($module->author) != $author_selected) {
                return true;
            }
        }

        // Filter on install status
        $show_installed_modules = $this->filter_configuration['PS_SHOW_INSTALLED_MODULES_' . (int) $this->id_employee];
        if ($show_installed_modules == 'installed' && !$module->id) {
            return true;
        }
        if ($show_installed_modules == 'uninstalled' && $module->id) {
            return true;
        }

        // Filter on active status
        $show_enabled_modules = $this->filter_configuration['PS_SHOW_ENABLED_MODULES_' . (int) $this->id_employee];
        if ($show_enabled_modules == 'enabled' && !$module->active) {
            return true;
        }
        if ($show_enabled_modules == 'disabled' && $module->active) {
            return true;
        }

        // Filter on country
        $show_country_modules = $this->filter_configuration['PS_SHOW_COUNTRY_MODULES_' . (int) $this->id_employee];
        if ($show_country_modules
            && (
                isset($module->limited_countries)
                && !empty($module->limited_countries)
                && (
                    (
                        is_array($module->limited_countries)
                        && !in_array(strtolower($this->iso_default_country), $module->limited_countries)
                    )
                    || (
                        !is_array($module->limited_countries)
                        && strtolower($this->iso_default_country) != (string) $module->limited_countries
                    )
                )
            )
        ) {
            return true;
        }

        // Module has not been filtered
        return false;
    }

    public function renderKpis()
    {
        $time = time();
        $kpis = [];

        /* The data generation is located in AdminStatsControllerCore */

        $helper = new HelperKpi();
        $helper->id = 'box-installed-modules';
        $helper->icon = 'icon-puzzle-piece';
        $helper->color = 'color1';
        $helper->title = $this->trans('Installed modules', [], 'Admin.Modules.Feature');
        if (ConfigurationKPI::get('INSTALLED_MODULES') !== false && ConfigurationKPI::get('INSTALLED_MODULES') != '') {
            $helper->value = ConfigurationKPI::get('INSTALLED_MODULES');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=installed_modules';
        $helper->refresh = (bool) (ConfigurationKPI::get('INSTALLED_MODULES_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-disabled-modules';
        $helper->icon = 'icon-off';
        $helper->color = 'color2';
        $helper->title = $this->trans('Disabled modules', [], 'Admin.Modules.Feature');
        if (ConfigurationKPI::get('DISABLED_MODULES') !== false && ConfigurationKPI::get('DISABLED_MODULES') != '') {
            $helper->value = ConfigurationKPI::get('DISABLED_MODULES');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=disabled_modules';
        $helper->refresh = (bool) (ConfigurationKPI::get('DISABLED_MODULES_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-update-modules';
        $helper->icon = 'icon-refresh';
        $helper->color = 'color3';
        $helper->title = $this->trans('Modules to update');
        if (ConfigurationKPI::get('UPDATE_MODULES') !== false && ConfigurationKPI::get('UPDATE_MODULES') != '') {
            $helper->value = ConfigurationKPI::get('UPDATE_MODULES');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=update_modules';
        $helper->refresh = (bool) (ConfigurationKPI::get('UPDATE_MODULES_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpiRow();
        $helper->kpis = $kpis;

        return $helper->generate();
    }

    public function initModal()
    {
        parent::initModal();

        $languages = Language::getLanguages(false);
        $translateLinks = [];

        if (Tools::getIsset('configure')) {
            /** @var Module|false $module */
            $module = Module::getInstanceByName(Tools::getValue('configure'));

            if (false === $module) {
                return;
            }

            $isNewTranslateSystem = $module->isUsingNewTranslationSystem();
            $link = Context::getContext()->link;
            foreach ($languages as $lang) {
                if ($isNewTranslateSystem) {
                    $translateLinks[$lang['iso_code']] = $link->getAdminLink('AdminTranslationSf', true, [
                        'lang' => $lang['iso_code'],
                        'type' => 'modules',
                        'selected' => $module->name,
                        'locale' => $lang['locale'],
                    ]);
                } else {
                    $translateLinks[$lang['iso_code']] = $link->getAdminLink('AdminTranslations', true, [], [
                        'type' => 'modules',
                        'module' => $module->name,
                        'lang' => $lang['iso_code'],
                    ]);
                }
            }
        }

        $this->context->smarty->assign([
            'trad_link' => 'index.php?tab=AdminTranslations&token=' . Tools::getAdminTokenLite('AdminTranslations') . '&type=modules&module=' . Tools::getValue('configure') . '&lang=',
            'module_languages' => $languages,
            'module_name' => Tools::getValue('configure'),
            'translateLinks' => $translateLinks,
        ]);

        $modal_content = $this->context->smarty->fetch('controllers/modules/modal_translation.tpl');
        $this->modals[] = [
            'modal_id' => 'moduleTradLangSelect',
            'modal_class' => 'modal-sm',
            'modal_title' => $this->trans('Translate this module'),
            'modal_content' => $modal_content,
        ];
    }

    public function initContent()
    {
        if (Tools::isSubmit('addnewmodule') && $this->context->mode == Context::MODE_HOST) {
            $this->display = 'add';
            $this->context->smarty->assign(['iso_code' => $this->context->language->iso_code]);

            return parent::initContent();
        }

        // If we are on a module configuration, no need to load all modules
        if (Tools::getValue('configure') != '') {
            $this->context->smarty->assign(['maintenance_mode' => !(bool) Configuration::Get('PS_SHOP_ENABLE')]);

            return true;
        }

        // Since 1.7, legacy modules page does not have to be show
        // Redirect to the new page, can not do this into __construct because
        // some install module, configuration are done with this controller...
        Tools::redirectAdmin($this->getAdminModulesUrl());
    }

    public function assignReadMoreSmartyVar()
    {
        $modules = Module::getModulesOnDisk();

        foreach ($modules as $module) {
            if ($module->name == Tools::getValue('module')) {
                break;
            }
        }

        if (!isset($module)) {
            return;
        }

        $url = $module->url;

        if (isset($module->type) && ($module->type == 'addonsPartner' || $module->type == 'addonsNative')) {
            $url = $this->context->link->getAdminLink('AdminModules')
                . '&install=' . urlencode($module->name)
                . '&tab_module=' . $module->tab
                . '&module_name=' . $module->name
                . '&anchor=' . ucfirst($module->name);
            if ($admin_list_from_source = Tools::getValue('admin_list_from_source')) {
                $url .= '&source=' . $admin_list_from_source;
            }
        } else {
            if ($admin_list_from_source = Tools::getValue('admin_list_from_source')) {
                $url .= '&utm_term=' . $admin_list_from_source;
            }
        }

        $this->fillModuleData($module, 'array');

        $this->context->smarty->assign([
            'displayName' => $module->displayName,
            'image' => $module->image,
            'nb_rates' => (int) $module->nb_rates[0],
            'avg_rate' => (int) $module->avg_rate[0],
            'badges' => $module->badges,
            'compatibility' => $module->compatibility,
            'description_full' => $module->description_full,
            'additional_description' => $module->additional_description,
            'is_addons_partner' => (isset($module->type) && ($module->type == 'addonsPartner' || $module->type == 'addonsNative')),
            'url' => $url,
            'price' => $module->price,
            'options' => $module->optionsHtml,
            'installed' => (bool) $module->installed,
        ]);
    }

    public function ajaxProcessGetModuleQuickView()
    {
        $this->assignReadMoreSmartyVar();

        $this->smartyOutputContent('controllers/modules/quickview.tpl');
    }

    public function ajaxProcessGetModuleReadMoreView()
    {
        $this->assignReadMoreSmartyVar();

        die(json_encode([
            'header' => $this->context->smarty->fetch('controllers/modules/readmore-header.tpl'),
            'body' => $this->context->smarty->fetch('controllers/modules/readmore-body.tpl'),
        ]));
    }
}
