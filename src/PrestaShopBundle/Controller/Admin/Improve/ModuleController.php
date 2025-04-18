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
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\Module as ModuleAdapter;
use PrestaShop\PrestaShop\Core\Module\ModuleCollection;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\SourceHandlerNotFoundException;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\ZipSourceHandler;
use PrestaShop\PrestaShop\Core\Security\Permission;
use PrestaShopBundle\Controller\Admin\Improve\Modules\ModuleAbstractController;
use PrestaShopBundle\Entity\ModuleHistory;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Service\DataProvider\Admin\CategoriesProvider;
use PrestaShopBundle\Twig\Layout\MenuLink;
use Symfony\Component\Form\Util\ServerParams;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

/**
 * Responsible of "Improve > Modules > Modules & Services > Catalog / Manage" page display.
 */
class ModuleController extends ModuleAbstractController
{
    public const CONTROLLER_NAME = 'ADMINMODULESSF';

    public const MAX_MODULES_DISPLAYED = 6;

    public function __construct(
        private readonly Environment $twig,
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Controller responsible for displaying "Catalog Module Grid" section of Module management pages with ajax.
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', 'ADMINMODULESSF_')")]
    public function manageAction(
        AdminModuleDataProvider $modulesProvider,
        CategoriesProvider $categoriesProvider
    ): Response {
        $installedProducts = $this->getModuleRepository()->getList();

        $moduleErrors = $installedProducts->getErrors();
        foreach ($moduleErrors as $moduleError) {
            $this->addFlash('warning', $moduleError->getMessage());
        }

        $categories = $this->getCategories($categoriesProvider, $modulesProvider, $installedProducts);
        $bulkActions = [
            'bulk-install' => $this->trans('Install', [], 'Admin.Modules.Actions'),
            'bulk-uninstall' => $this->trans('Uninstall', [], 'Admin.Modules.Actions'),
            'bulk-disable' => $this->trans('Disable', [], 'Admin.Modules.Actions'),
            'bulk-enable' => $this->trans('Enable', [], 'Admin.Modules.Actions'),
            'bulk-reset' => $this->trans('Reset', [], 'Admin.Modules.Actions'),
            'bulk-delete' => $this->trans('Delete', [], 'Admin.Modules.Actions'),
        ];

        return $this->render(
            '@PrestaShop/Admin/Module/manage.html.twig',
            [
                'maxModulesDisplayed' => self::MAX_MODULES_DISPLAYED,
                'bulkActions' => $bulkActions,
                'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
                'layoutTitle' => $this->trans('Module manager', [], 'Admin.Modules.Feature'),
                'categories' => $categories['categories'],
                'topMenuData' => $categories,
                'requireBulkActions' => true,
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink('AdminModules'),
                'requireFilterStatus' => true,
                'level' => $this->getAuthorizationLevel(self::CONTROLLER_NAME),
                'errorMessage' => $this->trans('You do not have permission to add this.', [], 'Admin.Notifications.Error'),
            ]
        );
    }

    /**
     * @param string $module_name
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', 'ADMINMODULESSF_') || is_granted('create', 'ADMINMODULESSF_') || is_granted('update', 'ADMINMODULESSF_') || is_granted('delete', 'ADMINMODULESSF_')")]
    public function configureModuleAction(
        string $module_name,
        LegacyContext $legacyContext,
    ): Response {
        // Get accessed module object
        /** @var ModuleAdapter $module */
        $module = $this->getModuleRepository()->getModule($module_name);
        if (!$module->getInstance()) {
            $this->addFlash('error', $this->trans(
                'The module "%modulename%" cannot be found',
                ['%modulename%' => $module_name],
                'Admin.Modules.Notification'
            ));
            $layoutSubTitle = null;
        } else {
            $this->saveModuleHistory($module);
            $layoutSubTitle = $module->getInstance()->displayName;
        }

        // This controller is not purely migrated, in the sense that it still relies on the legacy layout because module implementing
        // getContent need the default theme to be working as expected
        $smarty = $legacyContext->getSmarty();
        $smarty->setTemplateDir([
            _PS_BO_ALL_THEMES_DIR_ . 'default/template/',
            _PS_OVERRIDE_DIR_ . 'controllers/admin/templates',
        ]);

        // Force legacy layout to load legacy assets like AdminController::setMedia does
        $this->getLegacyControllerContext()->loadLegacyMedia();
        // Only after can we add additional plugins (to be sure jquery is loaded before the plugins)
        $this->getLegacyControllerContext()->addJqueryPlugin(['autocomplete', 'fancybox', 'tablefilter']);

        if (method_exists($module->getInstance(), 'getContent')) {
            $moduleContent = $module->getInstance()->getContent();
        } else {
            $moduleContent = null;
            $this->addFlash('error', $this->trans('Module %s has no getContent() method', [$module->getInstance()->name], 'Admin.Modules.Notification'));
        }

        return $this->render(
            '@PrestaShop/Admin/Module/configure.html.twig',
            [
                'moduleContent' => $moduleContent,
                'showContentHeader' => true,
                'layoutHeaderToolbarBtn' => $this->getConfigureToolbarButtons($module),
                'translationLinks' => $this->getTranslationLinks($module, $legacyContext),
                'layoutTitle' => $this->trans('Configure', [], 'Admin.Modules.Feature'),
                // Force metaTitle to match the legacy page one (based on the parent)
                'metaTitle' => $this->trans('Module Manager', [], 'Admin.Navigation.Menu'),
                'layoutSubTitle' => $layoutSubTitle,
                'breadcrumbLinks' => [
                    'container' => new MenuLink(
                        $this->trans('Modules', [], 'Admin.Modules.Feature'),
                        $this->generateUrl('admin_module_manage'),
                    ),
                    'tab' => new MenuLink(
                        $this->trans('Configure', [], 'Admin.Modules.Feature'),
                        $this->generateUrl('admin_module_configure_action', ['module_name' => $module_name]),
                        'build',
                    ),
                ],
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink('AdminModules'),
            ]
        );
    }

