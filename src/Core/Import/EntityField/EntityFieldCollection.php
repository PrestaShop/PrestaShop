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

namespace PrestaShop\PrestaShop\Core\Import\EntityField;

use ArrayIterator;

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
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->entityFields);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->entityFields[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->entityFields[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->entityFields[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->entityFields);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->entityFields);
    }
}
