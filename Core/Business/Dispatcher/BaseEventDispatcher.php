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

use Symfony\Component\Config\ConfigCacheFactory;
use Symfony\Component\Config\ConfigCacheInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\EventDispatcher;

/**
 * Existing dispatchers:
 *
 * - routing        All events triggered during Routing (before action call, and after action result),
 *                  and during shutdown, subcall, redirect, forward...
 *      - cache_generation      A cache file has been (re)generated from the Router.
 *      - dispatch_succeed
 *      - dispatch_failed
 *      - subcall_succeed
 *      - subcall_failed
 *      - redirection_sent      The redirection has been sent into the HTTP Location header. Cannot guarantee that it will succeed.
 *      - redirection_failed    The redirection failed due to a bad parameter given or headers already sent.
 *      - forward_succeed
 *      - forward_failed
 *
 * - log            All events triggered when a log is dumped into the logger.
 *                  WARNING: this could become very slow if you listen to many log event!
 *
 * - message        All events triggered when a PHP code wants to post a message to the user (often to Admin interface)
 *                  (warnings, notices, messages to flash on the screen, etc...).
 *      - error_message         When an error must be displayed (in RED?). Used by: ErrorException
 *      - warning_message       When an warning must be displayed (in ORANGE?). Used by: WarningException
 *      - info_message          When a notice must be displayed (in BLUE/themed?)
 *      - success_message       When a success must be displayed after an action (in GREEN/themed?)
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
class BaseEventDispatcher extends EventDispatcher
{
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
        ) // all events concerning modules manipulation: install, update, uninstall, etc...
    );

    final public static function initBaseDispatchers($forceDebug = false)
    {
        EventDispatcher::$dispatcherRegistry = array_merge(EventDispatcher::$dispatcherRegistry, self::$baseDispatcherRegistry);
        EventDispatcher::initDispatchers($forceDebug);
    }
}
