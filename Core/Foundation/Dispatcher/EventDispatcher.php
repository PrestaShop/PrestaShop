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
class EventDispatcher extends \Symfony\Component\EventDispatcher\EventDispatcher
{
    private static $instances = array();

    public final static function getInstance($dispatcherName = 'default')
    {
        if (!isset(self::$instances[$dispatcherName])) {
            self::$instances[$dispatcherName] = new self($dispatcherName);
        }
        return self::$instances[$dispatcherName];
    }


    private static $baseDispatcherRegistry = array(
        'routing' => array(
            array('cache_generation', 'PrestaShop\\PrestaShop\\Core\\Foundation\\Log\\RoutingLogger', 'onCacheGeneration', -255, false),
        ), // all event triggered during Routing (before action call, and after action result)
        'log' => array(
            
        ), // all event triggered when a log is dumped into the logger. WARNING: this could become slow if you listen to each log event!
        'error' => array(
            
        ), // all event triggered when a system wide error is thrown(triggered in Exception subclasses)
        'message' => array(
            
        ) // all event triggered when a PHP code wants to post a message (warnings, notices, messages to flash on the screen, etc...)
    );

    public final static function initDispatchers($forceDebug = false)
    {
        $configuration = \Adapter_ServiceLocator::get('Core_Business_ConfigurationInterface');
        
        $cache = (new ConfigCacheFactory($forceDebug || $configuration->get('_PS_MODE_DEV_')))->cache(
            $configuration->get('_PS_CACHE_DIR_').'dispatcher/init_subscribers.php',
            function (ConfigCacheInterface $cache) use(&$configuration) {
                $moduleCoreConfigExists = (count(glob($configuration->get('_PS_MODULE_DIR_').'*/CoreConfig/')) > 0);
                
                $settingsFilesFinder = Finder::create()->files()->name('settings.yml')->sortByName()->followLinks()
                    ->in($configuration->get('_PS_ROOT_DIR_').'/CoreConfig/');
                if($moduleCoreConfigExists) {
                    $settingsFilesFinder->in($configuration->get('_PS_MODULE_DIR_').'*/CoreConfig/');
                }
                
                $phpCode = '<'.'?php
';
                foreach($settingsFilesFinder as $file) {
                    try {
                        $settings = Yaml::parse(file_get_contents($file->getRealpath()));
                        if (isset($settings['dispatchers']) && is_array($dispatchers = $settings['dispatchers'])) {
                            foreach($dispatchers as $dispatcherName => $dispatchersEvents) {
                                foreach($dispatchersEvents as $dispatchersEventName => $listeners) {
                                    foreach($listeners as $listener) {
                                        $lazy = ($listener['lazy'] == 1)? 'true' : 'false';
                                        
                                        $phpCode .= 'self::$baseDispatcherRegistry[\''.$dispatcherName.'\'][] = array(\''.$dispatchersEventName
                                            .'\', \''.$listener['class'].'\', \''.$listener['method'].'\', '.$listener['priority'].', '.$lazy.');';
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        throw new \ErrorException('The following settings file is not well structured: '.$file->getRealPath(), $e->getCode());
                    }
                }
                $cache->write($phpCode);
            }
        );
        include $cache->getPath();

        foreach(self::$baseDispatcherRegistry as $dispatcherName => $listeners) {
            /* @var $dispatcher EventDispatcher */
            $dispatcher = new self($dispatcherName);
            foreach($listeners as $listener) {
                if ($listener[4] === true) { // lazy instantiation, use one-shot auto-destructive closure
                    $closure = function(ResponseEvent $event) use($dispatcher, $listener, &$closure) {
                        $listenerInstance = new $listener[1]();
                        $listenerInstance->$listener[2]($event); // trigger event listener manually the first time
                        $dispatcher->addListener($listener[0], array($listenerInstance, $listener[2]), $listener[3]); // for next event, use lazy instance
                        $dispatcher->removeListener($listener[0], $closure);
                    };
                    $dispatcher->addListener($listener[0], $closure, $listener[3]);
                } else { // no lazy, so direct instantiation.
                    $dispatcher->addListener($listener[0], array(new $listener[1](), $listener[2]), $listener[3]);
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

    protected final function getConfigCacheFactory($forceDebug = false)
    {
        if (null === $this->configCacheFactory) {
            $this->configCacheFactory = new ConfigCacheFactory($forceDebug || $this->configuration->get('_PS_MODE_DEV_'));
        }

        return $this->configCacheFactory;
    }
}
