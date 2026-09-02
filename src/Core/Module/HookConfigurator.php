<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Module;

use Psr\Log\LoggerInterface;

class HookConfigurator
{
    public function __construct(
        private readonly HookRepository $hookRepository,
        private readonly ?LoggerInterface $logger = null,
        private readonly ?ModuleManager $moduleManager = null,
    ) {
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
     * ].
     */
    public function getThemeHooksConfiguration(array $hooks)
    {
        $hooks = array_filter($hooks, 'is_array');
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
                [];
            $currentHooks[$hookName] = [];
            foreach ($modules as $key => $module) {
                if ($module === null && $firstNullValueFound) {
                    $firstNullValueFound = false;
                    foreach ($existing as $m) {
                        // If module has been removed we ignore it but inform via a warning
                        if ($this->moduleManager && !$this->moduleManager->isOnDisk($m)) {
                            $this->logger?->warning(sprintf('Module %s was removed from disk, impossible to hook it', $m));
                            continue;
                        }
                        $currentHooks[$hookName][] = $m;
                    }
                } elseif (is_array($module)) {
                    // If module has been removed we ignore it but inform via a warning
                    if ($this->moduleManager && !$this->moduleManager->isOnDisk($key)) {
                        $this->logger?->warning(sprintf('Module %s was removed from disk, impossible to hook it', $key));
                        continue;
                    }
                    $currentHooks[$hookName][$key] = $module;
                } elseif ($module !== null) {
                    // If module has been removed we ignore it but inform via a warning
                    if ($this->moduleManager && !$this->moduleManager->isOnDisk($module)) {
                        $this->logger?->warning(sprintf('Module %s was removed from disk, impossible to hook it', $module));
                        continue;
                    }
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

    public function unhookModules(array $removedHooks): self
    {
        $cleanHooks = [];
        foreach ($removedHooks as $hookName => $moduleNames) {
            foreach ($moduleNames as $moduleName) {
                if (null === $moduleName) {
                    $cleanHooks[$hookName][] = $moduleName;
                    continue;
                }

                if ($this->moduleManager && !$this->moduleManager->isOnDisk($moduleName)) {
                    $this->logger?->warning(sprintf('Module %s was removed from disk, no need to unhook it', $moduleName));
                    continue;
                }
                $cleanHooks[$hookName][] = $moduleName;
            }
        }
        if (!empty($cleanHooks)) {
            $this->hookRepository->unHookModules($cleanHooks);
        }

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
