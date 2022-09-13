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

use PrestaShop\PrestaShop\Core\Module\ModuleCollection;
use PrestaShop\PrestaShop\Core\Module\ModuleRepositoryInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Voter\PageVoter;

abstract class ModuleAbstractController extends FrameworkBundleAdminController
{
    public const CONTROLLER_NAME = 'ADMINMODULESSF';

    public const CONFIGURABLE_MODULE_TYPE = 'to_configure';
    public const UPDATABLE_MODULE_TYPE = 'to_update';
    public const TOTAL_MODULE_TYPE = 'count';

    final protected function getNotificationPageData(ModuleCollection $moduleCollection): array
    {
        $modulePresenter = $this->get('prestashop.adapter.presenter.module');
        $moduleRepository = $this->getModuleRepository();

        $moduleRepository->setActionUrls($moduleCollection);

        return [
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'layoutTitle' => $this->trans('Module notifications', 'Admin.Modules.Feature'),
            'help_link' => $this->generateSidebarLink('AdminModules'),
            'modules' => $modulePresenter->presentCollection($moduleCollection),
            'requireBulkActions' => false,
            'requireFilterStatus' => false,
            'level' => $this->authorizationLevel($this::CONTROLLER_NAME),
            'errorMessage' => $this->trans('You do not have permission to add this.', 'Admin.Notifications.Error'),
        ];
    }

    protected function getModuleRepository(): ModuleRepositoryInterface
    {
        return $this->get('prestashop.core.admin.module.repository');
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

        return $toolbarButtons;
    }
}
