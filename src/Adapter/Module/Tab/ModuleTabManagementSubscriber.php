<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Module\Tab;

use PrestaShopBundle\Event\ModuleManagementEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * This class subscribes to the events module installation / uninstallation
 * in order to install or remove its tabs as well
 */
class ModuleTabManagementSubscriber implements EventSubscriberInterface
{
    /**
     * @var ModuleTabRegister
     */
    private $moduleTabRegister;
    /**
     * @var ModuleTabDeregister
     */
    private $moduleTabUnregister;
    
    public function __construct(ModuleTabRegister $moduleTabRegister, ModuleTabUnregister $moduleTabUnregister)
    {
        $this->moduleTabRegister = $moduleTabRegister;
        $this->moduleTabUnregister = $moduleTabUnregister;
    }
    
    public static function getSubscribedEvents()
    {
        return [
            ModuleManagementEvent::INSTALL => 'onModuleInstall',
            ModuleManagementEvent::UNINSTALL => 'onModuleUninstall',
            
            ModuleManagementEvent::RESET => 'onModuleReset',
        ];
    }

    public function onModuleInstall(ModuleManagementEvent $event)
    {
        $this->moduleTabRegister->registerTabs($event->getModule());
    }
    
    public function onModuleReset(ModuleManagementEvent $event)
    {
        $this->onModuleUninstall($event);
        $this->onModuleInstall($event);
    }
    
    public function onModuleUninstall(ModuleManagementEvent $event)
    {
        $this->moduleTabUnregister->unregisterTabs($event->getModule());
    }
}
