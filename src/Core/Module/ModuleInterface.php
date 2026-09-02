<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Module;

use Module as LegacyModule;

interface ModuleInterface
{
    public function getInstance(): ?LegacyModule;

    public function hasValidInstance(): bool;

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function get(string $name);

    public function onInstall(): bool;

    public function onPostInstall(): bool;

    public function onUninstall(): bool;

    /**
     * Called when switching the current theme of the selected shop.
     * You can update configuration, enable/disable modules...
     *
     * @return bool true for success
     */
    public function onEnable(): bool;

    /**
     * Not necessarily the opposite of enable. Use this method if
     * something must be done when switching to another theme (like uninstall
     * very specific modules for example).
     *
     * @return bool true for success
     */
    public function onDisable(): bool;

    /**
     * @return bool
     */
    public function onReset(): bool;

    /**
     * Execute up files. You can update configuration, update sql schema.
     * No file modification.
     *
     * @return bool true for success
     */
    public function onUpgrade(string $version): bool;
}
