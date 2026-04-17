<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DataCollector;

use ModuleCore;
use PrestaShop\PrestaShop\Core\Module\Legacy\ModuleInterface;

/**
 * Collect all hooks information dispatched during a request.
 */
final class HookRegistry
{
    public const HOOK_NOT_CALLED = 'notCalled';
    public const HOOK_NOT_REGISTERED = 'notRegistered';
    public const HOOK_CALLED = 'called';

    /**
     * @var array the current selected hook during the request
     */
    private $currentHook = null;

    /**
     * @var array<string, array<string, array{args: array, name:string, location: string, modules: array}>> the list of hooks data
     */
    private $hooks;

    public function __construct()
    {
        $this->hooks = [
            self::HOOK_CALLED => [],
            self::HOOK_NOT_CALLED => [],
            self::HOOK_NOT_REGISTERED => [],
        ];
    }

    /**
     * @param string $hookName
     * @param array $hookArguments
     * @param string $file filepath where the "Hook::exec" call have been done
     * @param int $line position in file where the "Hook::exec" call have been done
     */
    public function selectHook($hookName, $hookArguments, $file, $line)
    {
        $this->currentHook = [
            'name' => $hookName,
            'args' => $hookArguments,
            'location' => "$file:$line",
            'status' => self::HOOK_NOT_CALLED,
            'modules' => [],
        ];
    }

    /**
     * Notify the registry that the selected hook have been called.
     */
    public function hookWasCalled()
    {
        $this->currentHook['status'] = self::HOOK_CALLED;
    }

    /**
     * Notify the registry that the selected hook have been called.
     */
    public function hookWasNotRegistered()
    {
        $this->currentHook['status'] = self::HOOK_NOT_REGISTERED;
    }

    /**
     * @param ModuleCore $module
     */
    public function hookedByModule(ModuleInterface $module)
    {
        $this->currentHook['modules'][$module->name] = [
            'callback' => [],
            'widget' => [],
        ];
    }

    /**
     * A callback have been executed by the module during the Hook dispatch.
     *
     * @param ModuleCore $module
     * @param array $args All arguments passed to the Module callback
     */
    public function hookedByCallback(ModuleInterface $module, $args)
    {
        $this->currentHook['modules'][$module->name]['callback'] = [
            'args' => $args,
        ];
    }

    /**
     * A widget have been rendered by the module during the Hook dispatch.
     *
     * @param ModuleCore $module
     * @param array $args All arguments passed to the Module callback
     */
    public function hookedByWidget(ModuleInterface $module, $args)
    {
        $this->currentHook['modules'][$module->name]['widget'] = [
            'args' => $args,
        ];
    }

    /**
     * @return array the list of called hooks
     */
    public function getCalledHooks()
    {
        return $this->hooks[self::HOOK_CALLED];
    }

    /**
     * @return array the list of uncalled hooks
     */
    public function getNotCalledHooks()
    {
        return $this->hooks[self::HOOK_NOT_CALLED];
    }

    /**
     * @return array the list of unregistered hooks
     */
    public function getNotRegisteredHooks()
    {
        return $this->hooks[self::HOOK_NOT_REGISTERED];
    }

    /**
     * @return array the list of dispatched hooks
     */
    public function getHooks()
    {
        return $this->hooks[self::HOOK_CALLED] + $this->hooks[self::HOOK_NOT_CALLED] + $this->hooks[self::HOOK_NOT_REGISTERED];
    }

    /**
     * Persist the selected hook into the list.
     *
     * Theses hooks will be used by the HookDataCollector
     */
    public function collect()
    {
        $name = $this->currentHook['name'];
        $status = $this->currentHook['status'];

        $hook = [
            'args' => $this->currentHook['args'],
            'name' => $name,
            'location' => $this->currentHook['location'],
            'modules' => $this->currentHook['modules'],
        ];

        $this->hooks[$status][$name][] = $hook;
    }
}
