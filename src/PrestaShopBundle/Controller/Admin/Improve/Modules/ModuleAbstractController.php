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

use PrestaShop\PrestaShop\Adapter\Presenter\Module\ModulePresenter;
use PrestaShop\PrestaShop\Core\Module\ModuleCollection;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use PrestaShop\PrestaShop\Core\Module\ModuleRepositoryInterface;
use PrestaShop\PrestaShop\Core\Security\Permission;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;

abstract class ModuleAbstractController extends PrestaShopAdminController
{
    public const CONTROLLER_NAME = 'ADMINMODULESSF';

    public const CONFIGURABLE_MODULE_TYPE = 'to_configure';
    public const UPDATABLE_MODULE_TYPE = 'to_update';
    public const TOTAL_MODULE_TYPE = 'count';

    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
            ModuleRepository::class => ModuleRepository::class,
            ModulePresenter::class => ModulePresenter::class,
        ];
    }

    protected function getNotificationPageData(ModuleCollection $moduleCollection): array
    {
        $this->getModuleRepository()->setActionUrls($moduleCollection);

        return [
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'layoutTitle' => $this->trans('Module notifications', [], 'Admin.Navigation.Menu'),
            'help_link' => $this->generateSidebarLink('AdminModules'),
            'modules' => $this->getModulePresenter()->presentCollection($moduleCollection),
            'requireBulkActions' => false,
            'requireFilterStatus' => false,
            'level' => $this->getAuthorizationLevel($this::CONTROLLER_NAME),
            'errorMessage' => $this->trans('You do not have permission to add this.', [], 'Admin.Notifications.Error'),
        ];
    }

    protected function getModulePresenter(): ModulePresenter
    {
        return $this->container->get(ModulePresenter::class);
    }

    protected function getModuleRepository(): ModuleRepositoryInterface
    {
        return $this->container->get(ModuleRepository::class);
    }

    /**
     * Common method for all module related controller for getting the header buttons.
     *
     * @return array
     */
    protected function getToolbarButtons(): array
    {
        // toolbarButtons
        $toolbarButtons = [];

        if ($this->isGranted(Permission::CREATE, self::CONTROLLER_NAME) || $this->isGranted(Permission::DELETE, self::CONTROLLER_NAME)) {
            $toolbarButtons['add_module'] = [
                'href' => '#',
                'desc' => $this->trans('Upload a module', [], 'Admin.Modules.Feature'),
                'icon' => 'cloud_upload',
                'help' => $this->trans('Upload a module', [], 'Admin.Modules.Feature'),
            ];
        }

        return $toolbarButtons;
    }
}
