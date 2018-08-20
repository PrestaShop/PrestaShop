<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Hook;

use Hook;

/**
 * Give information about the hooks
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
     * Return Hooks List
     *
     * @param bool $position         Where position is active
     * @param bool $onlyDisplayHooks Only hook with display hook name
     *
     * @return array Hooks List
     */
    public function getHooks($position = false, $onlyDisplayHooks = false)
    {
        return Hook::getHooks($position, $onlyDisplayHooks);
    }

    /**
     * Return Hooks list
     *
     * @param int $hookId   Hook id
     * @param int $moduleId Module id
     *
     * @return array Modules list
     */
    public function getModulesFromHook($hookId, $moduleId = null)
    {
        return Hook::getModulesFromHook($hookId, $moduleId);
    }
}
