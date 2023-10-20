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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Module;

use PrestaShop\PrestaShop\Adapter\Cache\Clearer\SymfonyCacheClearer;
use PrestaShopBundle\Event\ModuleManagementEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventSubscriber implements EventSubscriberInterface
{
    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    /**
     * @var SymfonyCacheClearer
     */
    private $cacheClearer;

    public function __construct(ModuleRepository $moduleRepository, SymfonyCacheClearer $cacheClearer)
    {
        $this->moduleRepository = $moduleRepository;
        $this->cacheClearer = $cacheClearer;
    }

    public static function getSubscribedEvents()
    {
        return [
            ModuleManagementEvent::INSTALL => 'onModuleInstalledOrUninstalled',
            ModuleManagementEvent::UNINSTALL => 'onModuleInstalledOrUninstalled',
            ModuleManagementEvent::UPGRADE => 'onModuleInstalledOrUninstalled',
            ModuleManagementEvent::ENABLE => 'onModuleStateChanged',
            ModuleManagementEvent::DISABLE => 'onModuleStateChanged',
            ModuleManagementEvent::ENABLE_MOBILE => 'onModuleStateChanged',
            ModuleManagementEvent::DISABLE_MOBILE => 'onModuleStateChanged',
        ];
    }

    public function onModuleStateChanged(ModuleManagementEvent $event): void
    {
        $moduleName = $event->getModule()->get('name');
        $this->moduleRepository->clearCache($moduleName, true);
    }

    public function onModuleInstalledOrUninstalled(ModuleManagementEvent $event): void
    {
        $this->onModuleStateChanged($event);
        $this->cacheClearer->clear();
    }
}
