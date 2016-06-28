<?php

namespace PrestaShopBundle\Controller\Admin;

use Exception;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterStatus;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterType;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShopBundle\Entity\ModuleHistory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;

class ModuleController extends FrameworkBundleAdminController
{
    /**
     * Controller responsible for displaying "Catalog" section of Module management pages
     * @return Response
     */
    public function catalogAction()
    {
        $translator = $this->container->get('translator');

        return $this->render('PrestaShopBundle:Admin/Module:catalog.html.twig', array(
                'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
                'layoutTitle' => $translator->trans('Modules & Services', array(), 'Admin.Navigation.Menu'),
                'requireAddonsSearch' => true,
                'requireBulkActions' => false,
                'showContentHeader' => true,
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink('AdminModules'),
                'requireFilterStatus' => false,
            ));
    }

    /**
     * Controller responsible for displaying "Catalog Module Grid" section of Module management pages with ajax
     * @param  Request $request
     * @return Response
     */
    public function refreshCatalogAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            // Bad request
            return new Response('', 400);
        }

        $modulesProvider = $this->get('prestashop.core.admin.data_provider.module_interface');
        $translator = $this->get('translator');
        $moduleRepository = $this->get('prestashop.core.admin.module.repository');
        $responseArray = [];

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE | AddonListFilterType::SERVICE)
            ->setStatus(~ AddonListFilterStatus::INSTALLED);

        try {
            $products = $modulesProvider->generateAddonsUrls(
                $moduleRepository->getFilteredList($filters)
            );
            shuffle($products);
            $responseArray['domElements'][] = $this->constructJsonCatalogCategoriesMenuResponse($modulesProvider, $products);
            $responseArray['domElements'][] = $this->constructJsonCatalogBodyResponse($modulesProvider, $products);
            $responseArray['status'] = true;
        } catch (Exception $e) {
            $responseArray['msg'] = $translator->trans(
                'Cannot get catalog data, please try again later. Reason: '.
                print_r($e->getMessage(), true),
                array(),
                'AdminModules'
            );
            $responseArray['status'] = false;
        }

        return new JsonResponse($responseArray, 200);
    }

    private function constructJsonCatalogBodyResponse($modulesProvider, $products)
    {
        $products = $modulesProvider->generateAddonsUrls($products);
        $formattedContent = [];
        $formattedContent['selector'] = '.module-catalog-page';
        $formattedContent['content'] = $this->render(
            'PrestaShopBundle:Admin/Module/Includes:sorting.html.twig',
            [
                'totalModules' => count($products)
            ]
        )->getContent();
        $formattedContent['content'] .= $this->render(
            'PrestaShopBundle:Admin/Module/Includes:grid.html.twig',
            [
                'modules' => $this->getPresentedProducts($products),
                'requireAddonsSearch' => true
            ]
        )->getContent();

        return $formattedContent;
    }

    private function constructJsonCatalogCategoriesMenuResponse($modulesProvider, $products)
    {
        $formattedContent = [];
        $formattedContent['selector'] = '.module-menu-item';
        $formattedContent['content'] = $this->render(
            'PrestaShopBundle:Admin/Module/Includes:dropdown_categories.html.twig',
            [
                'topMenuData' => $this->getTopMenuData($modulesProvider->getCategoriesFromModules($products))
            ]
        )->getContent();

        return $formattedContent;
    }

    public function manageAction()
    {
        $translator = $this->get('translator');
        $modulesProvider = $this->get('prestashop.core.admin.data_provider.module_interface');
        $shopService = $this->get('prestashop.adapter.shop.context');
        $moduleRepository = $this->get('prestashop.core.admin.module.repository');
        $themeRepository = $this->get('prestashop.core.admin.theme.repository');

        // Retrieve current shop
        $shopID = $shopService->getContextShopID();
        $shops = $shopService->getShops();
        $shop = $shops[$shopID];
        $currentTheme = $themeRepository->getInstanceByName($shop['theme_name']);
        $modulesTheme = $currentTheme->getModulesToEnable();

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE | AddonListFilterType::SERVICE)
            ->removeStatus(AddonListFilterStatus::UNINSTALLED);
        $installed_products = $moduleRepository->getFilteredList($filters);

        $products = new \stdClass();
        foreach (['native_modules', 'theme_bundle', 'modules'] as $subpart) {
            $products->{$subpart} = [];
        }

        foreach ($installed_products as $installed_product) {
            if (in_array($installed_product->attributes->get('name'), $modulesTheme)) {
                $row = 'theme_bundle';
            } elseif ($installed_product->attributes->has('origin') && $installed_product->attributes->get('origin') === 'native' && $installed_product->attributes->get('author') === 'PrestaShop') {
                $row = 'native_modules';
            } else {
                $row= 'modules';
            }
            $products->{$row}[] = (object)$installed_product;
        }

        foreach ($products as $product_label => $products_part) {
            $products->{$product_label} = $modulesProvider->generateAddonsUrls($products_part);
            $products->{$product_label} = $this->getPresentedProducts($products_part);
        }

        return $this->render('PrestaShopBundle:Admin/Module:manage.html.twig', array(
                'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
                'layoutTitle' => $translator->trans('Manage my modules', array(), 'Admin.Modules.Feature'),
                'modules' => $products,
                'topMenuData' => $this->getTopMenuData($modulesProvider->getCategoriesFromModules($installed_products)),
                'requireAddonsSearch' => false,
                'requireBulkActions' => true,
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink('AdminModules'),
                'requireFilterStatus' => true,
            ));
    }

    public function moduleAction(Request $request)
    {
        $action = $request->attributes->get('action');
        $module = $request->attributes->get('module_name');
        $forceDeletion = $request->query->has('deletion');

        $moduleManager = $this->get('prestashop.module.manager');
        $moduleRepository = $this->get('prestashop.core.admin.module.repository');
        $modulesProvider = $this->get('prestashop.core.admin.data_provider.module_interface');
        $translator = $this->get('translator');

        $response = array();
        if (method_exists($moduleManager, $action)) {
            // ToDo : Check if allowed to call this action
            try {
                if ($action == "uninstall") {
                    $response[$module]['status'] = $moduleManager->{$action}($module, $forceDeletion);
                } else {
                    $response[$module]['status'] = $moduleManager->{$action}($module);
                }

                if ($response[$module]['status'] === null) {
                    $response[$module]['status'] = false;
                    $response[$module]['msg'] = $translator->trans(
                        '%module% did not return a valid response on %action% action.',
                        array(
                            '%module%' => $module,
                            '%action%' => $action),
                        'Admin.Notifications.Error'
                        );
                } elseif ($response[$module]['status'] === false) {
                    $error = $moduleManager->getError($module);
                    $response[$module]['msg'] = $translator->trans(
                        'Cannot %action% module %module%. %error_details%',
                        array(
                            '%action%' => str_replace('_', ' ', $action),
                            '%module%' => $module,
                            '%error_details%' => $error),
                        'Admin.Notifications.Error'
                    );
                } else {
                    $response[$module]['msg'] = $translator->trans(
                        '%action% action on module %module% succeeded.',
                        array(
                            '%action%' => ucfirst(str_replace('_', ' ', $action)),
                            '%module%' => $module),
                        'Admin.Notifications.Success'
                    );
                }
            } catch (Exception $e) {
                $response[$module]['status'] = false;
                $response[$module]['msg'] = $translator->trans(
                    'Exception thrown by addon %module% on %action%. %error_details%',
                    array(
                            '%action%' => str_replace('_', ' ', $action),
                            '%module%' => $module,
                            '%error_details%' => $e->getMessage()),
                    'Admin.Notifications.Error'
                );

                $logger = $this->get('logger');
                $logger->error($response[$module]['msg']);
            }
        } else {
            $response[$module]['status'] = false;
            $response[$module]['msg'] = $translator->trans(
                'Invalid action',
                array(),
                'Admin.Notifications.Error'
            );
        }

        if ($request->isXmlHttpRequest()) {
            if ($response[$module]['status'] === true && $action != 'uninstall') {
                $moduleInstance = $moduleRepository->getModule($module);
                $moduleInstanceWithUrl = $modulesProvider->generateAddonsUrls(array($moduleInstance));
                $response[$module]['action_menu_html'] = $this->render('PrestaShopBundle:Admin/Module/Includes:action_menu.html.twig', array(
                        'module' => $this->getPresentedProducts($moduleInstanceWithUrl)[0],
                    ))->getContent();
            }

            return new JsonResponse($response, 200);
        }

        // We need a better error handler here. Meanwhile, I throw an exception
        if (!$response[$module]['status']) {
            $this->addFlash('error', $response[$module]['msg']);
        } else {
            $this->addFlash('success', $response[$module]['msg']);
        }

        if ($request->server->get('HTTP_REFERER')) {
            return $this->redirect($request->server->get('HTTP_REFERER'));
        } else {
            return $this->redirect($this->generateUrl('admin_module_catalog'));
        }
    }

    public function notificationAction()
    {
        $translator = $this->get('translator');
        $modulesProvider = $this->get('prestashop.core.admin.data_provider.module_interface');

        $moduleRepository = $this->get('prestashop.core.admin.module.repository');

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE | AddonListFilterType::SERVICE)
            ->setStatus(AddonListFilterStatus::INSTALLED);
        $installed_products = $moduleRepository->getFilteredList($filters);

        $products = new \stdClass();
        foreach (array('to_configure', 'to_update') as $subpart) {
            $products->{$subpart} = [];
        }

        foreach ($installed_products as $installed_product) {
            $warnings = $installed_product->attributes->get('warning');
            if (!empty($warnings)) {
                $row = 'to_configure';
            } elseif ($installed_product->database->get('installed') == 1 && $installed_product->database->get('version') !== 0 && version_compare($installed_product->database->get('version'), $installed_product->attributes->get('version'), '<')) {
                $row = 'to_update';
            } else {
                $row = false;
            }

            if ($row) {
                $products->{$row}[] = (object)$installed_product;
            }
        }

        foreach ($products as $product_label => $products_part) {
            $products->{$product_label} = $modulesProvider->generateAddonsUrls($products_part);
            $products->{$product_label} = $this->getPresentedProducts($products_part);
        }

        return $this->render('PrestaShopBundle:Admin/Module:notifications.html.twig', array(
                'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
                'layoutTitle' => $translator->trans('Module notifications', array(), 'Admin.Modules.Feature'),
                'modules' => $products,
                'requireAddonsSearch' => false,
                'requireBulkActions' => false,
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink('AdminModules'),
                'requireFilterStatus' => false,
        ));
    }

    public function getPreferredModulesAction(Request $request)
    {
        $tabModulesList = $request->request->get('tab_modules_list');
        $back = $request->request->get('back_tab_modules_list');

        if ($back) {
            $back .= '&tab_modules_open=1';
        }
        if ($tabModulesList) {
            $tabModulesList = explode(',', $tabModulesList);
            $modulesListUnsorted = $this->getModulesByInstallation($tabModulesList, $request->request->get('admin_list_from_source'));
        }

        $installed = $uninstalled = array();

        foreach ($tabModulesList as $key => $value) {
            $continue = 0;
            foreach ($modulesListUnsorted['installed'] as $moduleInstalled) {
                if ($moduleInstalled->name == $value) {
                    $continue = 1;
                    $installed[] = $moduleInstalled;
                }
            }
            if ($continue) {
                continue;
            }
            foreach ($modulesListUnsorted['not_installed'] as $moduleNotinstalled) {
                if ($moduleNotinstalled->name == $value) {
                    $uninstalled[] = $moduleNotinstalled;
                }
            }
        }

        $moduleListSorted = array(
            'installed' => $installed,
            'notInstalled' => $uninstalled
        );

        $twigParams = array(
            'currentIndex' => '',
            'modulesList' => $moduleListSorted
        );

        if ($request->request->has('admin_list_from_source')) {
            $twigParams['adminListFromSource'] = $request->request->get('admin_list_from_source');
        }

        return $this->render('PrestaShopBundle:Admin/Module:tab-modules-list.html.twig', $twigParams);
    }

    protected function getModulesByInstallation($tab_modules_list = null, $install_source_tracking = false)
    {
        $addonsProvider = $this->get('prestashop.core.admin.data_provider.addons_interface');
        $listModulesPartners = [];

        $all_modules = \Module::getModulesOnDisk(true, $addonsProvider->isAddonsAuthenticated(), $this->getContext()->employee->id);
        $all_unik_modules = array();
        $modules_list = array('installed' =>array(), 'not_installed' => array());

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
                    $perm &= \Module::getPermissionStatic($module->id, 'configure');
                } else {
                    $id_admin_module = \Tab::getIdFromClassName('AdminModules');
                    $access = \Profile::getProfileAccess($this->getContext()->employee->id_profile, $id_admin_module);
                    if (!$access['edit']) {
                        $perm &= false;
                    }
                }

                if (in_array($module->name, $listModulesPartners)) {
                    $module->type = 'addonsPartner';
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

    /**
     * @param Module $module
     * @param string $output_type
     * @param string|null $back
     * @param string|bool $install_source_tracking
     */
    public function fillModuleData(&$module, $output_type = 'link', $back = null, $install_source_tracking = false)
    {
        $translator = $this->get('translator');
        /** @var Module $obj */
        $obj = null;
        if ($module->onclick_option) {
            $obj = new $module->name();
        }
        // Fill module data
        $module->logo = '../../img/questionmark.png';

        if (@filemtime(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.basename(_PS_MODULE_DIR_).DIRECTORY_SEPARATOR.$module->name
            .DIRECTORY_SEPARATOR.'logo.gif')) {
            $module->logo = 'logo.gif';
        }
        if (@filemtime(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.basename(_PS_MODULE_DIR_).DIRECTORY_SEPARATOR.$module->name
            .DIRECTORY_SEPARATOR.'logo.png')) {
            $module->logo = 'logo.png';
        }

        $link_admin_modules = $this->getContext()->link->getAdminLink('AdminModules', true);

        $module->options['install_url'] = $link_admin_modules.'&install='.urlencode($module->name).'&tab_module='.$module->tab.'&module_name='.$module->name
            .'&anchor='.ucfirst($module->name).($install_source_tracking ? '&source='.$install_source_tracking : '');
        $module->options['update_url'] = $link_admin_modules.'&update='.urlencode($module->name).'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor='.ucfirst($module->name);
        $module->options['uninstall_url'] = $link_admin_modules.'&uninstall='.urlencode($module->name).'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor='.ucfirst($module->name);

        // free modules get their source tracking data here
        $module->optionsHtml = $this->displayModuleOptions($module, $output_type, $back, $install_source_tracking);
        // pay modules get their source tracking data here
        if ($install_source_tracking && isset($module->addons_buy_url)) {
            $module->addons_buy_url .= ($install_source_tracking ? '&utm_term='.$install_source_tracking : '');
        }

        $module->options['uninstall_onclick'] = ((!$module->onclick_option) ?
            ((empty($module->confirmUninstall)) ? 'return confirm(\''.$translator->trans('Do you really want to uninstall this module?').'\');' : 'return confirm(\''.addslashes($module->confirmUninstall).'\');') :
            $obj->onclickOption('uninstall', $module->options['uninstall_url']));

        if ((\Tools::getValue('module_name') == $module->name || in_array($module->name, explode('|', \Tools::getValue('modules_list')))) && (int)\Tools::getValue('conf') > 0) {
            $module->message = $this->_conf[(int)\Tools::getValue('conf')];
        }

        if ((\Tools::getValue('module_name') == $module->name || in_array($module->name, explode('|', \Tools::getValue('modules_list')))) && (int)\Tools::getValue('conf') > 0) {
            unset($obj);
        }

        if (!empty($module->image) && false !== strpos($module->image, '../img')) {
            $module->image_absolute = str_replace('../', _PS_BASE_URL_.__PS_BASE_URI__, $module->image);
        }
    }

    /**
     * Display modules list
     *
     * @param Module $module
     * @param string $output_type (link or select)
     * @param string|null $back
     * @param string|bool $install_source_tracking
     * @return string|array
     */
    public function displayModuleOptions($module, $output_type = 'link', $back = null, $install_source_tracking = false)
    {
        $translator = $this->get('translator');
        if (!isset($module->enable_device)) {
            $module->enable_device = \Context::DEVICE_COMPUTER | \Context::DEVICE_TABLET | \Context::DEVICE_MOBILE;
        }

        $this->translationsTab['confirm_uninstall_popup'] = (isset($module->confirmUninstall) ? $module->confirmUninstall : $translator->trans('Do you really want to uninstall this module?'));
        if (!isset($this->translationsTab['Disable this module'])) {
            $this->translationsTab['Disable this module'] = $translator->trans('Disable this module');
            $this->translationsTab['Enable this module for all shops'] = $translator->trans('Enable this module for all shops');
            $this->translationsTab['Disable'] = $translator->trans('Disable');
            $this->translationsTab['Enable'] = $translator->trans('Enable');
            $this->translationsTab['Disable on mobiles'] = $translator->trans('Disable on mobiles');
            $this->translationsTab['Disable on tablets'] = $translator->trans('Disable on tablets');
            $this->translationsTab['Disable on computers'] = $translator->trans('Disable on computers');
            $this->translationsTab['Display on mobiles'] = $translator->trans('Display on mobiles');
            $this->translationsTab['Display on tablets'] = $translator->trans('Display on tablets');
            $this->translationsTab['Display on computers'] = $translator->trans('Display on computers');
            $this->translationsTab['Reset'] = $translator->trans('Reset');
            $this->translationsTab['Configure'] = $translator->trans('Configure');
            $this->translationsTab['Delete'] = $translator->trans('Delete');
            $this->translationsTab['Install'] = $translator->trans('Install');
            $this->translationsTab['Uninstall'] = $translator->trans('Uninstall');
            $this->translationsTab['Would you like to delete the content related to this module ?'] = $translator->trans('Would you like to delete the content related to this module ?');
            $this->translationsTab['This action will permanently remove the module from the server. Are you sure you want to do this?'] = $translator->trans('This action will permanently remove the module from the server. Are you sure you want to do this?');
            $this->translationsTab['Remove from Favorites'] = $translator->trans('Remove from Favorites');
            $this->translationsTab['Mark as Favorite'] = $translator->trans('Mark as Favorite');
        }

        $link_admin_modules = $this->getContext()->link->getAdminLink('AdminModules', true);
        $modules_options = array();

        $configure_module = array(
            'href' => $link_admin_modules.'&configure='.urlencode($module->name).'&tab_module='.$module->tab.'&module_name='.urlencode($module->name),
            'onclick' => $module->onclick_option && isset($module->onclick_option_content['configure']) ? $module->onclick_option_content['configure'] : '',
            'title' => '',
            'text' => $this->translationsTab['Configure'],
            'cond' => $module->id && isset($module->is_configurable) && $module->is_configurable,
            'icon' => 'wrench',
        );

        $desactive_module = array(
            'href' => $link_admin_modules.'&module_name='.urlencode($module->name).'&'.($module->active ? 'enable=0' : 'enable=1').'&tab_module='.$module->tab,
            'onclick' => $module->active && $module->onclick_option && isset($module->onclick_option_content['desactive']) ? $module->onclick_option_content['desactive'] : '' ,
            'title' => \Shop::isFeatureActive() ? htmlspecialchars($module->active ? $this->translationsTab['Disable this module'] : $this->translationsTab['Enable this module for all shops']) : '',
            'text' => $module->active ? $this->translationsTab['Disable'] : $this->translationsTab['Enable'],
            'cond' => $module->id,
            'icon' => 'off',
        );
        $link_reset_module = $link_admin_modules.'&module_name='.urlencode($module->name).'&reset&tab_module='.$module->tab;

        $is_reset_ready = false;
        if (\Validate::isModuleName($module->name)) {
            if (method_exists(\Module::getInstanceByName($module->name), 'reset')) {
                $is_reset_ready = true;
            }
        }

        $reset_module = array(
            'href' => $link_reset_module,
            'onclick' => $module->onclick_option && isset($module->onclick_option_content['reset']) ? $module->onclick_option_content['reset'] : '',
            'title' => '',
            'text' => $this->translationsTab['Reset'],
            'cond' => $module->id && $module->active,
            'icon' => 'undo',
            'class' => ($is_reset_ready ? 'reset_ready' : '')
        );

        $delete_module = array(
            'href' => $link_admin_modules.'&delete='.urlencode($module->name).'&tab_module='.$module->tab.'&module_name='.urlencode($module->name),
            'onclick' => $module->onclick_option && isset($module->onclick_option_content['delete']) ? $module->onclick_option_content['delete'] : 'return confirm(\''.$this->translationsTab['This action will permanently remove the module from the server. Are you sure you want to do this?'].'\');',
            'title' => '',
            'text' => $this->translationsTab['Delete'],
            'cond' => true,
            'icon' => 'trash',
            'class' => 'text-danger'
        );

        $display_mobile = array(
            'href' => $link_admin_modules.'&module_name='.urlencode($module->name).'&'.($module->enable_device & \Context::DEVICE_MOBILE ? 'disable_device' : 'enable_device').'='.\Context::DEVICE_MOBILE.'&tab_module='.$module->tab,
            'onclick' => '',
            'title' => htmlspecialchars($module->enable_device & \Context::DEVICE_MOBILE ? $this->translationsTab['Disable on mobiles'] : $this->translationsTab['Display on mobiles']),
            'text' => $module->enable_device & \Context::DEVICE_MOBILE ? $this->translationsTab['Disable on mobiles'] : $this->translationsTab['Display on mobiles'],
            'cond' => $module->id,
            'icon' => 'mobile'
        );

        $display_tablet = array(
            'href' => $link_admin_modules.'&module_name='.urlencode($module->name).'&'.($module->enable_device & \Context::DEVICE_TABLET ? 'disable_device' : 'enable_device').'='.\Context::DEVICE_TABLET.'&tab_module='.$module->tab,
            'onclick' => '',
            'title' => htmlspecialchars($module->enable_device & \Context::DEVICE_TABLET ? $this->translationsTab['Disable on tablets'] : $this->translationsTab['Display on tablets']),
            'text' => $module->enable_device & \Context::DEVICE_TABLET ? $this->translationsTab['Disable on tablets'] : $this->translationsTab['Display on tablets'],
            'cond' => $module->id,
            'icon' => 'tablet'
        );

        $display_computer = array(
            'href' => $link_admin_modules.'&module_name='.urlencode($module->name).'&'.($module->enable_device & \Context::DEVICE_COMPUTER ? 'disable_device' : 'enable_device').'='.\Context::DEVICE_COMPUTER.'&tab_module='.$module->tab,
            'onclick' => '',
            'title' => htmlspecialchars($module->enable_device & \Context::DEVICE_COMPUTER ? $this->translationsTab['Disable on computers'] : $this->translationsTab['Display on computers']),
            'text' => $module->enable_device & \Context::DEVICE_COMPUTER ? $this->translationsTab['Disable on computers'] : $this->translationsTab['Display on computers'],
            'cond' => $module->id,
            'icon' => 'desktop'
        );

        $install = array(
            'href' => $link_admin_modules.'&install='.urlencode($module->name).'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor='.ucfirst($module->name)
                .(!is_null($back) ? '&back='.urlencode($back) : '').($install_source_tracking ? '&source='.$install_source_tracking : ''),
            'onclick' => '',
            'title' => $this->translationsTab['Install'],
            'text' => $this->translationsTab['Install'],
            'cond' => $module->id,
            'icon' => 'plus-sign-alt'
        );

        $uninstall = array(
            'href' => $link_admin_modules.'&uninstall='.urlencode($module->name).'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor='.ucfirst($module->name).(!is_null($back) ? '&back='.urlencode($back) : ''),
            'onclick' => (isset($module->onclick_option_content['uninstall']) ? $module->onclick_option_content['uninstall'] : 'return confirm(\''.$this->translationsTab['confirm_uninstall_popup'].'\');'),
            'title' => $this->translationsTab['Uninstall'],
            'text' => $this->translationsTab['Uninstall'],
            'cond' => $module->id,
            'icon' => 'minus-sign-alt'
        );

        $remove_from_favorite = array(
            'href' => '#',
            'class' => 'action_unfavorite toggle_favorite',
            'onclick' =>'',
            'title' => $this->translationsTab['Remove from Favorites'],
            'text' => $this->translationsTab['Remove from Favorites'],
            'cond' => $module->id,
            'icon' => 'star',
            'data-value' => '0',
            'data-module' => $module->name
        );

        $mark_as_favorite = array(
            'href' => '#',
            'class' => 'action_favorite toggle_favorite',
            'onclick' => '',
            'title' => $this->translationsTab['Mark as Favorite'],
            'text' => $this->translationsTab['Mark as Favorite'],
            'cond' => $module->id,
            'icon' => 'star',
            'data-value' => '1',
            'data-module' => $module->name
        );

        $update = array(
            'href' => $module->options['update_url'],
            'onclick' => '',
            'title' => 'Update it!',
            'text' => 'Update it!',
            'icon' => 'refresh',
            'cond' => $module->id,
        );

        $divider = array(
            'href' => '#',
            'onclick' => '',
            'title' => 'divider',
            'text' => 'divider',
            'cond' => $module->id,
        );

        if (isset($module->version_addons) && $module->version_addons) {
            $modules_options[] = $update;
        }

        if ($module->active) {
            $modules_options[] = $configure_module;
            $modules_options[] = $desactive_module;
            $modules_options[] = $display_mobile;
            $modules_options[] = $display_tablet;
            $modules_options[] = $display_computer;
        } else {
            $modules_options[] = $desactive_module;
            $modules_options[] = $configure_module;
        }

        $modules_options[] = $reset_module;

        if ($output_type == 'select') {
            if (!$module->id) {
                $modules_options[] = $install;
            } else {
                $modules_options[] = $uninstall;
            }
        } elseif ($output_type == 'array') {
            if ($module->id) {
                $modules_options[] = $uninstall;
            }
        }

        if (isset($module->preferences) && isset($module->preferences['favorite']) && $module->preferences['favorite'] == 1) {
            $remove_from_favorite['style'] = '';
            $mark_as_favorite['style'] = 'display:none;';
            $modules_options[] = $remove_from_favorite;
            $modules_options[] = $mark_as_favorite;
        } else {
            $mark_as_favorite['style'] = '';
            $remove_from_favorite['style'] = 'display:none;';
            $modules_options[] = $remove_from_favorite;
            $modules_options[] = $mark_as_favorite;
        }

        if ($module->id == 0) {
            $install['cond'] = 1;
            $install['flag_install'] = 1;
            $modules_options[] = $install;
        }
        $modules_options[] = $divider;
        $modules_options[] = $delete_module;

        $return = '';
        foreach ($modules_options as $option_name => $option) {
            if ($option['cond']) {
                if ($output_type == 'link') {
                    $return .= '<li><a class="'.$option_name.' action_module';
                    $return .= '" href="'.$option['href'].(!is_null($back) ? '&back='.urlencode($back) : '').'"';
                    $return .= ' onclick="'.$option['onclick'].'"  title="'.$option['title'].'"><i class="icon-'.(isset($option['icon']) && $option['icon'] ? $option['icon']:'cog').'"></i>&nbsp;'.$option['text'].'</a></li>';
                } elseif ($output_type == 'array') {
                    if (!is_array($return)) {
                        $return = array();
                    }

                    $html = '<a class="';

                    $is_install = isset($option['flag_install']) ? true : false;

                    if (isset($option['class'])) {
                        $html .= $option['class'];
                    }
                    if ($is_install) {
                        $html .= ' btn btn-success';
                    }
                    if (!$is_install && count($return) == 0) {
                        $html .= ' btn btn-default';
                    }

                    $html .= '"';

                    if (isset($option['data-value'])) {
                        $html .= ' data-value="'.$option['data-value'].'"';
                    }

                    if (isset($option['data-module'])) {
                        $html .= ' data-module="'.$option['data-module'].'"';
                    }

                    if (isset($option['style'])) {
                        $html .= ' style="'.$option['style'].'"';
                    }

                    $html .= ' href="'.htmlentities($option['href']).(!is_null($back) ? '&back='.urlencode($back) : '').'" onclick="'.$option['onclick'].'"  title="'.$option['title'].'"><i class="icon-'.(isset($option['icon']) && $option['icon'] ? $option['icon']:'cog').'"></i> '.$option['text'].'</a>';
                    $return[] = $html;
                } elseif ($output_type == 'select') {
                    $return .= '<option id="'.$option_name.'" data-href="'.htmlentities($option['href']).(!is_null($back) ? '&back='.urlencode($back) : '').'" data-onclick="'.$option['onclick'].'">'.$option['text'].'</option>';
                }
            }
        }

        if ($output_type == 'select') {
            $return = '<select id="select_'.$module->name.'">'.$return.'</select>';
        }

        return $return;
    }


    /**
     * Controller responsible for importing new module from DropFile zone in BO
     * @param  Request $request
     * @return JsonResponse
     */
    public function importModuleAction(Request $request)
    {
        $translator = $this->get('translator');
        $moduleManager = $this->get('prestashop.module.manager');
        try {
            $file_uploaded = $request->files->get('file_uploaded');
            $constraints = array(
                new Assert\NotNull(),
                new Assert\File(array(
                    'maxSize' => ini_get('upload_max_filesize'),
                    'mimeTypes' => array(
                        'application/zip',
                        'application/x-gzip',
                        'application/gzip',
                        'application/x-gtar',
                        'application/x-tgz',
            ))));

            $violations = $this->get('validator')->validateValue($file_uploaded, $constraints);
            if (0 !== count($violations)) {
                $violationsMessages = '';
                foreach ($violations as $violation) {
                    $violationsMessages .= $violation->getMessage() . PHP_EOL;
                }
                throw new Exception($violationsMessages);
            }

            $file_uploaded_tmp_path = _PS_CACHE_DIR_.'tmp'.DIRECTORY_SEPARATOR.'upload';
            $tmp_filename_uniq = md5(uniqid()).$file_uploaded->guessExtension();
            $file_uploaded_tmp_fullpath = $file_uploaded_tmp_path.'/'.$tmp_filename_uniq;
            // Move file from server tmp DIR to PrestaShop tmp DIR
            $file_uploaded->move($file_uploaded_tmp_path, $tmp_filename_uniq);
            // Try to inflate archive given, and do check to verify we have a valid module architecture
            $module_name = $this->inflateModule($file_uploaded_tmp_fullpath);
            // Install the module
            $installation_response = [
                'status' => $moduleManager->install($module_name),
                'msg' => '',
                'module_name' => $module_name,
            ];

            if ($installation_response['status'] === null) {
                $installation_response['status'] = false;
                $installation_response['msg'] = $translator->trans(
                    '%module% did not return a valid response on installation.',
                    array('%module%' => $module_name),
                    'Admin.Modules.Notification');
            } elseif ($installation_response['status'] === true) {
                $installation_response['msg'] = $translator->trans(
                    'Installation of module %module% succeeded',
                    array('%module%' => $module_name),
                    'Admin.Modules.Notification');
                $installation_response['is_configurable'] = (bool)$this->get('prestashop.core.admin.module.repository')->getModule($module_name)->attributes->get('is_configurable');
            } else {
                $installation_response['msg'] = $translator->trans(
                    'Installation of module %module% failed.',
                    array('%module%' => $module_name),
                    'Admin.Modules.Notification');
            }

            return new JsonResponse(
                $installation_response,
                200,
                array( 'Content-Type' => 'application/json' )
            );
        } catch (Exception $e) {
            if (isset($module_name)) {
                $moduleManager->uninstall($module_name);
                $moduleManager->removeModuleFromDisk($module_name);
            }
            return new JsonResponse(
                array(
                'status' => false,
                'msg' => $e->getMessage()),
                200,
                array( 'Content-Type' => 'application/json' )
            );
        }
    }

    public function configureModuleAction($module_name)
    {
        /* @var $legacyUrlGenerator UrlGeneratorInterface */
        $legacyUrlGenerator = $this->get('prestashop.core.admin.url_generator_legacy');
        $legacyContextProvider = $this->get('prestashop.adapter.legacy.context');
        $legacyContext = $legacyContextProvider->getContext();
        $moduleRepository = $this->get('prestashop.core.admin.module.repository');
        // Get accessed module object
        $moduleAccessed = $moduleRepository->getModule($module_name);

        // Get current employee ID
        $currentEmployeeID = $legacyContext->employee->id;
        // Get accessed module DB ID
        $moduleAccessedID = (int)$moduleAccessed->database->get('id');

        // Save history for this module
        $moduleHistory = $this->getDoctrine()
            ->getRepository('PrestaShopBundle:ModuleHistory')
            ->findOneBy([
                'idEmployee' => $currentEmployeeID,
                'idModule' => $moduleAccessedID
            ]);

        if (is_null($moduleHistory)) {
            $moduleHistory = new ModuleHistory();
        }

        $moduleHistory->setIdEmployee($currentEmployeeID);
        $moduleHistory->setIdModule($moduleAccessedID);
        $moduleHistory->setDateUpd(new \DateTime(date('Y-m-d H:i:s')));

        $em = $this->getDoctrine()->getManager();
        $em->persist($moduleHistory);
        $em->flush();

        $redirectionParams = array(
            // do not transmit limit & offset: go to the first page when redirecting
            'configure' => $module_name,
        );
        return $this->redirect(
            $legacyUrlGenerator->generate('admin_module_configure_action', $redirectionParams),
            302
        );
    }

    protected function getToolbarButtons()
    {
        $translator = $this->get('translator');

        // toolbarButtons
        $toolbarButtons = array();
        $toolbarButtons['add_module'] = array(
            'href' => '#',
            'desc' => $translator->trans('Upload a module', array(), 'Admin.Modules.Feature'),
            'icon' => 'cloud_upload',
            'help' => $translator->trans('Upload a module', array(), 'Admin.Modules.Feature'),
        );
        $toolbarButtons['addons_connect'] = $this->getAddonsConnectToolbar();

        return $toolbarButtons;
    }

    private function inflateModule($fileToInflate)
    {
        $translator = $this->get('translator');
        if (file_exists($fileToInflate)) {
            $zipArchive = new \ZipArchive();
            $extractionStatus = $zipArchive->open($fileToInflate);

            if ($extractionStatus === true) {
                $filename = $zipArchive->getNameIndex(0);
                $moduleName = substr($filename, 0, strpos($filename, '/'));
                $zipArchive->extractTo(_PS_MODULE_DIR_);
                $zipArchive->close();
                unlink($fileToInflate);

                return $moduleName;
            } else {
                throw new Exception(
                    $translator->trans(
                        'Cannot open the following archive: %file% (error code: %code%)',
                        array(
                            '%file%' => $fileToInflate,
                            '%code%' => $extractionStatus),
                        'Admin.Modules.Notification'));
            }
        } else {
            throw new Exception(
                $translator->trans(
                    'Unable to find uploaded module at the following path: %file%',
                    array('%file%' => $fileToInflate),
                    'Admin.Modules.Notification'));
        }
    }

    private function getPresentedProducts(array &$products)
    {
        $modulePresenter = $this->get('prestashop.adapter.presenter.module');
        $presentedProducts = [];
        foreach ($products as $name => $product) {
            $presentedProducts[$name] = $modulePresenter->present($product);
        }

        return $presentedProducts;
    }

    private function getTopMenuData(array $topMenuData, $activeMenu = null)
    {
        if (isset($activeMenu)) {
            if (!isset($topMenuData[$activeMenu])) {
                throw new Exception("Menu '$activeMenu' not found in Top Menu data", 1);
            } else {
                $topMenuData[$activeMenu]->class = 'active';
            }
        }

        return (array)$topMenuData;
    }

    private function getAddonsConnectToolbar()
    {
        $addonsProvider = $this->get('prestashop.core.admin.data_provider.addons_interface');
        $translator = $this->get('translator');

        if ($addonsProvider->isAddonsAuthenticated()) {
            $addonsEmail = $addonsProvider->getAddonsEmail();
            $addonsConnect = [
                'href' => $this->generateUrl('admin_addons_logout'),
                'desc' => $addonsEmail['username_addons'],
                'icon' => 'exit_to_app',
                'help' => $translator->trans('Synchronized with Addons marketplace!', array(), 'Admin.Modules.Notification')
            ];
        } else {
            $addonsConnect = [
                'href' => '#',
                'desc' => $translator->trans('Connect to Addons marketplace', array(), 'Admin.Modules.Feature'),
                'icon' => 'vpn_key',
                'help' => $translator->trans('Connect to Addons marketplace', array(), 'Admin.Modules.Feature')
            ];
        }

        return $addonsConnect;
    }
}
