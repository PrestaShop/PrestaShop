<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin;

use Exception;
use PrestaShop\PrestaShop\Adapter\Module\Module as ModuleAdapter;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterStatus;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterType;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository;
use PrestaShop\PrestaShop\Core\Addon\Module\Exception\UnconfirmedModuleActionException;
use PrestaShopBundle\Security\Voter\PageVoter;
use PrestaShopBundle\Entity\ModuleHistory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterOrigin;
use Module;
use Profile;
use stdClass;
use DateTime;

class ModuleController extends FrameworkBundleAdminController
{
    const CONTROLLER_NAME = 'ADMINMODULESSF';

    /**
     * @deprecated
     */
    const controller_name = self::CONTROLLER_NAME;

    /**
     * @return Response
     */
    public function catalogAction()
    {
        if (
            !in_array(
                $this->authorizationLevel($this::CONTROLLER_NAME),
                array(
                    PageVoter::LEVEL_READ,
                    PageVoter::LEVEL_UPDATE,
                    PageVoter::LEVEL_CREATE,
                    PageVoter::LEVEL_DELETE,
                )
            )
        ) {
            return $this->redirect('admin_dashboard');
        }

        $errorMessage = $this->trans(
            'You do not have permission to add this.',
            'Admin.Notifications.Error'
        );

        return $this->render('PrestaShopBundle:Admin/Module:catalog.html.twig', array(
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'layoutTitle' => $this->trans('Module selection', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminModules'),
            'requireFilterStatus' => false,
            'level' => $this->authorizationLevel($this::CONTROLLER_NAME),
            'errorMessage' => $errorMessage,
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
        $moduleRepository = $this->get('prestashop.core.admin.module.repository');
        $responseArray = array();

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE | AddonListFilterType::SERVICE)
            ->setStatus(~AddonListFilterStatus::INSTALLED)
        ;

        try {
            $modules = $modulesProvider->generateAddonsUrls(
                $moduleRepository->getFilteredList($filters)
            );

            $categoriesMenu = $this->get('prestashop.categories_provider')->getCategoriesMenu($modules);
            shuffle($modules);
            $responseArray['domElements'][] = $this->constructJsonCatalogCategoriesMenuResponse($categoriesMenu);
            $responseArray['domElements'][] = $this->constructJsonCatalogBodyResponse($modulesProvider, $modules);
            $responseArray['status'] = true;
        } catch (Exception $e) {
            $responseArray['msg'] = $this->trans(
                'Cannot get catalog data, please try again later. Reason: %error_details%',
                'Admin.Modules.Notification',
                array('%error_details%' => print_r($e->getMessage(), true))
            );
            $responseArray['status'] = false;
        }

        return new JsonResponse($responseArray, 200);
    }

    private function constructJsonCatalogBodyResponse($modulesProvider, $modules)
    {
        $modules = $modulesProvider->generateAddonsUrls($modules);
        $formattedContent = array();
        $formattedContent['selector'] = '.module-catalog-page';
        $formattedContent['content'] = $this->render(
            'PrestaShopBundle:Admin/Module/Includes:sorting.html.twig',
            array(
                'totalModules' => count($modules),
            )
        )->getContent();

        $errorMessage = $this->trans('You do not have permission to add this.', 'Admin.Notifications.Error');

        $formattedContent['content'] .= $this->render(
            'PrestaShopBundle:Admin/Module/Includes:grid.html.twig',
            array(
                'modules' => $this->getPresentedProducts($modules),
                'requireAddonsSearch' => true,
                'id' => 'all',
                'level' => $this->authorizationLevel($this::CONTROLLER_NAME),
                'errorMessage' => $errorMessage,
            )
        )->getContent();

        return $formattedContent;
    }

    private function constructJsonCatalogCategoriesMenuResponse($categoriesMenu)
    {
        $formattedContent = array();
        $formattedContent['selector'] = '.module-menu-item';
        $formattedContent['content'] = $this->render(
            'PrestaShopBundle:Admin/Module/Includes:dropdown_categories.html.twig',
            array(
                'topMenuData' => $this->getTopMenuData($categoriesMenu),
            )
        )->getContent();

        return $formattedContent;
    }

    public function manageAction()
    {
        $modulesProvider = $this->get('prestashop.core.admin.data_provider.module_interface');
        $shopService = $this->get('prestashop.adapter.shop.context');
        $moduleRepository = $this->get('prestashop.core.admin.module.repository');
        $themeRepository = $this->get('prestashop.core.addon.theme.repository');

        // Retrieve current shop
        $shopID = $shopService->getContextShopID();
        $shops = $shopService->getShops();

        if (!empty($shopID) && is_array($shops) && array_key_exists($shopID, $shops)) {
            $shop = $shops[$shopID];
            $currentTheme = $themeRepository->getInstanceByName($shop['theme_name']);
            $modulesTheme = $currentTheme->getModulesToEnable();
        } else {
            $modulesTheme = array();
        }

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE | AddonListFilterType::SERVICE)
            ->removeStatus(AddonListFilterStatus::UNINSTALLED);
        $installedProducts = $moduleRepository->getFilteredList($filters);

        $modules = new stdClass();
        foreach (array('native_modules', 'theme_bundle', 'modules') as $subpart) {
            $modules->{$subpart} = array();
        }

        foreach ($installedProducts as $installedProduct) {
            if (in_array($installedProduct->attributes->get('name'), $modulesTheme)) {
                $row = 'theme_bundle';
            } elseif (
                $installedProduct->attributes->has('origin_filter_value')
                && in_array(
                    $installedProduct->attributes->get('origin_filter_value'),
                    array(
                        AddonListFilterOrigin::ADDONS_NATIVE,
                        AddonListFilterOrigin::ADDONS_NATIVE_ALL,
                    )
                )
                && 'PrestaShop' === $installedProduct->attributes->get('author')
            ) {
                $row = 'native_modules';
            } else {
                $row = 'modules';
            }
            $modules->{$row}[] = (object) $installedProduct;
        }

        foreach ($modules as $moduleLabel => $modulesPart) {
            $modules->{$moduleLabel} = $modulesProvider->generateAddonsUrls($modulesPart);
            $modules->{$moduleLabel} = $this->getPresentedProducts($modulesPart);
        }

        $categoriesMenu = $this->get('prestashop.categories_provider')->getCategoriesMenu($installedProducts);

        $errorMessage = $this->trans('You do not have permission to add this.', 'Admin.Notifications.Error');

        return $this->render('PrestaShopBundle:Admin/Module:manage.html.twig', array(
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'layoutTitle' => $this->trans('Manage installed modules', 'Admin.Modules.Feature'),
            'modules' => $modules,
            'topMenuData' => $this->getTopMenuData($categoriesMenu),
            'requireAddonsSearch' => false,
            'requireBulkActions' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminModules'),
            'requireFilterStatus' => true,
            'level' => $this->authorizationLevel($this::CONTROLLER_NAME),
            'errorMessage' => $errorMessage,
        ));
    }

    public function moduleAction(Request $request)
    {
        if (
            !in_array(
                $this->authorizationLevel($this::CONTROLLER_NAME),
                array(
                    PageVoter::LEVEL_CREATE,
                    PageVoter::LEVEL_UPDATE,
                    PageVoter::LEVEL_DELETE,
                )
            )
        ) {
            return $this->redirect('admin_dashboard');
        }

        if ($this->isDemoModeEnabled()) {
            return $this->getDisabledFunctionalityResponse($request);
        }

        $action = $request->get('action');
        $module = $request->get('module_name');

        $moduleManager = $this->get('prestashop.module.manager');
        $moduleManager->setActionParams($request->request->get('actionParams', array()));
        $moduleRepository = $this->get('prestashop.core.admin.module.repository');
        $modulesProvider = $this->get('prestashop.core.admin.data_provider.module_interface');

        $response = array(
            $module => array(),
        );
        if (!method_exists($moduleManager, $action)) {
            $response[$module]['status'] = false;
            $response[$module]['msg'] = $this->trans('Invalid action', 'Admin.Notifications.Error');
            return new JsonResponse($response);
        }

        try {
            $response[$module]['status'] = $moduleManager->{$action}($module);

            if ($response[$module]['status'] === null) {
                $response[$module]['status'] = false;
                $response[$module]['msg'] = $this->trans(
                    '%module% did not return a valid response on %action% action.',
                    'Admin.Modules.Notification',
                    array(
                        '%module%' => $module,
                        '%action%' => $action,
                    )
                );
            } elseif ($response[$module]['status'] === false) {
                $error = $moduleManager->getError($module);
                $response[$module]['msg'] = $this->trans(
                    'Cannot %action% module %module%. %error_details%',
                    'Admin.Modules.Notification',
                    array(
                        '%action%' => str_replace('_', ' ', $action),
                        '%module%' => $module,
                        '%error_details%' => $error,
                    )
                );
            } else {
                $response[$module]['msg'] = $this->trans(
                    '%action% action on module %module% succeeded.',
                    'Admin.Modules.Notification',
                    array(
                        '%action%' => ucfirst(str_replace('_', ' ', $action)),
                        '%module%' => $module,
                    )
                );
            }
        } catch (UnconfirmedModuleActionException $e) {
            $modules = $modulesProvider->generateAddonsUrls(array($e->getModule()));
            $response[$module] = array_replace($response[$module],
                    array(
                        'status' => false,
                        'confirmation_subject' => $e->getSubject(),
                        'module' => $this->getPresentedProducts($modules)[0],
                        'msg' => $this->trans(
                            'Confirmation needed by module %module% on %action% (%subject%).',
                            'Admin.Modules.Notification',
                            array(
                                '%subject%' => $e->getSubject(),
                                '%action%' => $e->getAction(),
                                '%module%' => $module,
                            )
                        )
                    ));
        } catch (Exception $e) {
            $response[$module]['status'] = false;
            $response[$module]['msg'] = $this->trans(
                'Exception thrown by module %module% on %action%. %error_details%',
                'Admin.Modules.Notification',
                array(
                    '%action%' => str_replace('_', ' ', $action),
                    '%module%' => $module,
                    '%error_details%' => $e->getMessage(),
                )
            );

            $logger = $this->get('logger');
            $logger->error($response[$module]['msg']);
        }

        if ($response[$module]['status'] === true && $action != 'uninstall') {
            $moduleInstance = $moduleRepository->getModule($module);
            $moduleInstanceWithUrl = $modulesProvider->generateAddonsUrls(array($moduleInstance));
            $response[$module]['action_menu_html'] = $this->render('PrestaShopBundle:Admin/Module/Includes:action_menu.html.twig', array(
                'module' => $this->getPresentedProducts($moduleInstanceWithUrl)[0],
                'level' => $this->authorizationLevel($this::CONTROLLER_NAME),
                ))->getContent();
        }

        return new JsonResponse($response, 200);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return JsonResponse
     */
    protected function getDisabledFunctionalityResponse($request)
    {
        $module = $request->get('module_name');
        $content = array(
            $module => array(
                'status' => false,
                'msg' => $this->getDemoModeErrorMessage(),
            ),
        );

        return new JsonResponse($content);
    }

    /**
     * @return Response
     */
    public function notificationAction()
    {
        $modulesPresenter = function (array &$modules) {
            return $this->getPresentedProducts($modules);
        };

        $moduleManager = $this->get('prestashop.module.manager');
        $modules = $moduleManager->getModulesWithNotifications($modulesPresenter);
        $layoutTitle = $this->trans('Module notifications', 'Admin.Modules.Feature');

        $errorMessage = $this->trans('You do not have permission to add this.', 'Admin.Notifications.Error');

        return $this->render('PrestaShopBundle:Admin/Module:notifications.html.twig', array(
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'layoutTitle' => $layoutTitle,
            'help_link' => $this->generateSidebarLink('AdminModules'),
            'modules' => $modules,
            'requireAddonsSearch' => false,
            'requireBulkActions' => false,
            'requireFilterStatus' => false,
            'level' => $this->authorizationLevel($this::CONTROLLER_NAME),
            'errorMessage' => $errorMessage,
        ));
    }

    /**
     * @return JsonResponse with number of modules having at least one notification
     */
    public function notificationsCountAction()
    {
        $moduleManager = $this->container->get('prestashop.module.manager');
        return new JsonResponse(array(
            'count' => $moduleManager->countModulesWithNotifications(),
            'target' => $this->generateUrl('admin_module_notification'),
        ));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getPreferredModulesAction(Request $request)
    {
        $tabModulesList = $request->get('tab_modules_list');

        if ($tabModulesList) {
            $tabModulesList = explode(',', $tabModulesList);
            $modulesListUnsorted = $this->getModulesByInstallation($tabModulesList, $request->request->get('admin_list_from_source'));
        }

        $installed = $uninstalled = array();

        if (!empty($tabModulesList)) {
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
        }

        $moduleListSorted = array(
            'installed' => $installed,
            'notInstalled' => $uninstalled,
        );

        $twigParams = array(
            'currentIndex' => '',
            'modulesList' => $moduleListSorted,
            'level' => $this->authorizationLevel($this::CONTROLLER_NAME),
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
        $tabRepository = $this->get('prestashop.core.admin.tab.repository');

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
                    $perm &= Module::getPermissionStatic($module->get('id'), 'configure', $this->getContext()->employee);
                } else {
                    $id_admin_module = $tabRepository->findOneIdByClassName('AdminModules');
                    $access = Profile::getProfileAccess($this->getContext()->employee->id_profile, $id_admin_module);
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
        $moduleManager = $this->get('prestashop.module.manager');
        $moduleZipManager = $this->get('prestashop.module.zip.manager');

        if ($this->isDemoModeEnabled()) {
            return new JsonResponse(array(
                'status' => false,
                'msg' => $this->getDemoModeErrorMessage(),
            ));
        }

        try {
            if (
                !in_array(
                    $this->authorizationLevel($this::CONTROLLER_NAME),
                    array(
                        PageVoter::LEVEL_CREATE,
                        PageVoter::LEVEL_DELETE
                    )
                )
            ) {
                return new JsonResponse(
                    array(
                        'status' => false,
                        'msg' => $this->trans('You do not have permission to add this.', 'Admin.Notifications.Error'),
                    ),
                    200,
                    array('Content-Type' => 'application/json')
                );
            }
            $file_uploaded = $request->files->get('file_uploaded');
            $constraints = array(
                new Assert\NotNull(),
                new Assert\File(
                    array(
                        'maxSize'   => ini_get('upload_max_filesize'),
                        'mimeTypes' => array(
                            'application/zip',
                            'application/x-gzip',
                            'application/gzip',
                            'application/x-gtar',
                            'application/x-tgz',
                        ),
                    )
                ),
            );

            $violations = $this->get('validator')->validate($file_uploaded, $constraints);
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
                $installation_response['msg'] = $this->trans(
                    '%module% did not return a valid response on installation.',
                    'Admin.Modules.Notification',
                    array('%module%' => $module_name)
                );
            } elseif ($installation_response['status'] === true) {
                $installation_response['msg'] = $this->trans(
                    'Installation of module %module% was successful.',
                    'Admin.Modules.Notification',
                    array('%module%' => $module_name)
                );
                $installation_response['is_configurable'] = (bool) $this->get('prestashop.core.admin.module.repository')->getModule($module_name)->attributes->get('is_configurable');
            } else {
                $error = $moduleManager->getError($module_name);
                $installation_response['msg'] = $this->trans(
                    'Installation of module %module% failed. %error%',
                    'Admin.Modules.Notification',
                    array(
                        '%module%' => $module_name,
                        '%error%' => $error,
                    )
                );
            }

            return new JsonResponse(
                $installation_response,
                200,
                array('Content-Type' => 'application/json')
            );
        } catch (UnconfirmedModuleActionException $e) {
            $modules = $this->get('prestashop.core.admin.data_provider.module_interface')->generateAddonsUrls(array($e->getModule()));
            return new JsonResponse(
                    array(
                        'status' => false,
                        'confirmation_subject' => $e->getSubject(),
                        'module' => $this->getPresentedProducts($modules)[0],
                        'msg' => $this->trans(
                            'Confirmation needed by module %module% on %action% (%subject%).',
                            'Admin.Modules.Notification',
                            array(
                                '%subject%' => $e->getSubject(),
                                '%action%' => $e->getAction(),
                                '%module%' => $module_name,
                            )
                    )));
        } catch (Exception $e) {
            if (isset($module_name)) {
                $moduleManager->disable($module_name);
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
        $moduleHistory->setDateUpd(new DateTime(date('Y-m-d H:i:s')));

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
        // toolbarButtons
        $toolbarButtons = array();

        if (!in_array(
            $this->authorizationLevel($this::controller_name),
            array(
                PageVoter::LEVEL_READ,
                PageVoter::LEVEL_UPDATE,
            )
        )) {
            $toolbarButtons['add_module'] = array(
                'href' => '#',
                'desc' => $this->trans('Upload a module', 'Admin.Modules.Feature'),
                'icon' => 'cloud_upload',
                'help' => $this->trans('Upload a module', 'Admin.Modules.Feature'),
            );
        }

        return array_merge($toolbarButtons, $this->getAddonsConnectToolbar());
    }

    private function getPresentedProducts(array &$modules)
    {
        $modulePresenter = $this->get('prestashop.adapter.presenter.module');
        $presentedProducts = array();
        foreach ($modules as $name => $product) {
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
        $addonsConnect = array();

        if ($addonsProvider->isAddonsAuthenticated()) {
            $addonsEmail = $addonsProvider->getAddonsEmail();
            $addonsConnect['addons_logout'] = array(
                'href' => '#',
                'desc' => $addonsEmail['username_addons'],
                'icon' => 'exit_to_app',
                'help' => $this->trans('Synchronized with Addons marketplace!', 'Admin.Modules.Notification'),
            );
        } else {
            $addonsConnect['addons_connect'] = array(
                'href' => '#',
                'desc' => $this->trans('Connect to Addons marketplace', 'Admin.Modules.Feature'),
                'icon' => 'vpn_key',
                'help' => $this->trans('Connect to Addons marketplace', 'Admin.Modules.Feature'),
            );
        }

        return $addonsConnect;
    }

    public function getModuleCartAction($moduleId)
    {
        $moduleRepository = $this->get('prestashop.core.admin.module.repository');
        $module = $moduleRepository->getModuleById($moduleId);

        $addOnsAdminDataProvider = $this->get('prestashop.core.admin.data_provider.module_interface');
        $addOnsAdminDataProvider->generateAddonsUrls(array($module));

        $modulePresenter = $this->get('prestashop.adapter.presenter.module');
        $moduleToPresent = $modulePresenter->present($module);

        return $this->render(
            '@PrestaShop/Admin/Module/Includes/modal_read_more_content.html.twig',
            array(
                'module' => $moduleToPresent,
                'level' => $this->authorizationLevel($this::CONTROLLER_NAME),
            )
        );
    }
}
