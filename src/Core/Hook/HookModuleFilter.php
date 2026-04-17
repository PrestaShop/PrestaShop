<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
