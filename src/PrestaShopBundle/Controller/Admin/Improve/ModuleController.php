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

namespace PrestaShopBundle\Controller\Admin\Improve;

use DateTime;
use Db;
use Exception;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\Module as ModuleAdapter;
use PrestaShop\PrestaShop\Core\Module\ModuleCollection;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\SourceHandlerNotFoundException;
use PrestaShop\PrestaShop\Core\Security\Permission;
use PrestaShopBundle\Controller\Admin\Improve\Modules\ModuleAbstractController;
use PrestaShopBundle\Entity\ModuleHistory;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\DataProvider\Admin\CategoriesProvider;
use Symfony\Component\Form\Util\ServerParams;
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
    public const CONTROLLER_NAME = 'ADMINMODULESSF';

    public const MAX_MODULES_DISPLAYED = 6;

    /**
     * Controller responsible for displaying "Catalog Module Grid" section of Module management pages with ajax.
     *
     * @AdminSecurity("is_granted('read', 'ADMINMODULESSF_')")
     *
     * @return Response
     */
    public function manageAction()
    {
        $modulesProvider = $this->get('prestashop.core.admin.data_provider.module_interface');
        $moduleRepository = $this->get('prestashop.core.admin.module.repository');

        $installedProducts = $moduleRepository->getList();

        $categories = $this->getCategories($modulesProvider, $installedProducts);
        $bulkActions = [
            'bulk-install' => $this->trans('Install', 'Admin.Actions'),
            'bulk-uninstall' => $this->trans('Uninstall', 'Admin.Actions'),
            'bulk-disable' => $this->trans('Disable', 'Admin.Actions'),
            'bulk-enable' => $this->trans('Enable', 'Admin.Actions'),
            'bulk-reset' => $this->trans('Reset', 'Admin.Actions'),
            'bulk-enable-mobile' => $this->trans('Enable Mobile', 'Admin.Modules.Feature'),
            'bulk-disable-mobile' => $this->trans('Disable Mobile', 'Admin.Modules.Feature'),
            'bulk-delete' => $this->trans('Delete', 'Admin.Modules.Feature'),
        ];

        return $this->render(
            '@PrestaShop/Admin/Module/manage.html.twig',
            [
                'bulkActions' => $bulkActions,
                'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
                'layoutTitle' => $this->trans('Module manager', 'Admin.Navigation.Menu'),
                'categories' => $categories['categories'],
                'topMenuData' => $this->getTopMenuData($categories),
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
     * @AdminSecurity(
     *     "is_granted('read', 'ADMINMODULESSF_') || is_granted('create', 'ADMINMODULESSF_') || is_granted('update', 'ADMINMODULESSF_') || is_granted('delete', 'ADMINMODULESSF_')"
     * )
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
            ->getRepository(ModuleHistory::class)
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
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function moduleAction(Request $request)
    {
        $action = $request->get('action');

        switch ($action) {
            case ModuleAdapter::ACTION_UPGRADE:
            case ModuleAdapter::ACTION_RESET:
            case ModuleAdapter::ACTION_ENABLE:
            case ModuleAdapter::ACTION_DISABLE:
            case ModuleAdapter::ACTION_ENABLE_MOBILE:
            case ModuleAdapter::ACTION_DISABLE_MOBILE:
                $deniedAccess = $this->checkPermission(Permission::UPDATE);
                break;
            case ModuleAdapter::ACTION_INSTALL:
                $deniedAccess = $this->checkPermission(Permission::CREATE);
                break;
            case ModuleAdapter::ACTION_DELETE:
            case ModuleAdapter::ACTION_UNINSTALL:
                $deniedAccess = $this->checkPermission(Permission::DELETE);
                break;

            default:
                $deniedAccess = null;
        }

        if (null !== $deniedAccess) {
            return $deniedAccess;
        }

        if ($this->isDemoModeEnabled()) {
            return $this->getDisabledFunctionalityResponse($request);
        }

        $module = $request->get('module_name');
        $source = $request->query->get('source');
        $moduleManager = $this->container->get('prestashop.module.manager');
        $moduleRepository = $this->container->get('prestashop.core.admin.module.repository');
        $modulesProvider = $this->container->get('prestashop.core.admin.data_provider.module_interface');
        $response = [$module => []];

        if (!method_exists($moduleManager, $action)) {
            $response[$module]['status'] = false;
            $response[$module]['msg'] = $this->trans('Invalid action', 'Admin.Notifications.Error');

            return new JsonResponse($response);
        }

        $actionTitle = AdminModuleDataProvider::ACTIONS_TRANSLATION_LABELS[$action];

        try {
            $args = [$module];
            if ($source !== null) {
                $args[] = $source;
            }
            if ($action === ModuleAdapter::ACTION_UNINSTALL) {
                $args[] = (bool) ($request->request->all('actionParams')['deletion'] ?? false);
                $response[$module]['refresh_needed'] = $this->moduleNeedsReload($moduleRepository->getModule($module));
            }
            if ($action === ModuleAdapter::ACTION_DELETE) {
                $response[$module]['refresh_needed'] = false;
            }
            $systemCacheClearEnabled = filter_var(
                $request->request->all('actionParams')['cacheClearEnabled'] ?? true,
                FILTER_VALIDATE_BOOLEAN
            );
            if (!$systemCacheClearEnabled) {
                $moduleManager->disableSystemClearCache();
            }
            $response[$module]['status'] = call_user_func([$moduleManager, $action], ...$args);
        } catch (Exception $e) {
            $response[$module]['status'] = false;
            $response[$module]['msg'] = $this->trans(
                'Cannot %action% module %module%. %error_details%',
                'Admin.Modules.Notification',
                [
                    '%action%' => $actionTitle,
                    '%module%' => $module,
                    '%error_details%' => $e->getMessage(),
                ]
            );

            return new JsonResponse($response);
        }

        $moduleInstance = $moduleRepository->getModule($module);
        if ($response[$module]['status'] === true) {
            if (!isset($response[$module]['refresh_needed'])) {
                $response[$module]['refresh_needed'] = $this->moduleNeedsReload($moduleInstance);
            }
            $response[$module]['msg'] = $this->trans(
                '%action% action on module %module% succeeded.',
                'Admin.Modules.Notification',
                [
                    '%action%' => ucfirst($actionTitle),
                    '%module%' => $module,
                ]
            );
            if ($action !== 'uninstall' && $action !== 'delete') {
                $response[$module]['module_name'] = $module;
                $response[$module]['is_configurable'] = (bool) $moduleInstance->attributes->get('is_configurable');
            }

            $collection = ModuleCollection::createFrom([$moduleInstance]);
            $collectionWithActionUrls = $modulesProvider->setActionUrls($collection);

            $modulePresenter = $this->get('prestashop.adapter.presenter.module');
            $collectionPresented = $modulePresenter->presentCollection($collectionWithActionUrls);

            $response[$module]['action_menu_html'] = $this->container->get('twig')->render(
                '@PrestaShop/Admin/Module/Includes/action_menu.html.twig',
                [
                    'module' => $collectionPresented[0],
                    'level' => $this->authorizationLevel(self::CONTROLLER_NAME),
                ]
            );
        } else {
            $response[$module]['msg'] = $this->trans(
                'Cannot %action% module %module%. %error_details%',
                'Admin.Modules.Notification',
                [
                    '%action%' => $actionTitle,
                    '%module%' => $module,
                    '%error_details%' => $moduleManager->getError($module),
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
                Permission::LEVEL_CREATE,
                Permission::LEVEL_DELETE,
            ]
        );
        if (null !== $deniedAccess) {
            return $deniedAccess;
        }

        $moduleManager = $this->get('prestashop.module.manager');
        $zipSource = $this->get('prestashop.module.sourcehandler.zip');
        $serverParams = new ServerParams();
        $moduleName = '';

        try {
            if ($serverParams->hasPostMaxSizeBeenExceeded()) {
                throw new Exception($this->trans(
                    'Your uploaded file might exceed the [1]upload_max_filesize[/1] and the [1]post_max_size[/1] directives in [1]php.ini[/1], please check your server configuration.',
                    'Admin.Notifications.Error',
                    [
                        '[1]' => '<i>',
                        '[/1]' => '</i>',
                    ]
                ));
            }

            $fileUploaded = $request->files->get('file_uploaded');
            $constraints = [
                new Assert\NotNull(
                    [
                        'message' => $this->trans(
                            'The file is missing.',
                            'Admin.Notifications.Error'
                        ),
                    ]
                ),
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

            $moduleName = $zipSource->getModuleName($fileUploaded->getPathname());

            $moduleWasAlreadyInstalled = $moduleManager->isInstalled($moduleName);
            $installationResult = $moduleManager->install($moduleName, $fileUploaded->getPathname());

            // Install the module
            $installationResponse = [
                'status' => $installationResult,
                'upgraded' => $installationResult && $moduleWasAlreadyInstalled,
                'msg' => '',
                'module_name' => $moduleName,
            ];

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
        } catch (SourceHandlerNotFoundException $e) {
            $installationResponse['status'] = false;
            $installationResponse['msg'] = $this->trans(
                'Installation of module %module% failed. %error%',
                'Admin.Modules.Notification',
                [
                    '%module%' => $moduleName,
                    '%error%' => $this->trans(
                        'Impossible to install from source',
                        'Admin.Modules.Notification'
                    ),
                ]
            );
        } catch (Exception $e) {
            try {
                $moduleManager->disable($moduleName);
            } catch (Exception $subE) {
            }
            $installationResponse['status'] = false;
            $installationResponse['msg'] = $this->trans(
                'Installation of module %module% failed. %error%',
                'Admin.Modules.Notification',
                [
                    '%module%' => $moduleName,
                    '%error%' => $e->getMessage(),
                ]
            );
        }

        return new JsonResponse($installationResponse);
    }

    private function getTopMenuData(array $topMenuData, $activeMenu = null)
    {
        if (isset($activeMenu)) {
            if (!isset($topMenuData[$activeMenu])) {
                throw new Exception(sprintf('Menu \'%s\' not found in Top Menu data', $activeMenu), 1);
            }

            $topMenuData[$activeMenu]->class = 'active';
        }

        return $topMenuData;
    }

    private function moduleNeedsReload(ModuleAdapter $module): bool
    {
        $instance = $module->getInstance();
        if (!empty($instance->getTabs())) {
            return true;
        }

        return !empty(Db::getInstance()->executeS(
            'SELECT 1 FROM `' . _DB_PREFIX_ . 'hook_module` hm
            INNER JOIN `' . _DB_PREFIX_ . 'hook` h ON h.id_hook = hm.id_hook
            WHERE hm.id_module = ' . (int) $instance->id . ' AND h.name = \'actionListModules\' LIMIT 1'
        ));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    private function getDisabledFunctionalityResponse(Request $request)
    {
        $content = [
            $request->get('module_name') => [
                'status' => false,
                'msg' => $this->getDemoModeErrorMessage(),
            ],
        ];

        return new JsonResponse($content);
    }

    /**
     * Check user permission.
     *
     * @param array $pageVoter
     *
     * @return JsonResponse|null
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

        return null;
    }

    /**
     * @param string $pageVoter
     *
     * @return JsonResponse|null
     */
    private function checkPermission($pageVoter)
    {
        if (!$this->isGranted($pageVoter, self::CONTROLLER_NAME)) {
            return new JsonResponse(
                [
                    'status' => false,
                    'msg' => $this->trans('You do not have permission to add this.', 'Admin.Notifications.Error'),
                ]
            );
        }

        return null;
    }

    /**
     * Get categories and its modules.
     *
     * @return array
     */
    private function getCategories(AdminModuleDataProvider $modulesProvider, ModuleCollection $modules)
    {
        /** @var CategoriesProvider $categoriesProvider */
        $categoriesProvider = $this->get('prestashop.categories_provider');
        $categories = $categoriesProvider->getCategoriesMenu($modules);

        foreach ($categories['categories']->subMenu as $category) {
            $collection = ModuleCollection::createFrom($category->modules);
            $modulesProvider->setActionUrls($collection);
            $category->modules = $this->get('prestashop.adapter.presenter.module')
                ->presentCollection($category->modules);
        }

        return $categories;
    }
}
