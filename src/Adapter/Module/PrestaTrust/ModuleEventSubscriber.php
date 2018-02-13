<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Module\PrestaTrust;

use PrestaShopBundle\Event\ModuleZipManagementEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * This class subscribes to the events module installation / uninstallation
 * in order to install or remove its tabs as well
 */
class ModuleEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var PrestaTrustChecker
     */
    private $checker;

    /**
     * These events can be enabled/disabled via the config file
     * @var boolean
     */
    public $enabled;

    public function __construct(PrestaTrustChecker $checker)
    {
        $this->checker = $checker;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            ModuleZipManagementEvent::DOWNLOAD => 'onNewModule',
        );
    }

    /**
     * Event executed on module download (coming from the marketplace or the employee disk)
     * If the feature is enabled in the project configuration, we will trigger our class PrestaTrustChecker to verify
     * if the module is compliant.
     *
     * @param ModuleZipManagementEvent $event
     * @return void
     */
    public function onNewModule(ModuleZipManagementEvent $event)
    {
        if (!$this->enabled) {
            return;
        }
        
        $this->checker->checkModuleZip($event->getModuleZip());
    }
}
