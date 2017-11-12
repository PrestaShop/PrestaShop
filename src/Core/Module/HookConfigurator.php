<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShop\PrestaShop\Core\Module;

use PrestaShop\PrestaShop\Core\Module\HookRepository;

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
     *     "someHookName" => [
     *        null,
     *        "blockstuff",
     *        "othermodule"
     *     ],
     *     "someOtherHookName" => [
     *         null,
     *         "blockmenu" => [
     *             "except_pages" => ["category", "product"]
     *         ]
     *     ]
     * ]
     */
    public function getThemeHooksConfiguration(array $hooks)
    {
        $uniqueModuleList = $this->getUniqueModuleToHookList($hooks);
        $currentHooks = $this->hookRepository->getDisplayHooksWithModules();

        foreach ($currentHooks as $hookName => $moduleList) {
            foreach ($moduleList as $key => $value) {
                if (in_array($value, $uniqueModuleList)) {
                    unset($currentHooks[$hookName][$key]);
                }
            }
        }

        foreach ($hooks as $hookName => $modules) {
            $firstNullValueFound = true;
            $existing = isset($currentHooks[$hookName]) ?
                $currentHooks[$hookName] :
                []
            ;
            $currentHooks[$hookName] = [];
            foreach ($modules as $key => $module) {
                if ($module === null && $firstNullValueFound) {
                    $firstNullValueFound = false;
                    foreach ($existing as $m) {
                        $currentHooks[$hookName][] = $m;
                    }
                } elseif (is_array($module)) {
                    $currentHooks[$hookName][$key] = $module;
                } elseif ($module !== null) {
                    $currentHooks[$hookName][] = $module;
                }
            }
        }

        return $currentHooks;
    }

    public function setHooksConfiguration(array $hooks)
    {
        $this->hookRepository->persistHooksConfiguration(
            $this->getThemeHooksConfiguration($hooks)
        );
        return $this;
    }

    public function addHook($name, $title, $description)
    {
        $this->hookRepository->createHook($name, $title, $description);
        return $this;
    }

    private function getUniqueModuleToHookList(array $hooks)
    {
        $list = [];
        foreach ($hooks as $modules) {
            $list = array_merge($list, $modules);
        }

        return $list;
    }
}
