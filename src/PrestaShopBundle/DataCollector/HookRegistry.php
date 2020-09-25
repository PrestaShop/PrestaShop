<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\DataCollector;

use PrestaShop\PrestaShop\Core\Module\ModuleInterface;

/**
 * Collect all hooks information dispatched during a request.
 */
final class HookRegistry
{
    const HOOK_NOT_CALLED = 'notCalled';
    const HOOK_CALLED = 'called';

    /**
     * @var array the current selected hook during the request
     */
    private $currentHook = null;

    /**
     * @var array the list of hooks data
     */
    private $hooks;

    public function __construct()
    {
        $this->hooks = [
            self::HOOK_CALLED => [],
            self::HOOK_NOT_CALLED => [],
        ];
    }

    /**
     * @param $hookName string
     * @param $hookArguments array
     * @param $file string filepath where the "Hook::exec" call have been done
     * @param $line string position in file where the "Hook::exec" call have been done
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
     * @param ModuleInterface $module
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
     * @param ModuleInterface $module
     * @param $args array All arguments passed to the Module callback
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
     * @param ModuleInterface $module
     * @param $args array All arguments passed to the Module callback
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
        return $this->hooks['called'];
    }

    /**
     * @return array the list of uncalled hooks
     */
    public function getNotCalledHooks()
    {
        return $this->hooks['notCalled'];
    }

    /**
     * @return array the list of dispatched hooks
     */
    public function getHooks()
    {
        return $this->hooks['called'] + $this->hooks['notCalled'];
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
