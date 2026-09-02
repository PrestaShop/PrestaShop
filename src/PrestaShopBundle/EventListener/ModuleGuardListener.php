<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\EventListener;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\IOException;
use PrestaShop\PrestaShop\Core\Security\FolderGuardInterface;
use PrestaShopBundle\Event\ModuleManagementEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Listen install/upgrade events from module manager, and protect the module vendor
 * folder using htaccess file.
 */
class ModuleGuardListener implements EventSubscriberInterface
{
    /**
     * @var FolderGuardInterface
     */
    private $vendorFolderGuard;

    /**
     * @var string
     */
    private $modulesDir;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param FolderGuardInterface $vendorFolderGuard
     * @param string $modulesDir
     * @param LoggerInterface $logger
     */
    public function __construct(
        FolderGuardInterface $vendorFolderGuard,
        $modulesDir,
        LoggerInterface $logger
    ) {
        $this->vendorFolderGuard = $vendorFolderGuard;
        $this->modulesDir = $modulesDir;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ModuleManagementEvent::INSTALL => 'protectModule',
            ModuleManagementEvent::UPGRADE => 'protectModule',
            ModuleManagementEvent::ENABLE => 'protectModule',
        ];
    }

    /**
     * @param ModuleManagementEvent $event
     */
    public function protectModule(ModuleManagementEvent $event)
    {
        $moduleName = $event->getModule()->get('name');
        $moduleVendorPath = $this->modulesDir . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . 'vendor';

        try {
            $this->logger->debug(sprintf('Protect vendor folder in module %s', $moduleName));
            $this->vendorFolderGuard->protectFolder($moduleVendorPath);
        } catch (IOException $e) {
            $this->logger->error(sprintf('%s: %s', $e->getMessage(), $e->getPath()));
        } catch (FileNotFoundException) {
            $this->logger->debug(sprintf('Module %s has no vendor folder', $moduleName));
        }
    }
}
