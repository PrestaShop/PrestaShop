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

namespace PrestaShopBundle\Controller\Admin\Improve\Design;

use Hook;
use PrestaShop\PrestaShop\Core\Domain\Hook\Command\UpdateHookStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\HookException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\HookNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\HookUpdateHookException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHookStatus;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Configuration modules positions "Improve > Design > Positions".
 */
class PositionsController extends FrameworkBundleAdminController
{
    /**
     * @var int
     */
    protected $selectedModule = null;

    /**
     * Display hooks positions.
     *
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller')) || is_granted('update', request.get('_legacy_controller')) || is_granted('create', request.get('_legacy_controller')) || is_granted('delete', request.get('_legacy_controller'))",
     *     message="Access denied.")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $isSingleShopContext = $this->get('prestashop.adapter.shop.context')->isSingleShopContext();
        if (!$isSingleShopContext) {
            return $this->render('@PrestaShop/Admin/Improve/Design/positions.html.twig', [
                'isSingleShopContext' => $isSingleShopContext,
            ]);
        }

        $moduleAdapter = $this->get('prestashop.adapter.legacy.module');
        $hookProvider = $this->get('prestashop.adapter.legacy.hook');
        $installedModules = $moduleAdapter->getModulesInstalled();

        $selectedModule = $request->get('show_modules');
        if ($selectedModule && (string) $selectedModule != 'all') {
            $this->selectedModule = (int) $selectedModule;
        }

        $this->manageLegacyFlashes($request->query->get('conf'));

        $modules = [];
        foreach ($installedModules as $installedModule) {
            if ($module = $moduleAdapter->getInstanceById($installedModule['id_module'])) {
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

        $legacyContextService = $this->get('prestashop.adapter.legacy.context');
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
                    'desc' => $this->trans('Hook a module', 'Admin.Design.Feature'),
                    'icon' => 'anchor',
                ],
            ],
            'selectedModule' => $this->selectedModule,
            'layoutTitle' => $this->trans('Module positions', 'Admin.Navigation.Menu'),
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
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller')~'_')", message="Access denied.")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function unhookAction(Request $request)
    {
        $validateAdapter = $this->get('prestashop.adapter.validate');
        $unhooks = $request->request->get('unhooks');
        $context = null;
        if (empty($unhooks)) {
            $moduleId = $request->query->get('moduleId');
            $hookId = $request->query->get('hookId');
            $unhooks = [sprintf('%d_%d', $hookId, $moduleId)];
            $context = $this->get('prestashop.adapter.shop.context')->getContextListShopID();
        }

        $errors = [];
        foreach ($unhooks as $unhook) {
            $explode = explode('_', $unhook);
            $hookId = (int) isset($explode[0]) ? $explode[0] : 0;
            $moduleId = (int) isset($explode[1]) ? $explode[1] : 0;
            $module = $this->get('prestashop.adapter.legacy.module')->getInstanceById($moduleId);
            $hook = new Hook($hookId);

            if (!$module) {
                $errors[] = $this->trans(
                    'This module cannot be loaded.',
                    'Admin.Modules.Notification'
                );

                continue;
            }

            if (!$validateAdapter->isLoadedObject($hook)) {
                $errors[] = $this->trans(
                    'Hook cannot be loaded.',
                    'Admin.Modules.Notification'
                );

                continue;
            }

            if (!$module->unregisterHook($hookId, $context) || !$module->unregisterExceptions($hookId, $context)) {
                $errors[] = $this->trans(
                    'An error occurred while deleting the module from its hook.',
                    'Admin.Modules.Notification'
                );
            }
        }

        if (!empty($errors)) {
            $this->flashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans(
                    'The module was successfully removed from the hook.',
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
            16 => $this->trans('The module transplanted successfully to the hook.', 'Admin.Modules.Notification'),
            17 => $this->trans('The module was successfully removed from the hook.', 'Admin.Modules.Notification'),
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
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller')~'_')", message="Access denied.")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function toggleStatusAction(Request $request)
    {
        $hookId = (int) $request->request->get('hookId');
        $hookStatus = false;

        try {
            $hookStatus = !$this->getQueryBus()->handle(new GetHookStatus($hookId));
            $this->getCommandBus()->handle(new UpdateHookStatusCommand($hookId, (bool) $hookStatus));
            $response = [
                'status' => true,
                'message' => $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success'),
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
            HookNotFoundException::class => $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error'),
            HookUpdateHookException::class => $this->trans('An error occurred while updating the status for an object.', 'Admin.Notifications.Error'),
        ];
    }
}
