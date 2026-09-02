<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Module;

/**
 * @method delete(string $name) will be added in 9.0
 */
interface ModuleManagerInterface
{
    /**
     * @param string $name
     * @param mixed|null $source can be anything a SourceHandler can handle
     *
     * @return bool
     */
    public function install(string $name, $source = null): bool;

    public function uninstall(string $name, bool $deleteFiles = false): bool;

    public function upgrade(string $name, $source = null): bool;

    public function enable(string $name): bool;

    public function disable(string $name): bool;

    public function reset(string $name, bool $keepData = false): bool;

    public function postInstall(string $name): bool;

    public function isInstalled(string $name): bool;

    public function isEnabled(string $name): bool;

    public function getError(string $name): string;
}
