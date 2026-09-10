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
     *         [
     *             "blockmenu" => [
     *                 "except_pages" => ["category", "product"]
     *             ]
     *         ]
     *     ]
     * ].
     *
     * The second hook shows a module carrying settings. It is a list entry rather than an array
     * key because that is what parsing a theme.yml produces, and a mapping cannot be written
     * alongside the `~` placeholder in the same YAML sequence.
     */
    public function getThemeHooksConfiguration(array $hooks)
    {
        $hooks = array_filter($hooks, 'is_array');
        $uniqueModuleList = $this->getUniqueModuleToHookList($hooks);
        $currentHooks = $this->hookRepository->getDisplayHooksWithModules();

        foreach ($currentHooks as $hookName => $moduleList) {
            foreach ($moduleList as $key => $value) {
                // A module hooked with exceptions comes back under its own name, holding them.
                // Comparing the raw value left those entries hooked where they already were as
                // well as where the theme asks for them.
                [$moduleName] = $this->readModuleEntry($key, $value);
                if (in_array($moduleName, $uniqueModuleList, true)) {
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
                    foreach ($existing as $existingKey => $m) {
                        [$existingName, $existingSettings] = $this->readModuleEntry($existingKey, $m);
                        // If module has been removed we ignore it but inform via a warning
                        if ($this->moduleManager && !$this->moduleManager->isOnDisk($existingName)) {
                            $this->logger?->warning(sprintf('Module %s was removed from disk, impossible to hook it', $existingName));
                            continue;
                        }
                        $currentHooks[$hookName][] = $existingSettings === [] ? $existingName : [$existingName => $existingSettings];
                    }
                } elseif (is_array($module)) {
                    [$moduleName, $moduleSettings] = $this->readModuleEntry($key, $module);
                    // If module has been removed we ignore it but inform via a warning
                    if ($this->moduleManager && !$this->moduleManager->isOnDisk($moduleName)) {
                        $this->logger?->warning(sprintf('Module %s was removed from disk, impossible to hook it', $moduleName));
                        continue;
                    }
                    $currentHooks[$hookName][] = [$moduleName => $moduleSettings];
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
            foreach ($modules as $key => $module) {
                if ($module === null) {
                    // The placeholder keeping existing modules, not a module of its own.
                    continue;
                }
                // Only the name identifies the module. Keeping the whole entry left the
                // comparison above unable to match it.
                [$moduleName] = $this->readModuleEntry($key, $module);
                $list[] = $moduleName;
            }
        }

        return $list;
    }

    /**
     * A module in a hook list is either a plain name, or - when it carries settings - a
     * single-entry map of that name to them. YAML can express the second form only as a list
     * item (`- blocklanguages: {except_pages: [...]}`), which is also the only form that can sit
     * next to the `~` placeholder every shipped theme uses, so the name is inside the entry and
     * never in its position. Configurations written as an array key are still accepted.
     *
     * The name is what `HookRepository::persistHooksConfiguration()` reads back out of the entry,
     * so both forms are normalised here rather than in each caller.
     *
     * The same two shapes come back out of `HookRepository::getDisplayHooksWithModules()`, where a
     * module without exceptions is a list value and one with exceptions is a key holding them.
     *
     * @param int|string $key the entry's position, or the module name in the array-key form
     * @param mixed $module a module name, or an entry carrying its settings
     *
     * @return array{0: string, 1: array} the module name and its settings
     */
    private function readModuleEntry($key, $module): array
    {
        if (!is_array($module)) {
            return [(string) $module, []];
        }

        if (is_int($key)) {
            return [(string) key($module), (array) current($module)];
        }

        return [(string) $key, $module];
    }
}
