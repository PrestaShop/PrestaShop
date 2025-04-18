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

namespace PrestaShop\PrestaShop\Core\Hook;

/**
 * This service is responsible for filtering the list of modules for a given hook that is returned by
 * the getHookModuleExecList method from Hook.php. It is called at the very end of getHookModuleExecList.
 *
 * How to use it to filter a list of modules for a hook:
 *
 * In your module, create a service which implements the HookModuleFilterInterface and give it
 * the tag named core.hook_module_exec_filter. Then in your service, you can filter the list of modules
 * in the filterHookModuleExecList method, according to your own logic.
 *
 * Your service will automatically be sent in this class's constructor, and be used to filter the list of modules.
 */
class HookModuleFilter implements HookModuleFilterInterface
{
    private $hookModuleFilters;

    public function __construct(iterable $hookModuleFilters)
    {
        $this->hookModuleFilters = $hookModuleFilters;
    }

    public function filterHookModuleExecList(array $modules, string $hookName): array
    {
        foreach ($this->hookModuleFilters as $hookModuleFilter) {
            $modules = $hookModuleFilter->filterHookModuleExecList($modules, $hookName);
        }

        return $modules;
    }
}
