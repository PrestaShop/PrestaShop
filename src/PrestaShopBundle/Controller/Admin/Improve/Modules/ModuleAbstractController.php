<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
