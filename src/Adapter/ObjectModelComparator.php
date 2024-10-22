<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter;

use ObjectModel;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

class ObjectModelComparator
{
    /** @var ObjectModel old object model */
    protected $oldObject;

    /** @var ObjectModel new object model */
    protected $newObject;

    /**
     * @param ObjectModel $oldObject
     * @param ObjectModel $newObject
     *
     * @throws InvalidArgumentException
     */
    public function __construct(ObjectModel $oldObject, ObjectModel $newObject)
    {
        if (get_class($oldObject) !== get_class($newObject)) {
            throw new InvalidArgumentException('Cant compare different objects type');
        }

        $this->oldObject = $oldObject;
        $this->newObject = $newObject;
    }

    /**
     * Returns an array of two objectModels differences
     *
     * @return array
     */
    public function getDiff(): array
    {
        $differences = [];
        $objectFields = array_merge($this->getObjectDefinedFields($this->oldObject), $this->getObjectNotDefinedFields());

        foreach ($objectFields as $field => $definition) {
            if (!property_exists($this->oldObject, $field)) {
                continue;
            }

            $fieldType = $definition['type'] ?? null;

            if (!empty($definition['lang']) && $definition['lang'] && is_array($this->newObject->$field)) {
                foreach ($this->newObject->$field as $idLang => $newValue) {
                    $oldValue = $this->oldObject->$field[$idLang] ?? null;

                    if (!$this->fieldValueComparator($oldValue, $newValue, $fieldType)) {
                        $differences[$field]['old'][$idLang] = $oldValue;
                        $differences[$field]['new'][$idLang] = $newValue;
                    }
                }
            } else {
                if (!$this->fieldValueComparator($this->oldObject->$field, $this->newObject->$field, $fieldType)) {
                    $differences[$field] = [
                        'old' => $this->oldObject->$field,
                        'new' => $this->newObject->$field,
                    ];
                }
            }
        }

        return $differences;
    }

    public function hasChanges(): bool
    {
        return !empty($this->getDiff());
    }

    /**
     * Returns old ObjectModel
     *
     * @return ObjectModel
     */
    public function getOldObject(): ObjectModel
    {
        return $this->oldObject;
    }

    /**
     * Returns new ObjectModel
     *
     * @return ObjectModel
     */
    public function getNewObject(): ObjectModel
    {
        return $this->newObject;
    }

    /**
     * Returns ObjectModel fields list
     *
     * @param ObjectModel $objectModel
     *
     * @return array
     */
    protected function getObjectDefinedFields(ObjectModel $objectModel): array
    {
        return $objectModel::$definition['fields'] ?? [];
    }

    /**
     * Returns ObjectModel not defined fields list
     *
     * @return array
     */
    protected function getObjectNotDefinedFields(): array
    {
        return [
            'id_shop_list' => ['type' => ObjectModel::TYPE_NOTHING],
            'id_shop_default' => ['type' => ObjectModel::TYPE_INT],
        ];
    }

    /**
     * Compare field values
     *
     * @param $oldValue
     * @param $newValue
     * @param int|null $fieldType
     *
     * @return bool
     */
    private function fieldValueComparator($oldValue, $newValue, ?int $fieldType = null): bool
    {
        return $oldValue == $newValue;
    }
}
