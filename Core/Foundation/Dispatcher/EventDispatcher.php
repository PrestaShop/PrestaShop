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
namespace PrestaShop\PrestaShop\Core\Foundation\Dispatcher;

use Symfony\Component\Config\ConfigCacheFactory;
use Symfony\Component\Config\ConfigCacheInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;
use PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException;
use PrestaShop\PrestaShop\Core\Foundation\Exception\ErrorException;

/**
 * TODO : explain YML structure needed to add a listener.
 *
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
 * - error
 *      - warning_message       Used by: WarningException
 *      - error_message         Used by: ErrorException and DevelopmentErrorException
 */
class EventDispatcher extends \Symfony\Component\EventDispatcher\EventDispatcher
{
    private static $instances = array();

    /**
     * Retrieve a singleton (default, or an indexed one) of EventDispatcher.
     * If the dispatcher is not instantiated by initialization, then it will be instantiated.
     *
     * @param string $dispatcherName
     * @return Ambigous <multitype:, \PrestaShop\PrestaShop\Core\Foundation\Dispatcher\EventDispatcher>
     */
    final public static function getInstance($dispatcherName = 'default')
    {
        if (!isset(self::$instances[$dispatcherName])) {
            self::$instances[$dispatcherName] = new self($dispatcherName);
        }
        return self::$instances[$dispatcherName];
    }

    /**
     * This registry contains base listeners to init (lazy mode or not).
     * This static registry can be completed by a subclass before calling initDispatchers().
     *
     * @var array
     */
    protected static $dispatcherRegistry = array(
        'routing' => array(
        ), // all events triggered during Routing (before action call, and after action result), and during shutdown
        'log' => array(
        ), // all events triggered when a log is dumped into the logger. WARNING: this could become slow if you listen to each log event!
        'message' => array(
            array('error_message', 'PrestaShop\\PrestaShop\\Core\\Foundation\\Log\\MessageStackManager', 'onError', -127, false, 'getInstance'),
            array('warning_message', 'PrestaShop\\PrestaShop\\Core\\Foundation\\Log\\MessageStackManager', 'onWarning', -127, false, 'getInstance'),
            array('info_message', 'PrestaShop\\PrestaShop\\Core\\Foundation\\Log\\MessageStackManager', 'onInfo', -127, false, 'getInstance'),
            array('success_message', 'PrestaShop\\PrestaShop\\Core\\Foundation\\Log\\MessageStackManager', 'onSuccess', -127, false, 'getInstance'),
        ), // all events triggered when a PHP code wants to post a message (warnings, notices, messages to flash on the screen, etc...)
    );

    /**
     * Scan configuration files (Core and modules directories) to register listeners to event dispatchers that
     * are instanciated as singletons at the same time. Each singleton is indexed by its name.
     * Then use static registry $dispatcherRegistry to init all the registered listeners to event dispatchers.
     *
     * @param string $rootDir The root directory path.
     * @param string $cacheDir The cache directory path.
     * @param string $moduleDir The module directory path to scan configuration files.
     * @param boolean $debug True to force debug mode (cache fil is generated each time).
     * @throws DevelopmentErrorException If a configuration file is malformed.
     */
    final public static function initDispatchers($rootDir, $cacheDir, $moduleDir, $debug = false)
    {
        $cache = (new ConfigCacheFactory($debug))->cache(
            $cacheDir.'dispatcher/init_subscribers.php',
            function (ConfigCacheInterface $cache) use ($rootDir, $cacheDir, $moduleDir) {
                $moduleCoreConfigExists = (count(glob($moduleDir.'*/CoreConfig/')) > 0);

                $settingsFilesFinder = Finder::create()->files()->name('settings.yml')->sortByName()->followLinks()
                    ->in($rootDir.'/CoreConfig/');
                if ($moduleCoreConfigExists) {
                    $settingsFilesFinder->in($moduleDir.'*/CoreConfig/');
                }

                $phpCode = '<'.'?php
';
                foreach ($settingsFilesFinder as $file) {
                    try {
                        $settings = Yaml::parse(file_get_contents($file->getRealpath()));
                        if (isset($settings['dispatchers']) && is_array($dispatchers = $settings['dispatchers'])) {
                            foreach ($dispatchers as $dispatcherName => $dispatchersEvents) {
                                foreach ($dispatchersEvents as $dispatchersEventName => $listeners) {
                                    foreach ($listeners as $listener) {
                                        $lazy = ($listener['lazy'] == 1)? 'true' : 'false';
                                        $staticInstantiator = (isset($listener['singletonMethod']))? ', '.$listener['singletonMethod'] : '';

                                        // check for Class existence (throws ReflectionException if not found)
                                        $class = new \ReflectionClass($listener['class']);
                                        $method = new \ReflectionMethod($class, $listener['method']);

                                        $phpCode .= 'self::$dispatcherRegistry[\''.$dispatcherName.'\'][] = array(\''.$dispatchersEventName
                                            .'\', \''.$listener['class'].'\', \''.$listener['method'].'\', '.$listener['priority'].', \''.$lazy.$staticInstantiator.'\');';
                                    }
                                }
                            }
                        }
                    } catch (\ReflectionException $e) {
                        throw new ErrorException(
                            'The following listener is not found or incorrectly set: '.$listener['class'],
                            array('Settings File' => $file->getRealPath(), 'Unknown listener' => $listener['class']),
                            $e->getCode(),
                            $e);
                        // FIXME: retrieve moduleDir/Name, to insert into ErrorException to auto-deactivate the module.
                    } catch (\Exception $e) {
                        throw new DevelopmentErrorException('The following settings file is not well structured: '.$file->getRealPath(), $e->getCode());
                    }
                }
                $cache->write($phpCode);
            }
        );
        include $cache->getPath();

        foreach (self::$dispatcherRegistry as $dispatcherName => $listeners) {
            /* @var $dispatcher EventDispatcher */
            $dispatcher = new self($dispatcherName);
            foreach ($listeners as $listener) {
                if ($listener[4] === true) { // lazy instantiation, use one-shot auto-destructive closure
                    $closure = function (ResponseEvent $event) use ($dispatcher, $listener, &$closure) {
                        $listenerInstance = (isset($listener[5]))? $listener[1]::$listener[5]() : new $listener[1]();
                        $listenerInstance->$listener[2]($event); // trigger event listener manually the first time
                        $dispatcher->addListener($listener[0], array($listenerInstance, $listener[2]), $listener[3]); // for next event, use lazy instance
                        $dispatcher->removeListener($listener[0], $closure);
                    };
                    $dispatcher->addListener($listener[0], $closure, $listener[3]);
                } else { // no lazy, so direct instantiation.
                    $dispatcher->addListener($listener[0], array((isset($listener[5]))? $listener[1]::$listener[5]() : new $listener[1](), $listener[2]), $listener[3]);
                }
            }
            self::$instances[$dispatcherName] = $dispatcher;
        }
    }

    private $name;

    private function __construct($dispatcherName)
    {
        $this->name = $dispatcherName;
    }
}
