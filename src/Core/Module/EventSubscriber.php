<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
            ModuleManagementEvent::PRE_ACTION => 'onModuleStateChanged',
            ModuleManagementEvent::INSTALL => 'onModuleStateChanged',
            ModuleManagementEvent::POST_INSTALL => 'onModuleStateChanged',
            ModuleManagementEvent::UNINSTALL => 'onModuleStateChanged',
            ModuleManagementEvent::UPGRADE => 'onModuleStateChanged',
            ModuleManagementEvent::UPLOAD => 'onModuleStateChanged',
            ModuleManagementEvent::ENABLE => 'onModuleStateChanged',
            ModuleManagementEvent::DISABLE => 'onModuleStateChanged',
            ModuleManagementEvent::DELETE => 'onModuleStateChanged',
        ];
    }

    public function onModuleStateChanged(ModuleManagementEvent $event): void
    {
        // Force clearing all cache because it's so badly handled
        $this->moduleRepository->clearCache();
        $this->cacheClearer->clear();
    }
}
