<?php

namespace PrestaShop\PrestaShop\Core\Module;

class HookConfigurator
{
    private $hookRepository;

    public function __construct(HookRepository $hookRepository)
    {
        $this->hookRepository = $hookRepository;
    }


    /**
     * $hooks is a hook configuration description
     * as found in theme.yml,
     * it has a format like:
     * [
     * 		"someHookName" => [
     * 			null,
     * 			"blockstuff",
     * 			"othermodule"
     * 		],
     * 		"someOtherHookName" => [
     * 			null,
     * 			"blockmenu" => [
     * 				"except_pages" => ["category", "product"]
     * 			]
     * 		],
     * 		"hookyHooka" => [
     * 			null,
     * 			"blocknewsletter" => [
     * 				"do_not_unkhook" => true
     * 			]
     * 		]
     * ]
     */
    public function getThemeHooksConfiguration(array $hooks)
    {
        $uniqueModuleList = $this->getUniqueModuleToHookList($hooks);
        $currentHooks = $this->hookRepository->getDisplayHooks();

        foreach ($currentHooks as $hookName => $moduleList) {
            foreach ($moduleList as $key => $value) {
                if (in_array($value, $uniqueModuleList)) {
                    unset($currentHooks[$hookName][$key]);
                }
            }
        }

        foreach ($hooks as $hookName => $modules) {
            $existing = $currentHooks[$hookName];
            $currentHooks[$hookName] = [];
            foreach ($modules as $module) {
                if ($module === null) {
                    foreach ($existing as $m) {
                        $currentHooks[$hookName][] = $m;
                    }
                } else {
                    $currentHooks[$hookName][] = $module;
                }
            }
        }

        return $currentHooks;
    }

    private function getUniqueModuleToHookList(array $hooks)
    {
        $list = [];
        foreach ($hooks as $hookName => $modules) {
            $list = array_merge($list, $modules);
        }

        return $list;
    }
}
