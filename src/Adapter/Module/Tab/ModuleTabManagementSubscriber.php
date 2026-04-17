<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Module\Tab;

use PrestaShopBundle\Event\ModuleManagementEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * This class subscribes to the events module installation / uninstallation
 * in order to install or remove its tabs as well.
 */
class ModuleTabManagementSubscriber implements EventSubscriberInterface
{
    /**
     * @var ModuleTabRegister
     */
    private $moduleTabRegister;
    /**
     * @var ModuleTabUnregister
     */
    private $moduleTabUnregister;

    public function __construct(ModuleTabRegister $moduleTabRegister, ModuleTabUnregister $moduleTabUnregister)
    {
        $this->moduleTabRegister = $moduleTabRegister;
        $this->moduleTabUnregister = $moduleTabUnregister;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ModuleManagementEvent::INSTALL => 'onModuleInstall',
            ModuleManagementEvent::UNINSTALL => 'onModuleUninstall',
            ModuleManagementEvent::ENABLE => 'onModuleEnable',
            ModuleManagementEvent::DISABLE => 'onModuleDisable',
        ];
    }

    /**
     * @param ModuleManagementEvent $event
     */
    public function onModuleInstall(ModuleManagementEvent $event)
    {
        $this->moduleTabRegister->registerTabs($event->getModule());
    }

    /**
     * @param ModuleManagementEvent $event
     */
    public function onModuleUninstall(ModuleManagementEvent $event)
    {
        $this->moduleTabUnregister->unregisterTabs($event->getModule());
    }

    /**
     * @param ModuleManagementEvent $event
     */
    public function onModuleEnable(ModuleManagementEvent $event)
    {
        $this->moduleTabRegister->enableTabs($event->getModule());
    }

    /**
     * @param ModuleManagementEvent $event
     */
    public function onModuleDisable(ModuleManagementEvent $event)
    {
        $this->moduleTabUnregister->disableTabs($event->getModule());
    }
}
