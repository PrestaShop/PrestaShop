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

namespace PrestaShopBundle\Controller\Admin\Improve\Modules;

use PrestaShop\PrestaShop\Core\Addon\AddonsCollection;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Voter\PageVoter;
use PrestaShopBundle\Service\Hook\HookFinder;

abstract class ModuleAbstractController extends FrameworkBundleAdminController
{
    public const CONTROLLER_NAME = 'ADMINMODULESSF';
    public const MANDATORY_TOOLBAR_BUTTON_KEYS = ['href', 'desc', 'icon', 'help'];

    /**
     * Common method of alerts & updates routes for getting template variables.
     *
     * @param string $type Type of alert to display (to_configure / to_update ...)
     *
     * @return array
     */
    protected function getNotificationPageData($type)
    {
        $modulePresenter = $this->get('prestashop.adapter.presenter.module');
        $modulesPresenterCallback = function (AddonsCollection &$modules) use ($modulePresenter) {
            return $modulePresenter->presentCollection($modules);
        };

        $moduleManager = $this->get('prestashop.module.manager');
        $modules = $moduleManager->getModulesWithNotifications($modulesPresenterCallback);

        return [
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'layoutTitle' => $this->trans('Module notifications', 'Admin.Modules.Feature'),
            'help_link' => $this->generateSidebarLink('AdminModules'),
            'modules' => $modules->{$type},
            'requireBulkActions' => false,
            'requireFilterStatus' => false,
            'level' => $this->authorizationLevel($this::CONTROLLER_NAME),
            'errorMessage' => $this->trans('You do not have permission to add this.', 'Admin.Notifications.Error'),
        ];
    }

    /**
     * Common method for all module related controller for getting the header buttons.
     *
     * @return array
     */
    protected function getToolbarButtons()
    {
        // toolbarButtons
        $toolbarButtons = [];

        if (!in_array(
            $this->authorizationLevel($this::CONTROLLER_NAME),
            [
                PageVoter::LEVEL_READ,
                PageVoter::LEVEL_UPDATE,
            ]
        )) {
            $toolbarButtons['add_module'] = [
                'href' => '#',
                'desc' => $this->trans('Upload a module', 'Admin.Modules.Feature'),
                'icon' => 'cloud_upload',
                'help' => $this->trans('Upload a module', 'Admin.Modules.Feature'),
            ];
        }

        return array_merge($toolbarButtons, $this->getExtraToolbarButtons());
    }

    /**
     * This method will call the actionAdminModuleExtraToolbarButton hook and allow
     * modules to add some extra buttons to the modules catalogue toolbar.
     * All the button's specification keys ('href', 'desc', 'icon', 'help') are mandatory
     */
    private function getExtraToolbarButtons(): array
    {
        try {
            $extraToolbarContentFromHooks = (new HookFinder())
                ->setHookName('actionAdminModuleExtraToolbarButton')
                ->setParams(['controller' => $this])
                ->find();
        } catch (CoreException $exception) {
            return [];
        }

        $extraToolbarButtons = [];

        // Validation. We check that we have the exact keys
        foreach ($extraToolbarContentFromHooks as $moduleName => $extraToolbarContentFromHook) {
            if (!is_array($extraToolbarContentFromHook)) {
                continue;
            }

            foreach ($extraToolbarContentFromHook as $buttonIndex => $extraToolbarButton) {
                if (!empty(array_diff(static::MANDATORY_TOOLBAR_BUTTON_KEYS, array_keys($extraToolbarButton)))) {
                    return [];
                } else {
                    $extraToolbarButtons[$buttonIndex] = $extraToolbarButton;
                }
            }
        }

        return $extraToolbarButtons;
    }
}
