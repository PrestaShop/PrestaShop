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
use PrestaShop\PrestaShop\Core\Foundation\Log\MessageStackManager;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;

/**
 * Used to build the Container at the process starting (bootstrap.php)
 *
 * At instantiation, the following aliases are created:
 * - CoreFoundation
 * - CoreBusiness
 * - CoreAdapter
 * These aliases can be used like this (to avoid long namespace in the parameter):
 * Instead of $container->make('PrestaShop\\PrestaShop\\Core\\Business\\Context'), use $container->make('CoreBusiness:Context')
 *
 * Final service instances are generated and can be called from make() (but not auto-injected in other services):
 * - final:EventDispatcher/routing
 * - final:EventDispatcher/log
 * - final:EventDispatcher/message
 * - final:EventDispatcher/module
 * - final:EventDispatcher/hook
 *
 * During init process of the new Router system, some services are mapped to specific shortcuts:
 * - Context = CoreBusiness:Context = PrestaShop\\PrestaShop\\Core\\Business\\Context
 * - Translator = TranslatorInterface = CoreAdapter:Translator = PrestaShop\\PrestaShop\\Adapter\\Translator
 * - Routing = CoreFoundation:RoutingService = PrestaShop\\PrestaShop\\Core\\Foundation\\Routing\\RoutingService
 * - MessageStack = CoreFoundation:MessageStackManager = PrestaShop\\PrestaShop\\Core\\Foundation\\Log\\MessageStackManager
 *
 */
class Core_Business_ContainerBuilder
{
    /**
     * Construct PrestaShop Core Service container.
     *
     * @return Container
     * @throws PrestaShop\PrestaShop\Core\Foundation\IoC\Exception
     */
    public function build()
    {
        $container = new Container();

        $container->aliasNamespace('CoreBusiness', 'PrestaShop\\PrestaShop\\Core\\Business');
        $container->aliasNamespace('CoreFoundation', 'PrestaShop\\PrestaShop\\Core\\Foundation');
        $container->aliasNamespace('CoreAdapter', 'PrestaShop\\PrestaShop\\Adapter');

        $container->bind('Core_Business_ConfigurationInterface', 'Adapter_Configuration', true);
        $container->bind('Core_Foundation_Database_DatabaseInterface', 'Adapter_Database', true);
        
        $messageStackManager = new MessageStackManager();
        $container->bind('PrestaShop\\PrestaShop\\Core\\Foundation\\Log\\MessageStackManager', $messageStackManager, true);
        $container->bind('MessageStack', $messageStackManager, true);

        return $container;
    }
    
    public function buildForTesting()
    {
        $container = new Container();

        $container->aliasNamespace('CoreBusiness', 'PrestaShop\\PrestaShop\\Core\\Business');
        $container->aliasNamespace('CoreFoundation', 'PrestaShop\\PrestaShop\\Core\\Foundation');
        $container->aliasNamespace('CoreAdapter', 'PrestaShop\\PrestaShop\\Adapter');

        // No ConfigurationInterface, neither DB (mocked)

        $messageStackManager = new MessageStackManager();
        $container->bind('PrestaShop\\PrestaShop\\Core\\Foundation\\Log\\MessageStackManager', $messageStackManager, true);
        $container->bind('MessageStack', $messageStackManager, true);

        return $container;
    }
}
