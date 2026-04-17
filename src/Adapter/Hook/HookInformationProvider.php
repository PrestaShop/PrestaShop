<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Hook;

use Hook;

/**
 * Give information about the hooks.
 */
class HookInformationProvider
{
    /**
     * @param string $hookName
     *
     * @return bool
     */
    public function isDisplayHookName($hookName)
    {
        return Hook::isDisplayHookName($hookName);
    }

    /**
     * Return Hooks List.
     *
     * @param bool $position Where position is active
     * @param bool $onlyDisplayHooks Only hook with display hook name
     *
     * @return array Hooks List
     */
    public function getHooks($position = false, $onlyDisplayHooks = false)
    {
        return Hook::getHooks($position, $onlyDisplayHooks);
    }

    /**
     * Return Hooks list.
     *
     * @param int $hookId Hook id
     * @param int $moduleId Module id
     *
     * @return array Modules list
     */
    public function getModulesFromHook($hookId, $moduleId = null)
    {
        return Hook::getModulesFromHook($hookId, $moduleId);
    }

    /**
     * @param string $hookName
     *
     * @return array
     */
    public function getRegisteredModulesByHookName(string $hookName): array
    {
        $extraModulesList = Hook::getHookModuleExecList($hookName);

        return empty($extraModulesList) ? [] : $extraModulesList;
    }
}
