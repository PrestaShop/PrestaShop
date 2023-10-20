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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Product\Search;

use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Product\Search\Exception\InvalidSortOrderDirectionException;

/**
 * This class define in which order the list of products will be sorted.
 */
class SortOrder
{
    /**
     * @var string the SortOrder entity
     */
    private $entity;
    /**
     * @var string the SortOrder field
     */
    private $field;
    /**
     * @var string the SortOrder direction
     */
    private $direction;
    /**
     * @var string string The SortOrder label
     */
    private $label;

    /**
     * SortOrder constructor.
     *
     * @param string $entity the SortOrder entity
     * @param string $field the SortOrder field
     * @param string $direction the SortOrder direction
     *
     * @throws InvalidSortOrderDirectionException
     */
    public function __construct($entity, $field, $direction = 'asc')
    {
        $this
            ->setEntity($entity)
            ->setField($field)
            ->setDirection($direction);
    }

    /**
     * Will returns a new Sort Order with random direction.
     *
     * @return SortOrder
     *
     * @throws InvalidSortOrderDirectionException
     */
    public static function random()
    {
        return new static('', '', 'random');
    }

    /**
     * @return bool if true, the Sort Order direction is random
     */
    public function isRandom()
    {
        return $this->getDirection() === 'random';
    }

    /**
     * @return array the array representation of a Sort Order
     */
    public function toArray()
    {
        return [
            'entity' => $this->entity,
            'field' => $this->field,
            'direction' => $this->direction,
            'label' => $this->label,
            'urlParameter' => $this->toString(),
        ];
    }

    /**
     * @return string the string representation of a Sort Order
     */
    public function toString()
    {
        return "{$this->entity}.{$this->field}.{$this->direction}";
    }

    /**
     * Creates a new Sort Order from string of this kind: {entity}.{field}.{direction}.
     *
     * @param string $sortOrderConfiguration the Sort Order configuration string
     *
     * @return SortOrder
     *
     * @throws InvalidSortOrderDirectionException
     */
    public static function newFromString($sortOrderConfiguration)
    {
        $sortParams = explode('.', $sortOrderConfiguration);

        if (count($sortParams) < 3) {
            throw new CoreException('Invalid argument');
        }

        list($entity, $field, $direction) = $sortParams;

        return new static($entity, $field, $direction);
    }

    /**
     * @param string $label the Sort Order label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string the Sort Order label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $entity the Sort Order entity
     *
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return string the Sort Order entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param string $field the Sort Order field
     *
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return string the Sort Order field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $direction
     *
     * @return string
     *
     * @throws InvalidSortOrderDirectionException
     */
    public function setDirection($direction)
    {
        $formattedDirection = strtolower($direction);
        if (!in_array($formattedDirection, ['asc', 'desc', 'random'])) {
            throw new InvalidSortOrderDirectionException($direction);
        }

        $this->direction = $formattedDirection;

        return $this->direction;
    }

    /**
     * @return string the Sort Order direction
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @return string Returns the order way using legacy prefix
     */
    private function getLegacyPrefix()
    {
        if ($this->entity === 'product') {
            if ($this->field === 'name') {
                return 'pl.';
            } elseif ($this->field === 'position') {
                return 'cp.';
            } elseif ($this->field === 'manufacturer_name') {
                $this->setField('name');

                return 'm.';
            }

            return 'p.';
        }
        if ($this->entity === 'manufacturer') {
            return 'm.';
        }

        return '';
    }

    /**
     * @param bool $prefix if true, relies on legacy prefix
     *
     * @return string
     */
    public function toLegacyOrderBy($prefix = false)
    {
        if ($prefix) {
            return $this->getLegacyPrefix() . $this->field;
        } elseif ($this->entity === 'manufacturer' && $this->field === 'name') {
            return 'manufacturer_name';
        } else {
            return $this->field;
        }
    }

    /**
     * @return string the legacy order way
     */
    public function toLegacyOrderWay()
    {
        return $this->getDirection();
    }
}
