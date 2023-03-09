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
     *
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

    /**
     * {@inheritdoc}
     */
    public function checkAccess()
    {
        if (!parent::checkAccess()) {
            return false;
        }

        // only accept configuring a module
        if (Tools::getIsset('configure')) {
            return true;
        }

        // redirect to module manager
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminModulesSf'));

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function postProcess()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            $this->errors[] = $this->trans('This functionality has been disabled.', [], 'Admin.Notifications.Error');

            return;
        }

        parent::postProcess();

        $moduleName = Tools::getValue('configure');
        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();

        // Check potential error
        if (!$moduleManager->isInstalled($moduleName) || !($module = Module::getInstanceByName($moduleName))) {
            $this->errors[] = $this->trans(
                'The module "%modulename%" cannot be found',
                ['%modulename%' => $moduleName],
                'Admin.Modules.Notification'
            );
        } elseif (!$this->access('edit') || !$module->getPermission('configure')) {
            $this->errors[] = $this->trans(
                'You do not have permission to configure this module.',
                [],
                'Admin.Modules.Notification'
            );
        } else {
            // build RTL assets
            if (Tools::getValue('generate_rtl')) {
                $this->buildRtlAssets($module);

                return;
            }

            // enable/disable
            if (($enable = ('1' === Tools::getValue('enable'))) || '1' === Tools::getValue('disable')) {
                $this->toggleActiveStatus($module, $enable);

                return;
            }

            // show module configuration page
            $this->buildModuleConfigurationPage($module);
        }
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
     * Toggles the module enabled or disabled
     *
     * @param Module $module
     * @param bool $enabled
     *
     * @return void
     */
    protected function toggleActiveStatus(Module $module, bool $enabled): void
    {
        try {
            $success = ($enabled) ? $module->enable() : $module->disable();
        } catch (Exception $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }

        if ($success) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true, [], ['configure' => $module->name, 'conf' => 6]));

            return;
        }

        if (!isset($errorMessage)) {
            $moduleErrors = $module->getErrors();
            if (!empty($moduleErrors)) {
                $errorMessage = implode('; ', $moduleErrors);
            } else {
                $errorMessage = $this->trans('Unfortunately, the module did not return additional details.', [], 'Admin.Modules.Notifications');
            }
        }

        $params = ['%module%' => $module->name, '%error_details%' => $errorMessage];
        $this->errors[] = ($enabled)
            ? $this->trans('Error when enabling module %module%. %error_details%.', $params, 'Admin.Modules.Notifications')
            : $this->trans('Error when disabling module %module%. %error_details%.', $params, 'Admin.Modules.Notifications');
    }

    /**
     * @return void
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     */
    protected function buildModuleConfigurationPage(Module $module): void
    {
        // retrocompatibility
        if (Tools::getValue('controller') != '') {
            $_POST['tab'] = Tools::safeOutput(Tools::getValue('controller'));
        }

        // We check if method of module exists
        if (!method_exists($module, 'getContent')) {
            throw new PrestaShopException(sprintf('Module %s has no getContent() method', $module->name));
        }

        // Get the page content
        $content = $module->getContent();

        $this->bootstrap = (bool) $module->bootstrap;
        if (isset($module->multishop_context)) {
            $this->multishop_context = $module->multishop_context;
        }

        $link = Context::getContext()->link;
        $back_link = self::$currentIndex . '&token=' . $this->token . '&tab_module=' . $module->tab . '&module_name=' . $module->name;
        $hook_link = $link->getAdminLink('AdminModulesPositions', true, [], ['show_modules' => (int) $module->id]);
        $trad_link = $link->getAdminLink('AdminTranslations', true, [], ['type' => 'modules', 'lang=' => '']);
        $rtl_link = $link->getAdminLink('AdminModules', true, [], ['configure' => $module->name, 'generate_rtl' => 1]);

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
                'shop_context' => $shop_context,
                'multishop_enable_url' => $link->getAdminLink('AdminModules', true, [], ['configure' => $module->name, 'enable' => 1]),
                'multishop_disable_url' => $link->getAdminLink('AdminModules', true, [], ['configure' => $module->name, 'disable' => 1]),
            ]);

            $configuration_bar = $this->context->smarty->fetch('controllers/modules/configuration_bar.tpl');
        } else {
            $configuration_bar = '';
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

        $this->context->smarty->assign('module_content', $header . $content . $configuration_bar);
    }

    /**
     * Builds RTL assets for the module & redirects to a success page
     *
     * @return void
     *
     * @throws \PrestaShop\PrestaShop\Core\Localization\RTL\Exception\GenerationException
     */
    protected function buildRtlAssets(Module $module): void
    {
        Language::getRtlStylesheetProcessor()
            ->setProcessPaths([
                _PS_MODULE_DIR_ . $module->name,
            ])
            ->process();

        Tools::redirectAdmin(
            $this->context->link->getAdminLink(
                'AdminModules',
                true,
                [],
                [
                    'configure' => Tools::getValue('configure'),
                    // @see AdminController::_conf
                    'conf' => 6,
                ]
            )
        );
    }
}
