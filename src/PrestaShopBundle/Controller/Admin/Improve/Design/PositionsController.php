<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Improve\Design;

use Hook;
use Module as LegacyModule;
use PrestaShop\PrestaShop\Adapter\Hook\HookInformationProvider;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\Domain\Hook\Command\UpdateHookStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\HookException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\HookNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\HookUpdateHookException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHookStatus;
use PrestaShop\PrestaShop\Core\Domain\Hook\QueryResult\HookStatus;
use PrestaShop\PrestaShop\Core\Shop\ShopContextInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
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

        $saveUrlParams = [
            'addToHook' => '',
        ];
        if ($this->selectedModule) {
            $saveUrlParams['show_modules'] = $this->selectedModule;
        }
        $saveUrl = $legacyContextService->getAdminLink('AdminModulesPositions', true, $saveUrlParams);

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
        ]);
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
     * @return array
     */
    private function getErrorMessages(): array
    {
        return [
            HookNotFoundException::class => $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error'),
            HookUpdateHookException::class => $this->trans('An error occurred while updating the status for an object.', [], 'Admin.Notifications.Error'),
        ];
    }
}
