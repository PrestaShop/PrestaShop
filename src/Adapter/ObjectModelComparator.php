<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter;

use ObjectModelCore;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * Compares two instances of the same ObjectModel class and exposes their differences,
 * based on the fields declared in the ObjectModel definition.
 */
final class ObjectModelComparator
{
    /**
     * Fields that are not part of the ObjectModel definition but are still persisted.
     */
    private const EXTRA_FIELDS = [
        'id_shop_list' => ['type' => ObjectModelCore::TYPE_NOTHING],
        'id_shop_default' => ['type' => ObjectModelCore::TYPE_INT],
    ];

    /**
     * @throws InvalidArgumentException when the two objects are not of the same class
     */
    public function __construct(
        private readonly ObjectModelCore $oldObject,
        private readonly ObjectModelCore $newObject
    ) {
        if (get_class($oldObject) !== get_class($newObject)) {
            throw new InvalidArgumentException('Only objects of the same class can be compared.');
        }
    }

    /**
     * Returns the differences between the two objects, indexed by field name.
     * Each entry contains the "old" and "new" values (indexed by language id for multilang fields).
     *
     * @return array<string, array{old: mixed, new: mixed}>
     */
    public function getDiff(): array
    {
        $differences = [];
        $objectFields = array_merge($this->getObjectDefinedFields(), self::EXTRA_FIELDS);

        foreach ($objectFields as $field => $definition) {
            if (!property_exists($this->oldObject, $field)) {
                continue;
            }

            $fieldType = $definition['type'] ?? null;

            if (!empty($definition['lang']) && is_array($this->newObject->$field)) {
                $oldValues = is_array($this->oldObject->$field) ? $this->oldObject->$field : [];

                foreach ($this->newObject->$field as $idLang => $newValue) {
                    $oldValue = $oldValues[$idLang] ?? null;

                    if (!$this->isSameValue($oldValue, $newValue, $fieldType)) {
                        $differences[$field]['old'][$idLang] = $oldValue;
                        $differences[$field]['new'][$idLang] = $newValue;
                    }
                }
            } elseif (!$this->isSameValue($this->oldObject->$field, $this->newObject->$field, $fieldType)) {
                $differences[$field] = [
                    'old' => $this->oldObject->$field,
                    'new' => $this->newObject->$field,
                ];
            }
        }

        return $differences;
    }

    public function hasChanges(): bool
    {
        return !empty($this->getDiff());
    }

    public function getOldObject(): ObjectModelCore
    {
        return $this->oldObject;
    }

    public function getNewObject(): ObjectModelCore
    {
        return $this->newObject;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getObjectDefinedFields(): array
    {
        return $this->oldObject::$definition['fields'] ?? [];
    }

    /**
     * Compares two field values strictly, after normalizing them
     * according to the ObjectModel field type.
     */
    private function isSameValue(mixed $oldValue, mixed $newValue, ?int $fieldType): bool
    {
        return $this->normalize($oldValue, $fieldType) === $this->normalize($newValue, $fieldType);
    }

    /**
     * Casts a value to a canonical representation of its ObjectModel field type,
     * so that equivalent values ('10' and 10, '1' and true, ...) compare as equal.
     */
    private function normalize(mixed $value, ?int $fieldType): mixed
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            return array_map(fn (mixed $item): mixed => $this->normalize($item, $fieldType), $value);
        }

        return match ($fieldType) {
            ObjectModelCore::TYPE_INT => (int) $value,
            ObjectModelCore::TYPE_BOOL => (bool) $value,
            ObjectModelCore::TYPE_FLOAT => (float) $value,
            ObjectModelCore::TYPE_STRING, ObjectModelCore::TYPE_HTML, ObjectModelCore::TYPE_SQL, ObjectModelCore::TYPE_DATE => (string) $value,
            default => is_scalar($value) ? (string) $value : $value,
        };
    }
}
