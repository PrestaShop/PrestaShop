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
namespace PrestaShop\PrestaShop\Core\Business\Dispatcher;

use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\EventDispatcher;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;

/**
 * This class extends EventDispatcher to add Business related listeners.
 *
 * @see \PrestaShop\PrestaShop\Core\Foundation\Dispatcher\EventDispatcher
 *
 * Existing dispatchers:
 * The default ones: @see \PrestaShop\PrestaShop\Core\Foundation\Dispatcher\EventDispatcher
 * New ones are defined:
 *
 * - module         All events concerning modules manipulation: install, update, uninstall, etc...
 *                  FOR NOW, THESE EVENTS ARE NOT TRIGGERED. FOR FUTURE BEHAVIOR.
 *      - before_install
 *      - after_install
 *      - before_update
 *      - after_update
 *      - before_uninstall
 *      - after_uninstall
 *      - before_deactivate
 *      - after_deactivate
 *      - before_reactivate
 *      - after_reactivate
 */
final class BaseEventDispatcher extends EventDispatcher
{
    /**
     * PrestaShop Business specific listeners.
     *
     * This static attribute contains PrestaShop Business specific listeners to register during Router instantiation.
     * The array will be merged into the super class array to complete the list.
     * @var array
     */
    private static $baseDispatcherRegistry = array(
        'module' => array(
            array('before_install', 'PrestaShop\\PrestaShop\\Core\\Business\\Log\\ModuleEventListener', 'onBefore', -255, false, 'getInstance'),
            array('before_update', 'PrestaShop\\PrestaShop\\Core\\Business\\Log\\ModuleEventListener', 'onBefore', -255, false, 'getInstance'),
            array('before_uninstall', 'PrestaShop\\PrestaShop\\Core\\Business\\Log\\ModuleEventListener', 'onBefore', -255, false, 'getInstance'),
            array('before_deactivate', 'PrestaShop\\PrestaShop\\Core\\Business\\Log\\ModuleEventListener', 'onBefore', -255, false, 'getInstance'),
            array('before_reactivate', 'PrestaShop\\PrestaShop\\Core\\Business\\Log\\ModuleEventListener', 'onBefore', -255, false, 'getInstance'),
            array('after_install', 'PrestaShop\\PrestaShop\\Core\\Business\\Log\\ModuleEventListener', 'onAfter', 128, false, 'getInstance'),
            array('after_update', 'PrestaShop\\PrestaShop\\Core\\Business\\Log\\ModuleEventListener', 'onAfter', 128, false, 'getInstance'),
            array('after_uninstall', 'PrestaShop\\PrestaShop\\Core\\Business\\Log\\ModuleEventListener', 'onAfter', 128, false, 'getInstance'),
            array('after_deactivate', 'PrestaShop\\PrestaShop\\Core\\Business\\Log\\ModuleEventListener', 'onAfter', 128, false, 'getInstance'),
            array('after_reactivate', 'PrestaShop\\PrestaShop\\Core\\Business\\Log\\ModuleEventListener', 'onAfter', 128, false, 'getInstance'),
        ), // all events concerning modules manipulation: install, update, uninstall, etc...
        'hook' => array(
            array('legacy_actionProductAdd', 'Adapter_HookManager', 'onHook', 0, true),
            array('legacy_actionProductUpdate', 'Adapter_HookManager', 'onHook', 0, true),
            array('legacy_actionCategoryUpdate', 'Adapter_HookManager', 'onHook', 0, true),
            // TODO: complete this list for Admin Product page
        ) // hooks
    );

    /**
     * This method is called at the Router instantiation to initialize the base event listeners.
     *
     * @param \Core_Foundation_IoC_Container $container The application service container.
     * @param boolean $forceDebug True for debug mode.
     */
    final public static function initBaseDispatchers(&$container, $forceDebug = false)
    {
        // complete registry with Business listeners, and then init
        EventDispatcher::$dispatcherRegistry = array_merge(EventDispatcher::$dispatcherRegistry, self::$baseDispatcherRegistry);
        $configuration = $container->make('Core_Business_ConfigurationInterface');
        self::initDispatchers(
            $container,
            $configuration->get('_PS_ROOT_DIR_'),
            $configuration->get('_PS_CACHE_DIR_'),
            $configuration->get('_PS_MODULE_DIR_'),
            ($forceDebug || $configuration->get('_PS_MODE_DEV_')));
    }

    /**
     * Call this instead of ->dispatch() to trigger a hook event (a more structured event than the base one).
     *
     * This case will use a HookEvent instead of a BaseEvent (means a subclass of BaseEvent with more options),
     * to allow Hook parameters and Hook results to pass through the event object, and to return the result.
     *
     * @param string $hookName
     * @param array $hookParameters An indexed array of parameters to send to the Hook listener.
     * @return string|array The result of the hook(s) if there is any.
     */
    final public static function hook($hookName, $hookParameters = array())
    {
        $dispatcher = self::$instances['hook'];
        $event = new HookEvent();
        $event->setHookParameters($hookParameters);
        $dispatcher->dispatch($hookName, $event);
        return $event->getHookResult();
    }
}
