<?php

namespace PrestaShopBundle\Controller\Admin;

use Exception;
use PrestaShop\PrestaShop\Adapter\Module\ModuleZip;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterStatus;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterType;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository;
use PrestaShopBundle\Entity\ModuleHistory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;

class ModuleController extends FrameworkBundleAdminController
{
    /**
     * Controller responsible for displaying "Catalog" section of Module management pages.
     *
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
     * Controller responsible for displaying "Catalog Module Grid" section of Module management pages with ajax.
     *
     * @param Request $request
     *
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
        $responseArray = array();

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE | AddonListFilterType::SERVICE)
            ->setStatus(~AddonListFilterStatus::INSTALLED);

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
        $formattedContent = array();
        $formattedContent['selector'] = '.module-catalog-page';
        $formattedContent['content'] = $this->render(
            'PrestaShopBundle:Admin/Module/Includes:sorting.html.twig',
            array(
                'totalModules' => count($products),
            )
        )->getContent();
        $formattedContent['content'] .= $this->render(
            'PrestaShopBundle:Admin/Module/Includes:grid.html.twig',
            array(
                'modules' => $this->getPresentedProducts($products),
                'requireAddonsSearch' => true,
            )
        )->getContent();

        return $formattedContent;
    }

    private function constructJsonCatalogCategoriesMenuResponse($modulesProvider, $products)
    {
        $formattedContent = array();
        $formattedContent['selector'] = '.module-menu-item';
        $formattedContent['content'] = $this->render(
            'PrestaShopBundle:Admin/Module/Includes:dropdown_categories.html.twig',
            array(
                'topMenuData' => $this->getTopMenuData($modulesProvider->getCategoriesFromModules()),
            )
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
        foreach (array('native_modules', 'theme_bundle', 'modules') as $subpart) {
            $products->{$subpart} = array();
        }

        foreach ($installed_products as $installed_product) {
            if (in_array($installed_product->attributes->get('name'), $modulesTheme)) {
                $row = 'theme_bundle';
            } elseif ($installed_product->attributes->has('origin') && $installed_product->attributes->get('origin') === 'native' && $installed_product->attributes->get('author') === 'PrestaShop') {
                $row = 'native_modules';
            } else {
                $row = 'modules';
            }
            $products->{$row}[] = (object) $installed_product;
        }

        foreach ($products as $product_label => $products_part) {
            $products->{$product_label} = $modulesProvider->generateAddonsUrls($products_part);
            $products->{$product_label} = $this->getPresentedProducts($products_part);
        }

        dump($this->getTopMenuData($modulesProvider->getCategoriesFromModules($installed_products)));die;

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
        $action = $request->get('action');
        $module = $request->get('module_name');
        $forceDeletion = $request->query->has('deletion');

        $moduleManager = $this->get('prestashop.module.manager');
        $moduleRepository = $this->get('prestashop.core.admin.module.repository');
        $modulesProvider = $this->get('prestashop.core.admin.data_provider.module_interface');
        $translator = $this->get('translator');

        $response = array();
        if (method_exists($moduleManager, $action)) {
            // ToDo : Check if allowed to call this action
            try {
                if ($action == 'uninstall') {
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
                            '%action%' => $action, ),
                        'Admin.Notifications.Error'
                        );
                } elseif ($response[$module]['status'] === false) {
                    $error = $moduleManager->getError($module);
                    $response[$module]['msg'] = $translator->trans(
                        'Cannot %action% module %module%. %error_details%',
                        array(
                            '%action%' => str_replace('_', ' ', $action),
                            '%module%' => $module,
                            '%error_details%' => $error, ),
                        'Admin.Notifications.Error'
                    );
                } else {
                    $response[$module]['msg'] = $translator->trans(
                        '%action% action on module %module% succeeded.',
                        array(
                            '%action%' => ucfirst(str_replace('_', ' ', $action)),
                            '%module%' => $module, ),
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
                            '%error_details%' => $e->getMessage(), ),
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
            $products->{$subpart} = array();
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
                $products->{$row}[] = (object) $installed_product;
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
        $tabModulesList = $request->get('tab_modules_list');

        if ($tabModulesList) {
            $tabModulesList = explode(',', $tabModulesList);
            $modulesListUnsorted = $this->getModulesByInstallation($tabModulesList, $request->request->get('admin_list_from_source'));
        }

        $installed = $uninstalled = array();

        foreach ($tabModulesList as $key => $value) {
            $continue = 0;
            foreach ($modulesListUnsorted['installed'] as $moduleInstalled) {
                if ($moduleInstalled['attributes']['name'] == $value) {
                    $continue = 1;
                    $installed[] = $moduleInstalled;
                }
            }
            if ($continue) {
                continue;
            }
            foreach ($modulesListUnsorted['not_installed'] as $moduleNotInstalled) {
                if ($moduleNotInstalled['attributes']['name'] == $value) {
                    $uninstalled[] = $moduleNotInstalled;
                }
            }
        }

        $moduleListSorted = array(
            'installed' => $installed,
            'notInstalled' => $uninstalled,
        );

        $twigParams = array(
            'currentIndex' => '',
            'modulesList' => $moduleListSorted,
        );

        if ($request->request->has('admin_list_from_source')) {
            $twigParams['adminListFromSource'] = $request->request->get('admin_list_from_source');
        }

        return $this->render('PrestaShopBundle:Admin/Module:tab-modules-list.html.twig', $twigParams);
    }

    private function getModulesByInstallation($modulesSelectList = null)
    {
        $addonsProvider = $this->get('prestashop.core.admin.data_provider.module_interface');
        $moduleRepository = $this->get('prestashop.core.admin.module.repository');
        $modulePresenter = $this->get('prestashop.adapter.presenter.module');

        $modulesOnDisk = $moduleRepository->getList();

        $modulesList = array(
            'installed' => array(),
            'not_installed' => array(),
        );

        $modulesOnDisk = $addonsProvider->generateAddonsUrls($modulesOnDisk);
        foreach ($modulesOnDisk as $module) {
            if (!isset($modulesSelectList) || in_array($module->get('name'), $modulesSelectList)) {
                $perm = true;
                if ($module->get('id')) {
                    $perm &= \Module::getPermissionStatic($module->get('id'), 'configure');
                } else {
                    $id_admin_module = \Tab::getIdFromClassName('AdminModules');
                    $access = \Profile::getProfileAccess($this->getContext()->employee->id_profile, $id_admin_module);
                    if (!$access['edit']) {
                        $perm &= false;
                    }
                }

                if ($module->get('author') === ModuleRepository::PARTNER_AUTHOR) {
                    $module->set('type', 'addonsPartner');
                }

                if ($perm) {
                    $module->fillLogo();
                    if ($module->database->get('installed') == 1) {
                        $modulesList['installed'][] = $modulePresenter->present($module);
                    } else {
                        $modulesList['not_installed'][] = $modulePresenter->present($module);
                    }
                }
            }
        }

        return $modulesList;
    }

    /**
     * Controller responsible for importing new module from DropFile zone in BO.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function importModuleAction(Request $request)
    {
        $translator = $this->get('translator');
        $moduleManager = $this->get('prestashop.module.manager');
        $moduleZipManager = $this->get('prestashop.module.zip.manager');

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
            ), )), );

            $violations = $this->get('validator')->validateValue($file_uploaded, $constraints);
            if (0 !== count($violations)) {
                $violationsMessages = '';
                foreach ($violations as $violation) {
                    $violationsMessages .= $violation->getMessage().PHP_EOL;
                }
                throw new Exception($violationsMessages);
            }

            $module_name = $moduleZipManager->getName($file_uploaded->getPathname());

            // Install the module
            $installation_response = array(
                'status' => $moduleManager->install($file_uploaded->getPathname()),
                'msg' => '',
                'module_name' => $module_name,
            );

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
                $installation_response['is_configurable'] = (bool) $this->get('prestashop.core.admin.module.repository')->getModule($module_name)->attributes->get('is_configurable');
            } else {
                $installation_response['msg'] = $translator->trans(
                    'Installation of module %module% failed.',
                    array('%module%' => $module_name),
                    'Admin.Modules.Notification');
            }

            return new JsonResponse(
                $installation_response,
                200,
                array('Content-Type' => 'application/json')
            );
        } catch (Exception $e) {
            if (isset($module_name)) {
                $moduleManager->uninstall($module_name);
                $moduleManager->removeModuleFromDisk($module_name);
            }

            return new JsonResponse(
                array(
                'status' => false,
                'msg' => $e->getMessage(), ),
                200,
                array('Content-Type' => 'application/json')
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
        $moduleAccessedID = (int) $moduleAccessed->database->get('id');

        // Save history for this module
        $moduleHistory = $this->getDoctrine()
            ->getRepository('PrestaShopBundle:ModuleHistory')
            ->findOneBy(array(
                'idEmployee' => $currentEmployeeID,
                'idModule' => $moduleAccessedID,
            ));

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
        return array_merge($toolbarButtons, $this->getAddonsConnectToolbar());
    }

    private function getPresentedProducts(array &$products)
    {
        $modulePresenter = $this->get('prestashop.adapter.presenter.module');
        $presentedProducts = array();
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

        return (array) $topMenuData;
    }

    private function getAddonsConnectToolbar()
    {
        $addonsProvider = $this->get('prestashop.core.admin.data_provider.addons_interface');
        $translator = $this->get('translator');
        $addonsConnect = array();

        if ($addonsProvider->isAddonsAuthenticated()) {
            $addonsEmail = $addonsProvider->getAddonsEmail();
            $addonsConnect['addons_logout'] = array(
                'href' => '#',
                'desc' => $addonsEmail['username_addons'],
                'icon' => 'exit_to_app',
                'help' => $translator->trans('Synchronized with Addons marketplace!', array(), 'Admin.Modules.Notification'),
                'data-trololo' => 'lol',
            );
        } else {
            $addonsConnect['addons_connect'] = array(
                'href' => '#',
                'desc' => $translator->trans('Connect to Addons marketplace', array(), 'Admin.Modules.Feature'),
                'icon' => 'vpn_key',
                'help' => $translator->trans('Connect to Addons marketplace', array(), 'Admin.Modules.Feature'),
            );
        }

        return $addonsConnect;
    }
}
