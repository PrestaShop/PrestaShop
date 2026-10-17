<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Storage;

use PrestaShop\PrestaShop\Core\Storage\Exception\StorageNotFoundException;
use PrestaShop\PrestaShop\Core\Storage\FactoryStorageInterface;
use PrestaShop\PrestaShop\Core\Storage\StorageInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Returns the storages from a service locator, which is filled with every service tagged with
 * StorageCollectorPass::STORAGE_TAG. Storages are therefore instantiated lazily, and a module
 * can register a new one without having to redefine this factory.
 */
class FactoryStorage implements FactoryStorageInterface
{
    public function __construct(
        private readonly ServiceLocator $storages
    ) {
    }

    public function get(string $name): StorageInterface
    {
        if (!$this->has($name)) {
            throw new StorageNotFoundException(sprintf(
                'Storage "%s" was not found, known storages are: %s.',
                $name,
                implode(', ', $this->getNames())
            ));
        }

        return $this->storages->get($name);
    }

    public function has(string $name): bool
    {
        return $this->storages->has($name);
    }

    public function getNames(): array
    {
        return array_keys($this->storages->getProvidedServices());
    }
}
