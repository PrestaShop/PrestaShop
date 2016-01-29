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
        $currentHooks = $this->hookRepository->getDisplayHooks();

        foreach ($hooks as $hookName => $modules) {
            foreach ($modules as $module) {
                if ($module === null) {
                    continue;
                }
                $currentHooks[$hookName][] = $module;
            }
        }

        return $currentHooks;
    }
}
