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
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class AdminModulesControllerCore extends AdminController
{
    /*
    ** @var array map with $_GET keywords and their callback
    */
    protected $map = [
        'configure' => 'getContent',
    ];

    protected $id_employee;

    public $bootstrap = true;

    /**
     * Admin Modules Controller Constructor
     * Init list modules categories
     * Load id employee
     * Load filter configuration
     * Load cache file.
     */
    public function __construct()
    {
        parent::__construct();

        // Rely on new module controller for right management
        $this->id = Tab::getIdFromClassName('AdminModulesSf');
        $this->template = 'content-legacy.tpl';

        register_shutdown_function('displayFatalError');

        // Set Id Employee, Iso Default Country and Filter Configuration
        $this->id_employee = (int) $this->context->employee->id;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryPlugin(['autocomplete', 'fancybox', 'tablefilter']);
    }

    /**
     * Get current URL
     *
     * @param array $remove List of keys to remove from URL
     * @return string
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

    public function postProcessCallback()
    {
        $return = false;

        if (!Tools::getValue('configure')) {
            return;
        }

        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            $this->errors[] = $this->trans('This functionality has been disabled.', [], 'Admin.Notifications.Error');

            return;
        }

        $modules = Tools::getValue('configure');
        if (!empty($modules)) {
            if (strpos($modules, '|')) {
                $modules_list_save = $modules;
                $modules = explode('|', $modules);
            }
        }

        if (!is_array($modules)) {
            $modules = (array) $modules;
        }

        $module_errors = [];
        foreach ($modules as $name) {
            $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
            $moduleManager = $moduleManagerBuilder->build();

            // Check potential error
            if (!($module = Module::getInstanceByName(urldecode($name)))) {
                $this->errors[] = $this->trans('Module not found');
            } elseif (!$this->access('edit') || !$module->getPermission('configure') || !$moduleManager->isInstalled(urldecode($name))) {
                $this->errors[] = $this->trans('You do not have permission to configure this module.', [], 'Admin.Modules.Notification');
            } else {
                //retrocompatibility
                if (Tools::getValue('controller') != '') {
                    $_POST['tab'] = Tools::safeOutput(Tools::getValue('controller'));
                }

                // We check if method of module exists
                if (!method_exists($module, 'getContent')) {
                    throw new PrestaShopException('Method of module cannot be found');
                }

                if (count($this->errors)) {
                    continue;
                }
                // Get the return value of current method
                $echo = $module->getContent();

                // we show the html code of configure page
                if ($moduleManager->isInstalled($module->name)) {
                    $this->buildModuleConfigurationPage($module, $echo);
                } elseif ($echo === true) {
                    $return = 13;
                } elseif ($echo === false) {
                    $module_errors[] = ['name' => $name, 'message' => $module->getErrors()];
                }

                if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL && isset(Context::getContext()->tmpOldShop)) {
                    Context::getContext()->shop = clone Context::getContext()->tmpOldShop;
                    unset(Context::getContext()->tmpOldShop);
                }
            }
        }


        if (count($module_errors)) {
            // If error during module installation, no redirection
            $html_error = $this->generateHtmlMessage($module_errors);
            $this->errors[] = $this->trans('The following module(s) could not be installed properly: %s.', [$html_error], 'Admin.Modules.Notification');
            $this->context->smarty->assign('error_module', 'true');
        }

        if ($return) {
            if (isset($module)) {
                Tools::redirectAdmin(self::$currentIndex . '&conf=' . $return . '&token=' . $this->token . '&tab_module=' . $module->tab . '&module_name=' . $module->name . '&anchor=' . ucfirst($module->name) . (isset($modules_list_save) ? '&modules_list=' . $modules_list_save : '') . $params);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkAccess()
    {
        if (!parent::checkAccess()) {
            return false;
        }

        // unless we're configuring a module, redirect to module manager
        // configure + module_name = module configuration page
        // configure + generate_rtl = generte RTL stylesheets
        if (!Tools::getIsset('configure') && (!Tools::getIsset('module_name') || !Tools::getIsset('generate_rtl'))) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModulesSf'));
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function postProcess()
    {
        // Parent Post Process
        parent::postProcess();

        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();

        // If redirect parameter is present and module already installed, we redirect on configuration module page
        if (Tools::getValue('redirect') == 'config' && Tools::getValue('module_name') != '' && $moduleManager->isInstalled(pSQL(Tools::getValue('module_name')))) {
            Tools::redirectAdmin('index.php?controller=adminmodules&configure=' . Tools::getValue('module_name') . '&token=' . Tools::getValue('token') . '&module_name=' . Tools::getValue('module_name'));
        }

        // Call appropriate module callback
        $this->postProcessCallback();

        if (Tools::getValue('generate_rtl')) {
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

    /**
     * @param Module $module
     * @param string $echo
     *
     * @return void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     */
    protected function buildModuleConfigurationPage(Module $module, string $echo): void
    {
        $this->bootstrap = (bool) $module->bootstrap;
        if (isset($module->multishop_context)) {
            $this->multishop_context = $module->multishop_context;
        }

        $back_link = self::$currentIndex . '&token=' . $this->token . '&tab_module=' . $module->tab . '&module_name=' . $module->name;
        $hook_link = 'index.php?tab=AdminModulesPositions&token='
            . Tools::getAdminTokenLite(
                'AdminModulesPositions'
            ) . '&show_modules=' . (int) $module->id;
        $trad_link = 'index.php?tab=AdminTranslations&token='
            . Tools::getAdminTokenLite(
                'AdminTranslations'
            ) . '&type=modules&lang=';
        $rtl_link = 'index.php?tab=AdminModules&token='
            . Tools::getAdminTokenLite(
                'AdminModules'
            ) . '&configure=' . $module->name . '&generate_rtl=1';

        $this->context->smarty->assign([
            'module_name' => $module->name,
            'module_display_name' => $module->displayName,
            'back_link' => $back_link,
            'module_hook_link' => $hook_link,
            'trad_link' => $trad_link,
            'module_rtl_link' => ($this->context->language->is_rtl ? $rtl_link : null),
            'module_languages' => Language::getLanguages(false),
            'theme_language_dir' => _THEME_LANG_DIR_,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
            'add_permission' => $this->access('add'),
        ]);

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

        if (Shop::isFeatureActive()
            && isset(Context::getContext()->tmpOldShop)
        ) {
            Context::getContext()->shop = clone Context::getContext()->tmpOldShop;
            unset(Context::getContext()->tmpOldShop);
        }

        // Display module configuration
        $header = $this->context->smarty->fetch('controllers/modules/configure.tpl');
        $configuration_bar = $this->context->smarty->fetch('controllers/modules/configuration_bar.tpl');

        $output = $header . $echo;

        $this->context->smarty->assign('module_content', $output . $configuration_bar);
    }
}
