<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Improve\Modules;

use PrestaShop\PrestaShop\Core\Addon\AddonsCollection;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Voter\PageVoter;

abstract class ModuleAbstractController extends FrameworkBundleAdminController
{
    const CONTROLLER_NAME = 'ADMINMODULESSF';

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
            'requireAddonsSearch' => false,
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

        return array_merge($toolbarButtons, $this->getAddonsConnectToolbar());
    }

    /**
     * Create a button in the header for the marketplace account (login or logout).
     *
     * @return array
     */
    protected function getAddonsConnectToolbar()
    {
        $addonsProvider = $this->get('prestashop.core.admin.data_provider.addons_interface');
        if ($addonsProvider->isAddonsAuthenticated()) {
            $addonsEmail = $addonsProvider->getAddonsEmail();

            return [
                'addons_logout' => [
                    'href' => '#',
                    'desc' => $addonsEmail['username_addons'],
                    'icon' => 'exit_to_app',
                    'help' => $this->trans('Synchronized with Addons marketplace!', 'Admin.Modules.Notification'),
                ],
            ];
        }

        return [
            'addons_connect' => [
                'href' => '#',
                'desc' => $this->trans('Connect to Addons marketplace', 'Admin.Modules.Feature'),
                'icon' => 'vpn_key',
                'help' => $this->trans('Connect to Addons marketplace', 'Admin.Modules.Feature'),
            ],
        ];
    }
}
