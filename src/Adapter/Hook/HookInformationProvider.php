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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
