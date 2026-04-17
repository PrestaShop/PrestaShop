<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Trait;

/**
 * Trait DirtyTrait
 * Provides functionality to track modified (dirty) properties of an object.
 * You need to manually call markDirty() method when a property is modified.
 */
trait DirtyTrait
{
    /**
     * Indicates whether the object has been modified.
     */
    protected array $dirtyProperties = [];

    /**
     * Marks a specific property as dirty (modified).
     *
     * @param string $propertyName
     *
     * @return void
     */
    public function markDirty(string $propertyName): void
    {
        $this->dirtyProperties[$propertyName] = true;
    }

    /**
     * Checks if a specific property is dirty (modified).
     *
     * @param string $propertyName
     *
     * @return bool
     */
    public function isDirty(string $propertyName): bool
    {
        return isset($this->dirtyProperties[$propertyName]) && $this->dirtyProperties[$propertyName];
    }

    /**
     * Retrieves a list of all dirty (modified) properties.
     *
     * @return array
     */
    public function getDirtyProperties(): array
    {
        return array_keys($this->dirtyProperties);
    }
}