    public function moduleAction(
        Request $request,
        AdminModuleDataProvider $modulesProvider,
        ModuleManager $moduleManager,
    ): JsonResponse {
        $action = $request->get('action');

        switch ($action) {
            case ModuleAdapter::ACTION_UPGRADE:
            case ModuleAdapter::ACTION_RESET:
            case ModuleAdapter::ACTION_ENABLE:
            case ModuleAdapter::ACTION_DISABLE:
                $deniedAccess = !$this->isGranted(Permission::UPDATE, self::CONTROLLER_NAME);
                break;
            case ModuleAdapter::ACTION_INSTALL:
                $deniedAccess = !$this->isGranted(Permission::CREATE, self::CONTROLLER_NAME);
                break;
            case ModuleAdapter::ACTION_DELETE:
            case ModuleAdapter::ACTION_UNINSTALL:
                $deniedAccess = !$this->isGranted(Permission::DELETE, self::CONTROLLER_NAME);
                break;

            default:
                $deniedAccess = false;
        }

        if ($deniedAccess) {
            return new JsonResponse(
                [
                    'status' => false,
                    'msg' => $this->trans('You do not have permission to add this.', [], 'Admin.Notifications.Error'),
                ]
            );
        }

        if ($this->isDemoModeEnabled()) {
            return $this->getDisabledFunctionalityResponse($request);
        }

        $moduleName = $request->get('module_name');
        $source = $request->query->get('source');
        $response = [$moduleName => []];

        if (!method_exists($moduleManager, $action)) {
            $response[$moduleName]['status'] = false;
            $response[$moduleName]['msg'] = $this->trans('Invalid action', [], 'Admin.Notifications.Error');

            return new JsonResponse($response);
        }

        $actionTitle = AdminModuleDataProvider::ACTIONS_TRANSLATION_LABELS[$action];
        try {
            $args = [$moduleName];
            if ($source !== null) {
                $args[] = $source;
            }
            if ($action === ModuleAdapter::ACTION_UNINSTALL) {
                $args[] = (bool) ($request->request->all('actionParams')['deletion'] ?? false);
                /** @var ModuleAdapter $moduleInstance */
                $moduleInstance = $this->getModuleRepository()->getModule($moduleName);
                $response[$moduleName]['refresh_needed'] = $this->moduleNeedsReload($moduleInstance);
                $response[$moduleName]['has_download_url'] = $moduleInstance->attributes->has('download_url');
            }
            if ($action === ModuleAdapter::ACTION_DELETE) {
                $moduleInstance = $this->getModuleRepository()->getModule($moduleName);
                $response[$moduleName]['refresh_needed'] = false;
                $response[$moduleName]['has_download_url'] = $moduleInstance->attributes->has('download_url');
            }

            $response[$moduleName]['status'] = call_user_func([$moduleManager, $action], ...$args);
        } catch (Exception $e) {
            $response[$moduleName]['status'] = false;
            $response[$moduleName]['msg'] = $this->trans(
                'Cannot %action% module %module%. %error_details%',
                [
                    '%action%' => $actionTitle,
                    '%module%' => $moduleName,
                    '%error_details%' => $e->getMessage(),
                ],
                'Admin.Modules.Notification',
            );

            return new JsonResponse($response);
        }

        /** @var ModuleAdapter $moduleInstance */
        $moduleInstance = $this->getModuleRepository()->getModule($moduleName);
        if ($response[$moduleName]['status'] === true) {
            if (!isset($response[$moduleName]['refresh_needed'])) {
                $response[$moduleName]['refresh_needed'] = $this->moduleNeedsReload($moduleInstance);
            }
            $response[$moduleName]['msg'] = $this->trans(
                '%action% action on module %module% succeeded.',
                [
                    '%action%' => ucfirst($actionTitle),
                    '%module%' => $moduleName,
                ],
                'Admin.Modules.Notification',
            );
            if ($action !== 'uninstall' && $action !== 'delete') {
                $response[$moduleName]['module_name'] = $moduleName;
                $response[$moduleName]['is_configurable'] = (bool) $moduleInstance->attributes->get('is_configurable');
            }

            $collection = ModuleCollection::createFrom([$moduleInstance]);
            $collectionWithActionUrls = $modulesProvider->setActionUrls($collection);

            $collectionPresented = $this->getModulePresenter()->presentCollection($collectionWithActionUrls);
            $response[$moduleName]['action_menu_html'] = $this->twig->render(
                '@PrestaShop/Admin/Module/Includes/action_menu.html.twig',
                [
                    'module' => $collectionPresented[0],
                    'level' => $this->getAuthorizationLevel(self::CONTROLLER_NAME),
                ]
            );
        } else {
            $response[$moduleName]['msg'] = $this->trans(
                'Cannot %action% module %module%. %error_details%',
                [
                    '%action%' => $actionTitle,
                    '%module%' => $moduleName,
                    '%error_details%' => $moduleManager->getError($moduleName),
                ],
                'Admin.Modules.Notification',
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
    public function importModuleAction(
        Request $request,
        ModuleManager $moduleManager,
        ZipSourceHandler $zipSource,
    ): JsonResponse {
        if ($this->isDemoModeEnabled()) {
            return new JsonResponse(
                [
                    'status' => false,
                    'msg' => $this->getDemoModeErrorMessage(),
                ]
            );
        }

        if (!$this->isGranted(Permission::CREATE, self::CONTROLLER_NAME) && !$this->isGranted(Permission::DELETE, self::CONTROLLER_NAME)) {
            return new JsonResponse(
                [
                    'status' => false,
                    'msg' => $this->trans('You do not have permission to add this.', [], 'Admin.Notifications.Error'),
                ]
            );
        }

        $serverParams = new ServerParams();
        $moduleName = '';

        try {
            if ($serverParams->hasPostMaxSizeBeenExceeded()) {
                throw new Exception($this->trans(
                    'Your uploaded file might exceed the [1]upload_max_filesize[/1] and the [1]post_max_size[/1] directives in [1]php.ini[/1], please check your server configuration.',
                    [
                        '[1]' => '<i>',
                        '[/1]' => '</i>',
                    ],
                    'Admin.Notifications.Error',
                ));
            }

            $fileUploaded = $request->files->get('file_uploaded');
            $constraints = [
                new Assert\NotNull(
                    [
                        'message' => $this->trans(
                            'The file is missing.',
                            [],
                            'Admin.Notifications.Error',
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

            $violations = $this->validator->validate($fileUploaded, $constraints);
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

            if ($installationResponse['status'] === true) {
                $installationResponse['msg'] = $this->trans(
                    'Installation of module %module% was successful.',
                    ['%module%' => $moduleName],
                    'Admin.Modules.Notification',
                );
                $installationResponse['is_configurable'] = (bool) $this->getModuleRepository()
                    ->getModule($moduleName)
                    ->attributes
                    ->get('is_configurable');
            } else {
                $error = $moduleManager->getError($moduleName);
                $installationResponse['msg'] = $this->trans(
                    'Installation of module %module% failed. %error%',
                    [
                        '%module%' => $moduleName,
                        '%error%' => $error,
                    ],
                    'Admin.Modules.Notification',
                );
            }
        } catch (SourceHandlerNotFoundException) {
            $installationResponse['status'] = false;
            $installationResponse['msg'] = $this->trans(
                'Installation of module %module% failed. %error%',
                [
                    '%module%' => $moduleName,
                    '%error%' => $this->trans(
                        'Impossible to install from source',
                        [],
                        'Admin.Modules.Notification'
                    ),
                ],
                'Admin.Modules.Notification',
            );
        } catch (Exception $e) {
            try {
                $moduleManager->disable($moduleName);
            } catch (Exception) {
            }
            $installationResponse['status'] = false;
            $installationResponse['msg'] = $this->trans(
                'Installation of module %module% failed. %error%',
                [
                    '%module%' => $moduleName,
                    '%error%' => $e->getMessage(),
                ],
                'Admin.Modules.Notification',
            );
        }

        return new JsonResponse($installationResponse);
    }

    private function saveModuleHistory(ModuleAdapter $module): void
    {
        // Get current employee Id
        $currentEmployeeId = $this->getEmployeeContext()->getEmployee()->getId();
        // Get accessed module DB ID
        $moduleAccessedId = (int) $module->database->get('id');

        // Save history for this module
        $moduleHistory = $this->entityManager
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

        $this->entityManager->persist($moduleHistory);
        $this->entityManager->flush();
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
    private function getDisabledFunctionalityResponse(Request $request): JsonResponse
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
     * Get categories and its modules.
     *
     * @return array
     */
    private function getCategories(CategoriesProvider $categoriesProvider, AdminModuleDataProvider $modulesProvider, ModuleCollection $modules): array
    {
        $categories = $categoriesProvider->getCategoriesMenu($modules);

        foreach ($categories['categories']->subMenu as $category) {
            $collection = ModuleCollection::createFrom($category->modules);
            $modulesProvider->setActionUrls($collection);
            $category->modules = $this->getModulePresenter()
                ->presentCollection($category->modules);
        }

        return $categories;
    }

    protected function getTranslationLinks(ModuleAdapter $module, LegacyContext $legacyContext): array
    {
        $translationLinks = [];
        $isNewTranslateSystem = $module->getInstance()->isUsingNewTranslationSystem();
        foreach ($this->getLegacyControllerContext()->getLanguages() as $lang) {
            if ($isNewTranslateSystem) {
                $translationLinks[$lang['name']] = $this->generateUrl('admin_international_translation_overview', [
                    'lang' => $lang['iso_code'],
                    'type' => 'modules',
                    'selected' => $module->getInstance()->name,
                    'locale' => $lang['locale'],
                ]);
            } else {
                $translationLinks[$lang['name']] = $legacyContext->getAdminLink('AdminTranslations', true, [
                    'type' => 'modules',
                    'module' => $module->getInstance()->name,
                    'lang' => $lang['iso_code'],
                ]);
            }
        }

        return $translationLinks;
    }

    /**
     * Common method for all module related controller for getting the header buttons.
     *
     * @return array
     */
    protected function getConfigureToolbarButtons(?ModuleAdapter $module): array
    {
        $toolbarButtons = [
            'module-back' => [
                'href' => $this->generateUrl('admin_module_manage'),
                'desc' => $this->trans('Back', [], 'Admin.Global'),
                'icon' => 'arrow_back',
                'help' => $this->trans('Module Manager', [], 'Admin.Navigation.Menu'),
            ],
        ];

        if ($this->isGranted(Permission::CREATE, self::CONTROLLER_NAME) || $this->isGranted(Permission::DELETE, self::CONTROLLER_NAME)) {
            $toolbarButtons['module-translate'] = [
                'href' => '#',
                'desc' => $this->trans('Translate', [], 'Admin.Modules.Feature'),
                'icon' => 'flag',
                'help' => $this->trans('Translate', [], 'Admin.Modules.Feature'),
                'modal_target' => '#moduleTradLangSelect',
            ];

            if ($module !== null) {
                $toolbarButtons['module-hook'] = [
                    'href' => $this->generateUrl('admin_modules_positions', ['show_modules' => (int) $module->database->get('id')]),
                    'desc' => $this->trans('Manage hooks', [], 'Admin.Modules.Feature'),
                    'icon' => 'anchor',
                    'help' => $this->trans('Manage hooks', [], 'Admin.Modules.Feature'),
                ];
            }
        }

        return $toolbarButtons;
    }
}
