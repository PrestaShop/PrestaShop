<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\EventListener;

use PrestaShop\PrestaShop\Core\Exception\IOException;
use PrestaShop\PrestaShop\Core\Security\FolderProtectorInterface;
use PrestaShopBundle\Event\ModuleManagementEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Listens to install/upgrade events from module manager, and protects the module vendor
 * folder using htaccess file.
 */
class ModuleProtectorListener implements EventSubscriberInterface
{
    /**
     * @var FolderProtectorInterface
     */
    private $vendorFolderProtector;

    /**
     * @var string
     */
    private $modulesDir;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param FolderProtectorInterface $vendorFolderProtector
     * @param string $modulesDir
     * @param LoggerInterface $logger
     */
    public function __construct(
        FolderProtectorInterface $vendorFolderProtector,
        $modulesDir,
        LoggerInterface $logger
    ) {
        $this->vendorFolderProtector = $vendorFolderProtector;
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
        if (!file_exists($moduleVendorPath)) {
            $this->logger->info(sprintf('Module %s has no vendor folder', $moduleName));

            return;
        }

        try {
            $this->logger->info(sprintf('Protect vendor folder in module %s', $moduleName));
            $this->vendorFolderProtector->protectFolder($moduleVendorPath);
        } catch (IOException $e) {
            $this->logger->error(sprintf('%s: %s', $e->getMessage(), $e->getPath()));
        }
    }
}
