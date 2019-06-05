<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Improve;

use DateTime;
use Exception;
use Module;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterStatus;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterType;
use PrestaShop\PrestaShop\Core\Addon\AddonsCollection;
use PrestaShop\PrestaShop\Core\Addon\Module\Exception\UnconfirmedModuleActionException;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository;
use PrestaShopBundle\Controller\Admin\Improve\Modules\ModuleAbstractController;
use PrestaShopBundle\Entity\ModuleHistory;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Voter\PageVoter;
use PrestaShopBundle\Service\DataProvider\Admin\CategoriesProvider;
use Profile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Responsible of "Improve > Modules > Modules & Services > Catalog / Manage" page display.
 */
class ModuleController extends ModuleAbstractController
{
    const CONTROLLER_NAME = 'ADMINMODULESSF';

    const MAX_MODULES_DISPLAYED = 6;

    /**
     * @AdminSecurity("is_granted(['read', 'create', 'update', 'delete'], 'ADMINMODULESSF_')")
     *
     * @return Response
     */
    public function catalogAction()
    {
        return $this->render(
            '@PrestaShop/Admin/Module/catalog.html.twig',
            [
                'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
                'layoutTitle' => $this->trans('Modules catalog', 'Admin.Navigation.Menu'),
                'requireAddonsSearch' => true,
                'requireBulkActions' => false,
                'showContentHeader' => true,
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink('AdminModules'),
                'requireFilterStatus' => false,
                'level' => $this->authorizationLevel(self::CONTROLLER_NAME),
                'errorMessage' => $this->trans(
                    'You do not have permission to add this.',
                    'Admin.Notifications.Error'
                ),
            ]
        );
    }

