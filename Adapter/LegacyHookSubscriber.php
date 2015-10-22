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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Prophecy\Exception\Doubler\MethodNotFoundException;
use PrestaShopBundle\Service\Hook\HookEvent;
use PrestaShopBundle\Service\Hook\RenderingHookEvent;

/**
 * The subscriber for HookDispatcher that triggers legacy Hooks.
 *
 * This subscriber is registered into the HookDispatcher service via services.yml.
 * The legacy hooks are registered one by one in the dispatcher, but each corresponding
 * function is a magic method catched by __call().
 * This ensure the listeners' count is real.
 */
class LegacyHookSubscriber implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value are a function name
     * that will be solved by magic __call(). The function contains data to extract: hookId, moduleId
     *
     * TODO: add cache layer on $listeners
     *
     * @return array The listeners array
     */
    public static function getSubscribedEvents()
    {
        $listeners = array();

        $hooks = \Hook::getHooks();
        foreach ($hooks as $hook) {
            $name = $hook['name'];
            $id = $hook['id_hook'];

            $moduleListeners = array();
            $modules = \Hook::getHookModuleExecList($name);
            if (is_array($modules)) {
                foreach ($modules as $order => $module) {
                    $moduleId = $module['id_module'];
                    $functionName = 'call_'.$id.'_'.$moduleId;
                    $moduleListeners[] = array($functionName, 2000-$order);
                }
    
                if (count($moduleListeners)) {
                    $listeners[$name] = $moduleListeners;
                }
            }
        }
        return $listeners;
    }

    /**
     * This will handle magic methods registered as listeners.
     *
     * These methods are built with the following syntax:
     * "call_<hookID>_<moduleID>(HookEvent $event, $hookName)"
     *
     * @param string $name The method called
     * @param array $args The HookEvent, and then the hook name (eventName)
     * @throws \BadMethodCallException
     */
    public function __call($name, $args)
    {
        if (strpos($name, 'call_') !== 0) {
            throw new \BadMethodCallException('The call to \''.$name.'\' is not recognized.');
        }

        $ids = explode('_', $name);
        array_shift($ids); // remove 'call'

        if (count($ids) != 2) {
            throw new \BadMethodCallException('The call to \''.$name.'\' is not recognized.');
        }

        $moduleId = $ids[1];

        $hookName = $args[1];
        $event = $args[0];
        /* @var $event HookEvent */
        $content = \Hook::exec($hookName, $event->getHookParameters(), $moduleId, ($event instanceof RenderingHookEvent));

        if ($event instanceof RenderingHookEvent) {
            $event->setContent(array_values($content)[0], array_keys($content)[0]);
        }
    }
}
