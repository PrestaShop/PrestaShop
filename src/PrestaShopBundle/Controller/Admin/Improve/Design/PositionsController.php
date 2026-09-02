<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Improve\Design;

use Dispatcher;
use Hook;
use Module as LegacyModule;
use PrestaShop\PrestaShop\Adapter\Hook\FormDataProvider\HookModuleFormDataProvider;
use PrestaShop\PrestaShop\Adapter\Hook\HookInformationProvider;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\Domain\Hook\Command\EditHookedModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Hook\Command\HookModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Hook\Command\UpdateHookStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\CannotUpdateHookException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\HookException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\HookNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\HookUpdateHookException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\ModuleAlreadyHookedException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\ModuleCannotBeHookedException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHookStatus;
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetPossibleHooksForModule;
use PrestaShop\PrestaShop\Core\Domain\Hook\QueryResult\HookStatus;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\Shop\ShopContextInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Form\Admin\Improve\Design\HookModuleType;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Configuration modules positions "Improve > Design > Positions".
 */
class PositionsController extends PrestaShopAdminController
{
    /**
     * @var int
     */
    protected $selectedModule = null;

    /**
     * Display hooks positions.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller')) || is_granted('update', request.get('_legacy_controller')) || is_granted('create', request.get('_legacy_controller')) || is_granted('delete', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function indexAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.legacy.module')]
        Module $moduleAdapter,
        #[Autowire(service: 'prestashop.adapter.legacy.hook')]
        HookInformationProvider $hookProvider,
        LegacyContext $legacyContextService
    ): Response {
        $isSingleShopContext = $this->getShopContext()->getShopConstraint()->isSingleShopContext();
        if (!$isSingleShopContext) {
            return $this->render('@PrestaShop/Admin/Improve/Design/positions.html.twig', [
                'isSingleShopContext' => $isSingleShopContext,
            ]);
        }

        $installedModules = $moduleAdapter->getModulesInstalled();

        $selectedModule = $request->get('show_modules');
        if ($selectedModule && (string) $selectedModule != 'all') {
            $this->selectedModule = (int) $selectedModule;
        }

        $this->manageLegacyFlashes($request->query->get('conf'));

        $modules = [];
        foreach ($installedModules as $installedModule) {
            /** @var LegacyModule|false $module */
            $module = $moduleAdapter->getInstanceById($installedModule['id_module']);
            if ($module) {
                // We want to be able to sort modules by display name
                $modules[(int) $module->id] = $module;
            }
        }

        $hooks = $hookProvider->getHooks();
        foreach ($hooks as $key => $hook) {
            $hooks[$key]['modules'] = $hookProvider->getModulesFromHook(
                $hook['id_hook']
            );
            // No module found, no need to continue
            if (!is_array($hooks[$key]['modules'])) {
                unset($hooks[$key]);

                continue;
            }

            foreach ($hooks[$key]['modules'] as $index => $module) {
                if (empty($modules[(int) $module['id_module']])) {
                    unset($hooks[$key]['modules'][$index]);
                }
            }

            $hooks[$key]['modules_count'] = count($hooks[$key]['modules']);
            // No module remaining after the check, no need to continue
            if ($hooks[$key]['modules_count'] === 0) {
                unset($hooks[$key]);

                continue;
            }

            $hooks[$key]['position'] = $hookProvider->isDisplayHookName($hook['name']);
        }

        $hookModuleV2Enabled = $this->getFeatureFlagStateChecker()->isEnabled(
            FeatureFlagSettings::FEATURE_FLAG_HOOK_MODULE_V2
        );

        if ($hookModuleV2Enabled) {
            $saveUrlParams = [];
            if ($this->selectedModule) {
                $saveUrlParams['show_modules'] = $this->selectedModule;
            }
            $saveUrl = $this->generateUrl('admin_modules_positions_hook_module', $saveUrlParams);
        } else {
            $saveUrlParams = ['addToHook' => ''];
            if ($this->selectedModule) {
                $saveUrlParams['show_modules'] = $this->selectedModule;
            }
            $saveUrl = $legacyContextService->getAdminLink('AdminModulesPositions', true, $saveUrlParams);
        }

        return $this->render('@PrestaShop/Admin/Improve/Design/positions.html.twig', [
            'layoutHeaderToolbarBtn' => [
                'save' => [
                    'class' => 'btn-primary transplant-module-button',
                    'href' => $saveUrl,
                    'desc' => $this->trans('Hook a module', [], 'Admin.Design.Feature'),
                    'icon' => 'anchor',
                ],
            ],
            'selectedModule' => $this->selectedModule,
            'layoutTitle' => $this->trans('Module positions', [], 'Admin.Navigation.Menu'),
            'requireBulkActions' => false,
            'requireFilterStatus' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminModulesPositions'),
            'hooks' => $hooks,
            'modules' => $modules,
            'isSingleShopContext' => $isSingleShopContext,
            'hookModuleV2Enabled' => $hookModuleV2Enabled,
        ]);
    }

    /**
     * Display and process the "Hook a module" form.
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function hookModuleAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.legacy.module')]
        Module $moduleAdapter
    ): Response {
        $moduleChoices = $this->buildModuleChoices($moduleAdapter);

        $submittedHookModule = $request->request->all('hook_module');
        $selectedModuleId = (int) ($submittedHookModule['id_module'] ?? $request->query->get('show_modules') ?? 0);
        $hookChoices = [];
        if ($selectedModuleId > 0) {
            $hookChoices = $this->buildHookChoices($selectedModuleId);
        }

        $form = $this->createForm(HookModuleType::class, null, [
            'module_choices' => $moduleChoices,
            'hook_choices' => $hookChoices,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $exceptions = $this->collectSubmittedExceptions($request, (string) ($data['exceptions'] ?? ''));

            try {
                $this->dispatchCommand(new HookModuleCommand(
                    (int) $data['id_module'],
                    (int) $data['id_hook'],
                    $exceptions
                ));

                $this->addFlash('success', $this->trans(
                    'The module transplanted successfully to the hook.',
                    [],
                    'Admin.Modules.Notification'
                ));

                return $this->redirectToRoute('admin_modules_positions');
            } catch (HookException $e) {
                $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
            }
        }

        return $this->render('@PrestaShop/Admin/Improve/Design/hook_module.html.twig', [
            'hookModuleForm' => $form->createView(),
            'layoutTitle' => $this->trans('Hook a module', [], 'Admin.Design.Feature'),
            'help_link' => $this->generateSidebarLink('AdminModulesPositions'),
            'enableSidebar' => true,
            'exceptionChoices' => $this->buildExceptionChoices(),
            'selectedExceptions' => $this->parseExceptionsString((string) ($form->getData()['exceptions'] ?? '')),
        ]);
    }

    /**
     * Display and process the "Edit hooked module" form.
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function editHookedModuleAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.legacy.module')]
        Module $moduleAdapter,
        HookModuleFormDataProvider $dataProvider
    ): Response {
        $submittedHookModule = $request->request->all('hook_module');
        $moduleId = (int) ($submittedHookModule['id_module'] ?? $request->query->get('id_module') ?? 0);
        $hookId = (int) ($submittedHookModule['id_hook_original'] ?? $request->query->get('id_hook') ?? 0);

        $moduleChoices = $this->buildModuleChoices($moduleAdapter);
        $hookChoices = $moduleId > 0 ? $this->buildHookChoices($moduleId, $hookId) : [];

        $formData = null;
        if ($request->isMethod('GET') && $moduleId > 0 && $hookId > 0) {
            try {
                $formData = $dataProvider->getData($hookId, $moduleId);
            } catch (CannotUpdateHookException $e) {
                $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

                return $this->redirectToRoute('admin_modules_positions');
            }
        }

        $form = $this->createForm(HookModuleType::class, $formData, [
            'module_choices' => $moduleChoices,
            'hook_choices' => $hookChoices,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $exceptions = $this->collectSubmittedExceptions($request, (string) ($data['exceptions'] ?? ''));
            $originalHookId = (int) ($data['id_hook_original'] ?: $hookId);

            try {
                $this->dispatchCommand(new EditHookedModuleCommand(
                    (int) $data['id_module'],
                    $originalHookId,
                    (int) $data['id_hook'],
                    $exceptions
                ));

                $this->addFlash('success', $this->trans(
                    'The module transplanted successfully to the hook.',
                    [],
                    'Admin.Modules.Notification'
                ));

                return $this->redirectToRoute('admin_modules_positions');
            } catch (HookException $e) {
                $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
            }
        }

        return $this->render('@PrestaShop/Admin/Improve/Design/hook_module.html.twig', [
            'hookModuleForm' => $form->createView(),
            'layoutTitle' => $this->trans('Hook a module', [], 'Admin.Design.Feature'),
            'help_link' => $this->generateSidebarLink('AdminModulesPositions'),
            'enableSidebar' => true,
            'exceptionChoices' => $this->buildExceptionChoices(),
            'selectedExceptions' => $this->parseExceptionsString((string) ($form->getData()['exceptions'] ?? '')),
        ]);
    }

    /**
     * AJAX — returns possible hooks for a given module as JSON.
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message: 'Access denied.')]
    public function getPossibleHooksForModuleAction(Request $request): JsonResponse
    {
        $moduleId = (int) $request->request->get('module_id');

        if ($moduleId <= 0) {
            return $this->json(['hasError' => true, 'errors' => 'Invalid module ID.'], 400);
        }

        try {
            $hooks = $this->dispatchQuery(new GetPossibleHooksForModule($moduleId));

            $data = array_map(static fn ($hook) => [
                'id' => $hook->getId(),
                'name' => $hook->getName(),
                'title' => $hook->getTitle(),
                'registered' => $hook->isRegistered(),
            ], $hooks);

            return $this->json(['hasError' => false, 'hooks' => $data]);
        } catch (HookException $e) {
            return $this->json(['hasError' => true, 'errors' => $e->getMessage()], 400);
        }
    }

    /**
     * Unhook module.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller')~'_')", message: 'Access denied.')]
    public function unhookAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.legacy.module')]
        Module $moduleAdapter,
        #[Autowire(service: 'prestashop.adapter.validate')]
        Validate $validateAdapter,
        ShopContextInterface $shopContext
    ): Response {
        $unhooks = $request->request->all('unhooks');
        $context = null;
        if (empty($unhooks)) {
            $moduleId = $request->query->get('moduleId');
            $hookId = $request->query->get('hookId');
            $unhooks = [sprintf('%d_%d', $hookId, $moduleId)];
            $context = $shopContext->getContextShopIds();
        }

        $errors = [];
        foreach ($unhooks as $unhook) {
            $explode = explode('_', $unhook);
            $hookId = (int) isset($explode[0]) ? $explode[0] : 0;
            $moduleId = (int) isset($explode[1]) ? $explode[1] : 0;
            /** @var LegacyModule|false $module */
            $module = $moduleAdapter->getInstanceById($moduleId);
            $hook = new Hook($hookId);

            if (!$module) {
                $errors[] = $this->trans(
                    'This module cannot be loaded.',
                    [],
                    'Admin.Modules.Notification'
                );

                continue;
            }

            if (!$validateAdapter->isLoadedObject($hook)) {
                $errors[] = $this->trans(
                    'Hook cannot be loaded.',
                    [],
                    'Admin.Modules.Notification'
                );

                continue;
            }

            if (!$module->unregisterHook($hookId, $context) || !$module->unregisterExceptions($hookId, $context)) {
                $errors[] = $this->trans(
                    'An error occurred while deleting the module from its hook.',
                    [],
                    'Admin.Modules.Notification'
                );
            }
        }

        if (!empty($errors)) {
            $this->addFlashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans(
                    'The module was successfully removed from the hook.',
                    [],
                    'Admin.Modules.Notification'
                )
            );
        }

        return $this->redirect(
            $this->generateUrl('admin_modules_positions')
        );
    }

    /**
     * Toggle hook status
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')~'_')", message: 'Access denied.')]
    public function toggleStatusAction(Request $request)
    {
        $hookId = (int) $request->request->get('hookId');
        $hookStatus = false;

        try {
            /** @var HookStatus $hookStatus */
            $hookStatus = $this->dispatchQuery(new GetHookStatus($hookId));
            $this->dispatchCommand(new UpdateHookStatusCommand($hookId, !$hookStatus->isActive()));
            $response = [
                'status' => true,
                'message' => $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success'),
            ];
        } catch (HookException $e) {
            $response = [
                'status' => false,
                'message' => $this->getErrorMessageForException($e, $this->getErrorMessages()),
            ];
        }

        $response['hook_status'] = $hookStatus;

        return $this->json($response);
    }

    /**
     * Manage legacy flashes, this code must be removed
     * when legacy edit will be migrated.
     *
     * @param int $messageId The message id from legacy context
     */
    private function manageLegacyFlashes($messageId)
    {
        if (empty($messageId)) {
            return;
        }

        $messages = [
            16 => $this->trans('The module transplanted successfully to the hook.', [], 'Admin.Modules.Notification'),
            17 => $this->trans('The module was successfully removed from the hook.', [], 'Admin.Modules.Notification'),
        ];

        if (isset($messages[$messageId])) {
            $this->addFlash(
                'success',
                $messages[$messageId]
            );
        }
    }

    /**
     * @return array
     */
    private function getErrorMessages(): array
    {
        return [
            HookNotFoundException::class => $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error'),
            HookUpdateHookException::class => $this->trans('An error occurred while updating the status for an object.', [], 'Admin.Notifications.Error'),
            CannotUpdateHookException::class => $this->trans('An error occurred while transplanting the module to its hook.', [], 'Admin.Modules.Notification'),
            ModuleAlreadyHookedException::class => $this->trans('This module has already been transplanted to this hook.', [], 'Admin.Modules.Notification'),
            ModuleCannotBeHookedException::class => $this->trans('This module cannot be transplanted to this hook.', [], 'Admin.Modules.Notification'),
        ];
    }

    /**
     * Builds displayName => id_module choices array for installed modules.
     */
    private function buildModuleChoices(Module $moduleAdapter): array
    {
        $choices = [];
        foreach ($moduleAdapter->getModulesInstalled() as $installedModule) {
            /** @var LegacyModule|false $module */
            $module = $moduleAdapter->getInstanceById($installedModule['id_module']);
            if ($module) {
                $choices[$module->displayName] = (int) $module->id;
            }
        }
        ksort($choices);

        return $choices;
    }

    /**
     * Builds the static list of selectable filenames for the exceptions field:
     * core front-controllers, admin module controllers, and front module controllers.
     */
    private function buildExceptionChoices(): array
    {
        $frontControllers = Dispatcher::getControllersPhpselfList(_PS_FRONT_CONTROLLER_DIR_);
        asort($frontControllers);

        $moduleControllers = static function (string $type): array {
            $entries = [];
            foreach (Dispatcher::getModuleControllers($type) as $module => $controllers) {
                foreach ($controllers as $controller) {
                    $entries[] = sprintf('module-%s-%s', $module, $controller);
                }
            }
            sort($entries);

            return $entries;
        };

        return [
            'core' => array_values($frontControllers),
            'admin_modules' => $moduleControllers('admin'),
            'front_modules' => $moduleControllers('front'),
        ];
    }

    /**
     * Builds title (name) => id_hook choices for a given module via CQRS query.
     * When $forceIncludeHookId is provided, the choice for that hook is appended
     * even if Module::getPossibleHooksList() filtered it out — this prevents the
     * Symfony ChoiceType from silently dropping the currently registered hook
     * when the module no longer implements its callback.
     */
    private function buildHookChoices(int $moduleId, ?int $forceIncludeHookId = null): array
    {
        try {
            $hooks = $this->dispatchQuery(new GetPossibleHooksForModule($moduleId));
        } catch (HookException) {
            $hooks = [];
        }

        $choices = [];
        $foundCurrent = false;
        foreach ($hooks as $hook) {
            $title = $hook->getTitle();
            $label = $title ? $hook->getName() . ' (' . $title . ')' : $hook->getName();
            $choices[$label] = $hook->getId();
            if ($forceIncludeHookId !== null && $hook->getId() === $forceIncludeHookId) {
                $foundCurrent = true;
            }
        }

        if ($forceIncludeHookId !== null && !$foundCurrent && $forceIncludeHookId > 0) {
            $hook = new Hook($forceIncludeHookId);
            if ((int) $hook->id === $forceIncludeHookId) {
                $label = $hook->title ? $hook->name . ' (' . $hook->title . ')' : $hook->name;
                $choices[$label] = $forceIncludeHookId;
            }
        }

        return $choices;
    }

    /**
     * Splits a comma-separated exceptions string into a clean array of filenames.
     *
     * @return string[]
     */
    private function parseExceptionsString(string $exceptions): array
    {
        if ($exceptions === '') {
            return [];
        }

        return array_values(array_unique(array_filter(
            array_map('trim', explode(',', $exceptions))
        )));
    }

    /**
     * Resolves the submitted exception filenames.
     */
    private function collectSubmittedExceptions(Request $request, string $textValue): array
    {
        $fromText = $this->parseExceptionsString($textValue);
        $knownFilenames = $this->getKnownExceptionFilenames();
        $customFromText = array_values(array_filter(
            $fromText,
            static fn (string $filename): bool => !in_array($filename, $knownFilenames, true)
        ));

        $rawHookModule = $request->request->all('hook_module');
        $fromSelect = $rawHookModule['exception_selected'] ?? [];
        if (!is_array($fromSelect)) {
            $fromSelect = [];
        }
        $fromSelect = array_values(array_filter(array_map(
            static fn ($value): string => trim((string) $value),
            $fromSelect
        )));

        return array_values(array_unique(array_merge($fromSelect, $customFromText)));
    }

    /**
     * Flat list of every filename rendered as a real `<option>` in the multi-select
     * helper (core front controllers + module controllers). Used to tell apart "user
     * picked this from the list" from "user typed a custom filename in the text input".
     *
     * @return string[]
     */
    private function getKnownExceptionFilenames(): array
    {
        $choices = $this->buildExceptionChoices();

        return array_merge(
            $choices['core'] ?? [],
            $choices['admin_modules'] ?? [],
            $choices['front_modules'] ?? []
        );
    }
}