    /**
     * Controller responsible for displaying "Catalog Module Grid" section of Module management pages with ajax.
     *
     * @AdminSecurity("is_granted(['read', 'create', 'update', 'delete'], 'ADMINMODULESSF_')")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function manageAction()
    {
        $modulesProvider = $this->get('prestashop.core.admin.data_provider.module_interface');
        $shopService = $this->get('prestashop.adapter.shop.context');
        $moduleRepository = $this->get('prestashop.core.admin.module.repository');
        $themeRepository = $this->get('prestashop.core.addon.theme.repository');

        // Retrieve current shop
        $shopId = $shopService->getContextShopId();
        $shops = $shopService->getShops();
        $modulesTheme = [];
        if (isset($shops[$shopId]) && is_array($shops[$shopId])) {
            $shop = $shops[$shopId];
            $currentTheme = $themeRepository->getInstanceByName($shop['theme_name']);
            $modulesTheme = $currentTheme->getModulesToEnable();
        }

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE | AddonListFilterType::SERVICE)
            ->removeStatus(AddonListFilterStatus::UNINSTALLED);
        $installedProducts = $moduleRepository->getFilteredList($filters);
        $categories = $this->getCategories($modulesProvider, $installedProducts);

        $bulkActions = [
            'bulk-uninstall' => $this->trans('Uninstall', 'Admin.Actions'),
            'bulk-disable' => $this->trans('Disable', 'Admin.Actions'),
            'bulk-enable' => $this->trans('Enable', 'Admin.Actions'),
            'bulk-reset' => $this->trans('Reset', 'Admin.Actions'),
            'bulk-enable-mobile' => $this->trans('Enable Mobile', 'Admin.Modules.Feature'),
            'bulk-disable-mobile' => $this->trans('Disable Mobile', 'Admin.Modules.Feature'),
        ];

        return $this->render(
            '@PrestaShop/Admin/Module/manage.html.twig',
            [
                'maxModulesDisplayed' => self::MAX_MODULES_DISPLAYED,
                'bulkActions' => $bulkActions,
                'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
                'layoutTitle' => $this->trans('Module manager', 'Admin.Modules.Feature'),
                'categories' => $categories['categories'],
                'topMenuData' => $this->getTopMenuData($categories),
                'requireAddonsSearch' => false,
                'requireBulkActions' => true,
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink('AdminModules'),
                'requireFilterStatus' => true,
                'level' => $this->authorizationLevel(self::CONTROLLER_NAME),
                'errorMessage' => $this->trans('You do not have permission to add this.', 'Admin.Notifications.Error'),
            ]
        );
    }

    /**
     * @AdminSecurity("is_granted(['read', 'create', 'update', 'delete'], 'ADMINMODULESSF_')")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getPreferredModulesAction(Request $request)
    {
        $tabModulesList = $request->get('tab_modules_list');

        if ($tabModulesList) {
            $tabModulesList = explode(',', $tabModulesList);
            $modulesListUnsorted = $this->getModulesByInstallation(
                $tabModulesList,
                $request->request->get('admin_list_from_source')
            );
        }

        $installed = $uninstalled = [];

        if (!empty($tabModulesList)) {
            foreach ($tabModulesList as $key => $value) {
                foreach ($modulesListUnsorted['installed'] as $moduleInstalled) {
                    if ($moduleInstalled['attributes']['name'] == $value) {
                        $installed[] = $moduleInstalled;

                        continue 2;
                    }
                }

                foreach ($modulesListUnsorted['not_installed'] as $moduleNotInstalled) {
                    if ($moduleNotInstalled['attributes']['name'] == $value) {
                        $uninstalled[] = $moduleNotInstalled;

                        continue 2;
                    }
                }
            }
        }

        $moduleListSorted = [
            'installed' => $installed,
            'notInstalled' => $uninstalled,
        ];

        $twigParams = [
            'currentIndex' => '',
            'modulesList' => $moduleListSorted,
            'level' => $this->authorizationLevel(self::CONTROLLER_NAME),
        ];

        if ($request->request->has('admin_list_from_source')) {
            $twigParams['adminListFromSource'] = $request->request->get('admin_list_from_source');
        }

        return $this->render(
            '@PrestaShop/Admin/Module/tab-modules-list.html.twig',
            $twigParams
        );
    }

    /**
     * @AdminSecurity("is_granted(['read', 'create', 'update', 'delete'], 'ADMINMODULESSF_')")
     *
     * @param Request $module_name
     *
     * @return Response
     */
    public function configureModuleAction($module_name)
    {
        /** @var UrlGeneratorInterface $legacyUrlGenerator */
        $legacyUrlGenerator = $this->get('prestashop.core.admin.url_generator_legacy');
        $legacyContextProvider = $this->get('prestashop.adapter.legacy.context');
        $legacyContext = $legacyContextProvider->getContext();
        $moduleRepository = $this->get('prestashop.core.admin.module.repository');
        // Get accessed module object
        $moduleAccessed = $moduleRepository->getModule($module_name);

        // Get current employee Id
        $currentEmployeeId = $legacyContext->employee->id;
        // Get accessed module DB Id
        $moduleAccessedId = (int) $moduleAccessed->database->get('id');

        // Save history for this module
        $moduleHistory = $this->getDoctrine()
            ->getRepository('PrestaShopBundle:ModuleHistory')
            ->findOneBy(
                [
                    'idEmployee' => $currentEmployeeId,
                    'idModule' => $moduleAccessedId,
                ]
            );

        if (null === $moduleHistory) {
            $moduleHistory = new ModuleHistory();
        }

        $moduleHistory->setIdEmployee($currentEmployeeId);
        $moduleHistory->setIdModule($moduleAccessedId);
        $moduleHistory->setDateUpd(new DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($moduleHistory);
        $em->flush();

        return $this->redirect(
            $legacyUrlGenerator->generate(
                'admin_module_configure_action',
                [
                    // do not transmit limit & offset: go to the first page when redirecting
                    'configure' => $module_name,
                ]
            )
        );
    }

    /**
     * @AdminSecurity("is_granted(['read', 'create', 'update', 'delete'], 'ADMINMODULESSF_')")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getModuleCartAction($moduleId)
    {
        $moduleRepository = $this->get('prestashop.core.admin.module.repository');
        $module = $moduleRepository->getModuleById($moduleId);

        $addOnsAdminDataProvider = $this->get('prestashop.core.admin.data_provider.module_interface');
        $addOnsAdminDataProvider->generateAddonsUrls(
            AddonsCollection::createFrom([$module])
        );

        $modulePresenter = $this->get('prestashop.adapter.presenter.module');
        $moduleToPresent = $modulePresenter->present($module);

        return $this->render(
            '@PrestaShop/Admin/Module/Includes/modal_read_more_content.html.twig',
            [
                'module' => $moduleToPresent,
                'level' => $this->authorizationLevel(self::CONTROLLER_NAME),
            ]
        );
    }

    /**
     * Controller responsible for displaying "Catalog Module Grid" section of Module management pages with ajax.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function refreshCatalogAction(Request $request)
    {
        $deniedAccess = $this->checkPermissions(
            [
                PageVoter::LEVEL_READ,
                PageVoter::LEVEL_CREATE,
                PageVoter::LEVEL_DELETE,
                PageVoter::LEVEL_UPDATE,
            ]
        );
        if (null !== $deniedAccess) {
            return $deniedAccess;
        }

        $modulesProvider = $this->get('prestashop.core.admin.data_provider.module_interface');
        $moduleRepository = $this->get('prestashop.core.admin.module.repository');
        $responseArray = [];

        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE | AddonListFilterType::SERVICE)
            ->setStatus(~AddonListFilterStatus::INSTALLED);

        try {
            $modulesFromRepository = AddonsCollection::createFrom($moduleRepository->getFilteredList($filters));
            $modulesProvider->generateAddonsUrls($modulesFromRepository);

            $modules = $modulesFromRepository->toArray();
            shuffle($modules);
            $categories = $this->getCategories($modulesProvider, $modules);

            $responseArray['domElements'][] = $this->constructJsonCatalogCategoriesMenuResponse($categories);
            $responseArray['domElements'][] = $this->constructJsonCatalogBodyResponse(
                $categories,
                $modules
            );
            $responseArray['status'] = true;
        } catch (Exception $e) {
            $responseArray['msg'] = $this->trans(
                'Cannot get catalog data, please try again later. Reason: %error_details%',
                'Admin.Modules.Notification',
                array('%error_details%' => print_r($e->getMessage(), true))
            );
            $responseArray['status'] = false;
        }

        return new JsonResponse($responseArray);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function moduleAction(Request $request)
    {
        $deniedAccess = $this->checkPermissions(
            [
                PageVoter::LEVEL_READ,
                PageVoter::LEVEL_UPDATE,
                PageVoter::LEVEL_CREATE,
                PageVoter::LEVEL_DELETE,
            ]
        );
        if (null !== $deniedAccess) {
            return $deniedAccess;
        }

        if ($this->isDemoModeEnabled()) {
            return $this->getDisabledFunctionalityResponse($request);
        }

        $action = $request->get('action');
        $module = $request->get('module_name');
        $moduleManager = $this->container->get('prestashop.module.manager');
        $moduleManager->setActionParams($request->request->get('actionParams', []));
        $moduleRepository = $this->container->get('prestashop.core.admin.module.repository');
        $modulesProvider = $this->container->get('prestashop.core.admin.data_provider.module_interface');
        $response = [$module => []];

        if (!method_exists($moduleManager, $action)) {
            $response[$module]['status'] = false;
            $response[$module]['msg'] = $this->trans('Invalid action', 'Admin.Notifications.Error');

            return new JsonResponse($response);
        }

        $actionTitle = str_replace('_', ' ', $action);

        try {
            $response[$module]['status'] = $moduleManager->{$action}($module);

            if ($response[$module]['status'] === true) {
                $response[$module]['msg'] = $this->trans(
                    '%action% action on module %module% succeeded.',
                    'Admin.Modules.Notification',
                    [
                        '%action%' => ucfirst($actionTitle),
                        '%module%' => $module,
                    ]
                );

                if ($action !== 'uninstall') {
                    $response[$module]['module_name'] = $module;
                    $response[$module]['is_configurable'] = (bool) $this
                        ->get('prestashop.core.admin.module.repository')
                        ->getModule($module)
                        ->attributes
                        ->get('is_configurable');
                }
            } elseif ($response[$module]['status'] === false) {
                $error = $moduleManager->getError($module);
                $response[$module]['msg'] = $this->trans(
                    'Cannot %action% module %module%. %error_details%',
                    'Admin.Modules.Notification',
                    [
                        '%action%' => $actionTitle,
                        '%module%' => $module,
                        '%error_details%' => $error,
                    ]
                );
            } else {
                $response[$module]['status'] = false;
                $response[$module]['msg'] = $this->trans(
                    '%module% did not return a valid response on %action% action.',
                    'Admin.Modules.Notification',
                    [
                        '%module%' => $module,
                        '%action%' => $actionTitle,
                    ]
                );
            }
        } catch (UnconfirmedModuleActionException $e) {
            $collection = AddonsCollection::createFrom(array($e->getModule()));
            $modules = $modulesProvider->generateAddonsUrls($collection);
            $response[$module] = array_replace(
                $response[$module],
                [
                    'status' => false,
                    'confirmation_subject' => $e->getSubject(),
                    'module' => $this
                        ->container
                        ->get('prestashop.adapter.presenter.module')
                        ->presentCollection($modules)[0],
                    'msg' => $this->trans(
                        'Confirmation needed by module %module% on %action% (%subject%).',
                        'Admin.Modules.Notification',
                        [
                            '%subject%' => $e->getSubject(),
                            '%action%' => $e->getAction(),
                            '%module%' => $module,
                        ]
                    ),
                ]
            );
        } catch (Exception $e) {
            $response[$module]['status'] = false;
            $response[$module]['msg'] = $this->trans(
                'Exception thrown by module %module% on %action%. %error_details%',
                'Admin.Modules.Notification',
                [
                    '%action%' => $actionTitle,
                    '%module%' => $module,
                    '%error_details%' => $e->getMessage(),
                ]
            );

            $logger = $this->container->get('logger');
            $logger->error($response[$module]['msg']);
        }

        if ($response[$module]['status'] === true && $action != 'uninstall') {
            $moduleInstance = $moduleRepository->getModule($module);
            $collection = AddonsCollection::createFrom([$moduleInstance]);
            $response[$module]['action_menu_html'] = $this->container->get('templating')->render(
                '@PrestaShop/Admin/Module/Includes/action_menu.html.twig',
                [
                    'module' => $this->container->get('prestashop.adapter.presenter.module')
                    ->presentCollection($modulesProvider->generateAddonsUrls($collection))[0],
                    'level' => $this->authorizationLevel(self::CONTROLLER_NAME),
                ]
            );
        }

        return new JsonResponse($response);
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
        if ($this->isDemoModeEnabled()) {
            return new JsonResponse(
                [
                    'status' => false,
                    'msg' => $this->getDemoModeErrorMessage(),
                ]
            );
        }

        $deniedAccess = $this->checkPermissions(
            [
                PageVoter::LEVEL_CREATE,
                PageVoter::LEVEL_DELETE,
            ]
        );
        if (null !== $deniedAccess) {
            return $deniedAccess;
        }

        $moduleManager = $this->get('prestashop.module.manager');
        $moduleZipManager = $this->get('prestashop.module.zip.manager');

        try {
            $fileUploaded = $request->files->get('file_uploaded');
            $constraints = [
                new Assert\NotNull(),
                new Assert\File(
                    [
                        'maxSize' => ini_get('upload_max_filesize'),
                        'mimeTypes' => [
                            'application/zip',
                            'application/x-gzip',
                            'application/gzip',
                            'application/x-gtar',
                            'application/x-tgz',
                        ],
                    ]
                ),
            ];

            $violations = $this->get('validator')->validate($fileUploaded, $constraints);
            if (0 !== count($violations)) {
                $violationsMessages = [];
                foreach ($violations as $violation) {
                    $violationsMessages[] = $violation->getMessage();
                }

                throw new Exception(implode(PHP_EOL, $violationsMessages));
            }

            $moduleName = $moduleZipManager->getName($fileUploaded->getPathname());

            // Install the module
            $installationResponse = array(
                'status' => $moduleManager->install($fileUploaded->getPathname()),
                'msg' => '',
                'module_name' => $moduleName,
            );

            if ($installationResponse['status'] === null) {
                $installationResponse['status'] = false;
                $installationResponse['msg'] = $this->trans(
                    '%module% did not return a valid response on installation.',
                    'Admin.Modules.Notification',
                    ['%module%' => $moduleName]
                );
            } elseif ($installationResponse['status'] === true) {
                $installationResponse['msg'] = $this->trans(
                    'Installation of module %module% was successful.',
                    'Admin.Modules.Notification',
                    ['%module%' => $moduleName]
                );
                $installationResponse['is_configurable'] = (bool) $this->get('prestashop.core.admin.module.repository')
                                                         ->getModule($moduleName)
                                                         ->attributes
                                                         ->get('is_configurable');
            } else {
                $error = $moduleManager->getError($moduleName);
                $installationResponse['msg'] = $this->trans(
                    'Installation of module %module% failed. %error%',
                    'Admin.Modules.Notification',
                    [
                        '%module%' => $moduleName,
                        '%error%' => $error,
                    ]
                );
            }
        } catch (UnconfirmedModuleActionException $e) {
            $collection = AddonsCollection::createFrom(array($e->getModule()));
            $modules = $this->get('prestashop.core.admin.data_provider.module_interface')
                     ->generateAddonsUrls($collection);
            $installationResponse = [
                'status' => false,
                'confirmation_subject' => $e->getSubject(),
                'module' => $this->get('prestashop.adapter.presenter.module')->presentCollection($modules)[0],
                'msg' => $this->trans(
                    'Confirmation needed by module %module% on %action% (%subject%).',
                    'Admin.Modules.Notification',
                    [
                        '%subject%' => $e->getSubject(),
                        '%action%' => $e->getAction(),
                        '%module%' => $moduleName,
                    ]
                ),
            ];
        } catch (Exception $e) {
            if (isset($moduleName)) {
                $moduleManager->disable($moduleName);
            }

            $installationResponse = [
                'status' => false,
                'msg' => $e->getMessage(),
            ];
        }

        return new JsonResponse($installationResponse);
    }

    private function getModulesByInstallation($modulesSelectList = null)
    {
        $addonsProvider = $this->get('prestashop.core.admin.data_provider.module_interface');
        $moduleRepository = $this->get('prestashop.core.admin.module.repository');
        $modulePresenter = $this->get('prestashop.adapter.presenter.module');
        $tabRepository = $this->get('prestashop.core.admin.tab.repository');

        $modulesOnDisk = AddonsCollection::createFrom($moduleRepository->getList());

        $modulesList = array(
            'installed' => [],
            'not_installed' => [],
        );

        $modulesOnDisk = $addonsProvider->generateAddonsUrls($modulesOnDisk);
        foreach ($modulesOnDisk as $module) {
            if (!isset($modulesSelectList) || in_array($module->get('name'), $modulesSelectList)) {
                $perm = true;
                if ($module->get('id')) {
                    $perm &= Module::getPermissionStatic(
                        $module->get('id'),
                        'configure',
                        $this->getContext()->employee
                    );
                } else {
                    $id_admin_module = $tabRepository->findOneIdByClassName('AdminModules');
                    $access = Profile::getProfileAccess(
                        $this->getContext()->employee->id_profile,
                        $id_admin_module
                    );

                    $perm &= !$access['edit'];
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

    private function getTopMenuData(array $topMenuData, $activeMenu = null)
    {
        if (isset($activeMenu)) {
            if (!isset($topMenuData[$activeMenu])) {
                throw new Exception(
                    sprintf(
                        'Menu \'%s\' not found in Top Menu data',
                        $activeMenu
                    ),
                    1
                );
            }

            $topMenuData[$activeMenu]->class = 'active';
        }

        return $topMenuData;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    private function getDisabledFunctionalityResponse(Request $request)
    {
        $content = array(
            $request->get('module_name') => array(
                'status' => false,
                'msg' => $this->getDemoModeErrorMessage(),
            ),
        );

        return new JsonResponse($content);
    }

    /**
     * Construct Json struct for catalog body response.
     *
     * @param array $categories
     * @param array $modules
     *
     * @return array
     */
    private function constructJsonCatalogBodyResponse(
        array $categories,
        array $modules
    ) {
        $formattedContent = [];
        $formattedContent['selector'] = '.module-catalog-page';
        $formattedContent['content'] = $this->render(
            '@PrestaShop/Admin/Module/Includes/sorting.html.twig',
            array(
                'totalModules' => count($modules),
            )
        )->getContent();

        $errorMessage = $this->trans('You do not have permission to add this.', 'Admin.Notifications.Error');

        $formattedContent['content'] .= $this->render(
            '@PrestaShop/Admin/Module/catalog-refresh.html.twig',
            array(
                'categories' => $categories['categories'],
                'requireAddonsSearch' => true,
                'level' => $this->authorizationLevel(self::CONTROLLER_NAME),
                'errorMessage' => $errorMessage,
            )
        )->getContent();

        return $formattedContent;
    }

    /**
     * Construct json struct from top menu.
     *
     * @param array $categories
     *
     * @return array
     */
    private function constructJsonCatalogCategoriesMenuResponse(array $categories)
    {
        $formattedContent = [];
        $formattedContent['selector'] = '.module-menu-item';
        $formattedContent['content'] = $this->render(
            '@PrestaShop/Admin/Module/Includes/dropdown_categories_catalog.html.twig',
            array(
                'topMenuData' => $this->getTopMenuData($categories),
            )
        )->getContent();

        return $formattedContent;
    }

    /**
     * Check user permission.
     *
     * @param array $pageVoter
     *
     * @return void|JsonResponse
     */
    private function checkPermissions(array $pageVoter)
    {
        if (!in_array(
            $this->authorizationLevel(self::CONTROLLER_NAME),
            $pageVoter
        )
        ) {
            return new JsonResponse(
                [
                    'status' => false,
                    'msg' => $this->trans('You do not have permission to add this.', 'Admin.Notifications.Error'),
                ]
            );
        }
    }

    /**
     * Get categories and its modules.
     *
     * @param array $modules List of installed modules
     *
     * @return array
     */
    private function getCategories(AdminModuleDataProvider $modulesProvider, array $modules)
    {
        /** @var CategoriesProvider */
        $categories = $this->get('prestashop.categories_provider')->getCategoriesMenu($modules);

        foreach ($categories['categories']->subMenu as $category) {
            $collection = AddonsCollection::createFrom($category->modules);
            $modulesProvider->generateAddonsUrls($collection);
            $category->modules = $this->get('prestashop.adapter.presenter.module')
                               ->presentCollection($category->modules);
        }

        return $categories;
    }
}
