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

use Symfony\Component\EventDispatcher\EventDispatcher as SfEventDispatcher;
use Symfony\Component\Config\ConfigCacheFactory;
use Symfony\Component\Config\ConfigCacheInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;
use PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException;
use PrestaShop\PrestaShop\Core\Foundation\Exception\ErrorException;
use Symfony\Component\EventDispatcher\Event;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;

/**
 * The current EventDispatcher is used to trigger a lot of event system widely.
 * You can get system EventDispatchers from the container (see below), but also create your own directly with the constructor.
 *
 * settings.yml files will be able to add some listeners to initialization time dispatchers:
 * Example of settings.yml file to register a listener on 'message' dispatcher, for 'error_message' event name:
 * dispatchers:
 *     message:
 *         error_message:
 *             - class: PrestaShop\PrestaShop\Tests\RouterTest\ErrorMessageLoggerExample
 *               method: onErrorEvent
 *               priority: 42
 *               lazy: 1
 *               singletonMethod: getInstance
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
 *      - init_action
 *      - before_action
 *      - after_action
 *      - close_action
 *      - shutdown
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
 * How to access these dispatchers:
 * You must call them from the $container instance, with the 'final:' prefix.
 * (final prefixed services cannot be injected in other services)
 *
 * Example:
 * - $container->make('final:EventDispatcher/routing');
 * - $container->make('final:EventDispatcher/message');
 * - $container->make('final:EventDispatcher/log');
 */
class EventDispatcher extends SfEventDispatcher
{
    /**
     * Indexed array of dispatchers, to avoid duplicated names (and to allow Core code to access them all :))
     * @var EventDispatcher
     */
    protected static $instances = array();

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
            array('error_message', 'PrestaShop\\PrestaShop\\Core\\Foundation\\Log\\MessageStackManager', 'onError', -127, false),
            array('warning_message', 'PrestaShop\\PrestaShop\\Core\\Foundation\\Log\\MessageStackManager', 'onWarning', -127, false),
            array('info_message', 'PrestaShop\\PrestaShop\\Core\\Foundation\\Log\\MessageStackManager', 'onInfo', -127, false),
            array('success_message', 'PrestaShop\\PrestaShop\\Core\\Foundation\\Log\\MessageStackManager', 'onSuccess', -127, false),
        ), // all events triggered when a PHP code wants to post a message (warnings, notices, messages to flash on the screen, etc...)
    );

    /**
     * Scan configuration files (Core and modules directories) to register listeners to event dispatchers that
     * are instanciated as singletons at the same time. Each singleton is indexed by its name.
     * Then use static registry $dispatcherRegistry to init all the registered listeners to event dispatchers.
     *
     * @param Container $container The initialized services container
     * @param string $rootDir The root directory path.
     * @param string $cacheDir The cache directory path.
     * @param string $moduleDir The module directory path to scan configuration files.
     * @param boolean $debug True to force debug mode (cache fil is generated each time).
     * @throws DevelopmentErrorException If a configuration file is malformed.
     */
    final public static function initDispatchers(Container &$container, $rootDir, $cacheDir, $moduleDir, $debug = false)
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
            $dispatcher = new static($dispatcherName, $container);
            foreach ($listeners as $listener) {
                if ($listener[4] === true) { // lazy instantiation, use one-shot auto-destructive closure
                    $closure = function (ResponseEvent $event) use ($dispatcher, $listener, &$closure) {
                        if (isset($listener[5])) {
                            $listenerInstance = $listener[1]::$listener[5]();
                        } else {
                            if (!$container->knows($listener[1])) {
                                $container->bind($listener[1], $listener[1]);
                            }
                            $listenerInstance = $container->make($listener[1]);
                        }

                        $listenerInstance->$listener[2]($event); // trigger event listener manually the first time
                        $dispatcher->addListener($listener[0], array($listenerInstance, $listener[2]), $listener[3]); // for next event, use the created lazy instance
                        $dispatcher->removeListener($listener[0], $closure);
                    };
                    $dispatcher->addListener($listener[0], $closure, $listener[3]);
                } else { // no lazy, so direct instantiation.
                    if (isset($listener[5])) {
                        $listenerInstance = $listener[1]::$listener[5]();
                    } else {
                        if (!$container->knows($listener[1])) {
                            $container->bind($listener[1], $listener[1]);
                        }
                        $listenerInstance = $container->make($listener[1]);
                    }
                    $dispatcher->addListener($listener[0], array($listenerInstance, $listener[2]), $listener[3]);
                }
            }
        }
    }

    /**
     * Name of the Dispatcher.
     * @var string
     */
    private $name;

    /**
     * Container instance, by reference. Optional.
     * @var Container
     */
    private $container = null;

    /**
     * Constructs a dispatcher with a specific unique name.
     *
     * The name must be unique system widely.
     * If a container is given, then the dispatcher will be attached to it with the following service name:
     * 'EventDispatcher/<$dispatcherName>'.
     *
     * @param string $dispatcherName The dispatcher name (must be unique).
     * @param Container $container Optional container to attach the dispatcher in (passed by reference).
     * @throws DevelopmentErrorException If the eventDispatcher already exists (duplicated name).
     * @throws \Core_Foundation_IoC_Exception If the eventDispatcher already exists in the container (duplicated name).
     */
    final public function __construct($dispatcherName, Container &$container = null)
    {
        if (array_key_exists($dispatcherName, self::$instances)) {
            throw new DevelopmentErrorException('The dispatcher name already exists in the system.');
        }
        $this->name = $dispatcherName;
        if ($container !== null) {
            $this->container = $container;
            $this->container->bind('final:EventDispatcher/'.$dispatcherName, $this, true);
        }
        self::$instances[$dispatcherName] = $this;
    }

    /**
     * Dispatch an event to its listeners.
     *
     * @see EventDispatcherInterface::dispatch()
     *
     * @param string $eventName The name of the event.
     * @param Event $event The event to send to the listeners.
     */
    final public function dispatch($eventName, Event $event = null)
    {
        if (null === $event) {
            $event = new BaseEvent();
        }
        if ($event instanceof BaseEvent && $this->container !== null) {
            $event->setContainer($this->container);
        }

        return parent::dispatch($eventName, $event);
    }
}
