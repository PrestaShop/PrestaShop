<?php
/**
 * 2007-2015 PrestaShop
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Business\Module;

use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;

/**
 * This is a event listener for dispatcher 'module', to manage cache cleaning.
 *
 * FIXME: for now, this listener does nothing, since modules cannot complete/modify routes.
 * Please come back later when modules will have acces to an API to add routes!
 * This listener is for now an example of what we can do in the Core. For modules, the API is not ready yet.
 */
class ModuleEventListener
{
    private $container;

    /**
     * Constructor
     *
     * @param \Core_Foundation_IoC_Container $container Injected by services Container
     */
    public function __construct(\Core_Foundation_IoC_Container $container)
    {
        $this->container = $container;
    }

    /**
     * Triggered before a module modification, to ensure the field is clean.
     *
     * TODO
     *
     * @param BaseEvent $event
     */
    public function onBefore(BaseEvent $event)
    {
        // TODO: clear routing/dispatchers caches, or maybe more!
    }

    /**
     * Triggered after a module modification (update, install, uninstall, etc...) to clean Routing cache
     *
     * TODO
     *
     * @param BaseEvent $event
     */
    public function onAfter(BaseEvent $event)
    {
        $cacheManager = $this->container->make('Adapter_CacheManager');
        //$cacheManager->clean();
        // TODO: clear routing/dispatchers caches, or maybe more!
    }
}
