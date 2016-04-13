<?php

namespace PrestaShopBundle\Controller\Admin;

use Exception;
use PrestaShop\PrestaShop\Adapter\Module\ModulePresenter;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterOrigin;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterStatus;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterType;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ModuleController extends FrameworkBundleAdminController
{
    /**
     * Controller responsible for displaying "Catalog" section of Module management pages
     * @return Response
     */
    public function catalogAction()
    {
        $translator = $this->container->get('prestashop.adapter.translator');

        return $this->render('PrestaShopBundle:Admin/Module:catalog.html.twig', array(
                'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
                'layoutTitle' => $translator->trans('Modules & Services', array(), 'AdminModules'),
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
        $translator = $this->get('prestashop.adapter.translator');
        $moduleManagerFactory = new ModuleManagerBuilder();
        $moduleRepository = $moduleManagerFactory->buildRepository();
        $responseArray = [];

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE | AddonListFilterType::SERVICE)
            ->setOrigin(AddonListFilterOrigin::ADDONS_ALL)
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

    public function manageAction(Request $request, $category = null, $keyword = null)
    {
        $translator = $this->get('prestashop.adapter.translator');
        $modulesProvider = $this->get('prestashop.core.admin.data_provider.module_interface');
        $moduleManagerFactory = new ModuleManagerBuilder();
        $moduleRepository = $moduleManagerFactory->buildRepository();

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE | AddonListFilterType::SERVICE)
            ->removeStatus(AddonListFilterStatus::UNINSTALLED);
        $installed_products = $moduleRepository->getFilteredList($filters);

        $filter = [];
        if ($keyword !== null) {
            $filter['search'] = $keyword;
        }
        if ($category !== null) {
            $filter['category'] = $category;
        }

        $products = new \stdClass;
        foreach (['native_modules', 'theme_bundle', 'modules'] as $subpart) {
            $products->{$subpart} = [];
        }

        foreach ($installed_products as $installed_product) {
            if ($installed_product->attributes->has('origin') && $installed_product->attributes->get('origin') === 'native' && $installed_product->attributes->get('author') === 'PrestaShop') {
                $row = 'native_modules';
            } elseif (0 /* ToDo: insert condition for theme related modules*/) {
                $row = 'theme_bundle';
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
                'layoutTitle' => $translator->trans('Manage my modules', array(), 'AdminModules'),
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

        $moduleManagerFactory = new ModuleManagerBuilder();
        $moduleManager = $moduleManagerFactory->build();
        $moduleRepository = $moduleManagerFactory->buildRepository();
        $modulesProvider = $this->get('prestashop.core.admin.data_provider.module_interface');

        $ret = array();
        if (method_exists($moduleManager, $action)) {
            // ToDo : Check if allowed to call this action
            try {
                if ($action == "uninstall") {
                    $ret[$module]['status'] = $moduleManager->{$action}($module, $forceDeletion);
                } else {
                    $ret[$module]['status'] = $moduleManager->{$action}($module);
                }

                if ($ret[$module]['status'] === null) {
                    $ret[$module]['status'] = false;
                    $ret[$module]['msg'] = $module .' did not return a valid response on '.$action .' action';
                } else {
                    $ret[$module]['msg'] = ucfirst(str_replace('_', ' ', $action)). ' action on module '. $module;
                    $ret[$module]['msg'] .= $ret[$module]['status']?' succeeded':' failed';
                }
            } catch (Exception $e) {
                $ret[$module]['status'] = false;
                $ret[$module]['msg'] = sprintf('Exception thrown by addon %s on %s. %s', $module, $action, $e->getMessage());

                $logger = $this->get('logger');
                $logger->error($ret[$module]['msg']);
            }
        } else {
            $ret[$module]['status'] = false;
            $ret[$module]['msg'] = 'Invalid action';
        }

        if ($request->isXmlHttpRequest()) {
            if ($ret[$module]['status'] === true && $action != 'uninstall') {
                $moduleInstance = $moduleRepository->getModule($module);
                $moduleInstanceWithUrl = $modulesProvider->generateAddonsUrls(array($moduleInstance));
                $ret[$module]['action_menu_html'] = $this->render('PrestaShopBundle:Admin/Module/Includes:action_menu.html.twig', array(
                        'module' => $this->getPresentedProducts($moduleInstanceWithUrl)[0],
                    ))->getContent();
            }

            return new JsonResponse($ret, 200);
        }

        // We need a better error handler here. Meanwhile, I throw an exception
        if (! $ret[$module]['status']) {
            $this->addFlash('error', $ret[$module]['msg']);
        } else {
            $this->addFlash('success', $ret[$module]['msg']);
        }

        if ($request->server->get('HTTP_REFERER')) {
            return $this->redirect($request->server->get('HTTP_REFERER'));
        } else {
            return $this->redirect($this->generateUrl('admin_module_catalog'));
        }
    }

    public function notificationAction()
    {
        $translator = $this->get('prestashop.adapter.translator');
        $modulesProvider = $this->get('prestashop.core.admin.data_provider.module_interface');
        $moduleManagerFactory = new ModuleManagerBuilder();
        $moduleRepository = $moduleManagerFactory->buildRepository();

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE | AddonListFilterType::SERVICE)
            ->setStatus(AddonListFilterStatus::INSTALLED);
        $installed_products = $moduleRepository->getFilteredList($filters);

        $products = new \stdClass;
        foreach (['to_configure', 'to_update', 'to_install'] as $subpart) {
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

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE)
            ->removeStatus(AddonListFilterStatus::INSTALLED)
            ->setOrigin(AddonListFilterOrigin::DISK | AddonListFilterOrigin::ADDONS_CUSTOMER);
        $products->to_install = $moduleRepository->getFilteredList($filters);

        foreach ($products as $product_label => $products_part) {
            $products->{$product_label} = $modulesProvider->generateAddonsUrls($products_part);
            $products->{$product_label} = $this->getPresentedProducts($products_part);
        }

        return $this->render('PrestaShopBundle:Admin/Module:notifications.html.twig', array(
                'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
                'layoutTitle' => $translator->trans('Module notifications', array(), 'AdminModules'),
                'modules' => $products,
                'requireAddonsSearch' => false,
                'requireBulkActions' => false,
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink('AdminModules'),
                'requireFilterStatus' => false,
        ));
    }

    public function getPreferredModulesAction()
    {
        $controller = new \AdminModulesControllerCore();
        ob_start();

        $controller->ajaxProcessGetTabModulesList();

        $content = ob_get_clean();
        return new Response($content);
    }

    /**
     * Controller responsible for importing new module from DropFile zone in BO
     * @param  Request $request
     * @return JsonResponse
     */
    public function importModuleAction(Request $request)
    {
        $moduleManager = $this->get('prestashop.module.manager');
        try {
            $file_uploaded = $request->files->get('file_uploaded');
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
                $installation_response['msg'] = $module_name .' did not return a valid response on install action';
            } else {
                $installation_response['msg'] = 'Install action on module '. $module_name;
                if ($installation_response['status'] === true) {
                    $installation_response['is_configurable'] = (bool)$this->get('prestashop.core.admin.module.repository')->getModule($module_name)->attributes->get('is_configurable');
                    $installation_response['msg'] .= 'succeeded';
                } else {
                    $installation_response['msg'] .= 'failed';
                }
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
            return new JsonResponse(array(
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

        $redirectionParams = array(
            // do not transmit limit & offset: go to the first page when redirecting
            'configure' => $module_name,
        );
        return $this->redirect($legacyUrlGenerator->generate('admin_module_configure_action',
            $redirectionParams), 302);
    }

    protected function getToolbarButtons()
    {
        $translator = $this->get('prestashop.adapter.translator');

        // toolbarButtons
        $toolbarButtons = array();
        $toolbarButtons['add_module'] = array(
            'href' => '#',
            'desc' => $translator->trans('Upload a module', array(), get_class($this)),
            'icon' => 'cloud_upload',
            'help' => $translator->trans('Upload a module', array(), get_class($this)),
        );
        $toolbarButtons['addons_connect'] = $this->getAddonsConnectToolbar();

        return $toolbarButtons;
    }

    private function inflateModule($fileToInflate)
    {
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
                throw new Exception('Cannot open the following archive: '.$fileToInflate.' (error code: '.$extractionStatus.')');
            }
        } else {
            throw new Exception('Unable to find uploaded module at the following path: '.$fileToInflate);
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
        $translator = $this->get('prestashop.adapter.translator');

        if ($addonsProvider->isAddonsAuthenticated()) {
            $addonsEmail = $addonsProvider->getAddonsEmail();
            $addonsConnect = [
                'href' => $this->generateUrl('admin_addons_logout'),
                'desc' => $addonsEmail['username_addons'],
                'icon' => 'exit_to_app',
                'help' => $translator->trans('Synchronized with Addons Marketplace!', array(), get_class($this))
            ];
        } else {
            $addonsConnect = [
                'href' => '#',
                'desc' => $translator->trans('Connect to addons marketplace', array(), get_class($this)),
                'icon' => 'vpn_key',
                'help' => $translator->trans('Connect to addons marketplace', array(), get_class($this))
            ];
        }

        return $addonsConnect;
    }
}
