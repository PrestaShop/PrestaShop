<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter;

use BadMethodCallException;
use Context;
use Hook;
use PrestaShopBundle\Service\Hook\HookEvent;
use PrestaShopBundle\Service\Hook\RenderingHookEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
        $listeners = [];

        // Hack Symfony cache clear : if context not mounted, bypass legacy call
        $legacyContext = Context::getContext();
        if (!$legacyContext || empty($legacyContext->shop) || empty($legacyContext->employee)) {
            return $listeners;
        }

        $hooks = Hook::getHooks();

        if (is_array($hooks)) {
            foreach ($hooks as $hook) {
                $name = strtolower($hook['name']);
                $id = $hook['id_hook'];

                $moduleListeners = [];
                $modules = [];
                // Symfony cache clear bug fix : call bqSQL alias function
                if (function_exists('bqSQL')) {
                    $modules = Hook::getHookModuleExecList($name);
                }

                if (is_array($modules)) {
                    foreach ($modules as $order => $module) {
                        $moduleId = $module['id_module'];
                        $functionName = 'call_' . $id . '_' . $moduleId;
                        $moduleListeners[] = [$functionName, 2000 - $order];
                    }
                } else {
                    $moduleListeners[] = ['call_' . $id . '_0', 2000];
                }

                $listeners[$name] = $moduleListeners;
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
     *
     * @throws BadMethodCallException
     */
    public function __call($name, $args)
    {
        if (!str_starts_with($name, 'call_')) {
            throw new BadMethodCallException('The call to \'' . $name . '\' is not recognized.');
        }

        $ids = explode('_', $name);
        array_shift($ids); // remove 'call'

        if (count($ids) !== 2) {
            throw new BadMethodCallException('The call to \'' . $name . '\' is not recognized.');
        }

        $moduleId = (int) $ids[1];
        [$event, $hookName] = $args;

        $content = Hook::exec(
            $hookName,
            $event->getHookParameters(),
            $moduleId,
            $event instanceof RenderingHookEvent
        );

        if (
            $event instanceof RenderingHookEvent
            && 0 !== $moduleId
            && !empty($content)
        ) {
            $event->setContent([array_values($content)[0]], array_keys($content)[0]);
        }
    }
}
