<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Storage;

use PrestaShop\PrestaShop\Core\Storage\Exception\StorageNotFoundException;

/**
 * Returns the storages registered in the application configuration, by name.
 */
interface FactoryStorageInterface
{
    /**
     * @param string $name storage name, see StorageSettings for the ones provided by the core
     *
     * @throws StorageNotFoundException when no storage is registered under that name
     */
    public function get(string $name): StorageInterface;

    public function has(string $name): bool;

    /**
     * @return string[] names of every registered storage
     */
    public function getNames(): array;
}
