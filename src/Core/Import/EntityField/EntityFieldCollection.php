<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\EntityField;

use ArrayIterator;
use ReturnTypeWillChange;
use Traversable;

/**
 * Class EntityFieldCollection defines an entity field collection.
 */
final class EntityFieldCollection implements EntityFieldCollectionInterface
{
    /**
     * @var array
     */
    private $entityFields = [];

    /**
     * {@inheritdoc}
     */
    public function addEntityField(EntityFieldInterface $entityField)
    {
        $this->entityFields[] = $entityField;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredFields()
    {
        $requiredFields = [];

        /** @var EntityFieldInterface $entityField */
        foreach ($this->entityFields as $entityField) {
            if ($entityField->isRequired()) {
                $requiredFields[] = $entityField->getName();
            }
        }

        return $requiredFields;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $array = [];

        /** @var EntityFieldInterface $entityField */
        foreach ($this->entityFields as $entityField) {
            $array[] = [
                'label' => $entityField->getLabel(),
                'description' => $entityField->getDescription(),
                'required' => $entityField->isRequired(),
            ];
        }

        return $array;
    }

    /**
     * {@inheritdoc}
     */
    public static function createFromArray(array $entityFields)
    {
        $collection = new self();

        foreach ($entityFields as $entityField) {
            $collection->addEntityField($entityField);
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->entityFields);
    }

    /**
     * {@inheritdoc}
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->entityFields[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        $this->entityFields[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        unset($this->entityFields[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->entityFields);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->entityFields);
    }
}
