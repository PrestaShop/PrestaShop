<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Product\Search;

use Exception;

/**
 * Class SortOrder.
 */
class SortOrder
{
    /**
     * @var
     */
    private $entity;
    /**
     * @var
     */
    private $field;
    /**
     * @var
     */
    private $direction;
    /**
     * @var
     */
    private $label;

    /**
     * SortOrder constructor.
     *
     * @param $entity
     * @param $field
     * @param string $direction
     *
     * @throws Exception
     */
    public function __construct($entity, $field, $direction = 'asc')
    {
        $this
            ->setEntity($entity)
            ->setField($field)
            ->setDirection($direction);
    }

    /**
     * @return SortOrder
     *
     * @throws Exception
     */
    public static function random()
    {
        return new static('', '', 'random');
    }

    /**
     * @return bool
     */
    public function isRandom()
    {
        return $this->getDirection() === 'random';
    }

    /**
     * @return array
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
     * @return string
     */
    public function toString()
    {
        return "{$this->entity}.{$this->field}.{$this->direction}";
    }

    /**
     * @param $paramValue
     *
     * @return SortOrder
     *
     * @throws Exception
     */
    public static function newFromString($paramValue)
    {
        list($entity, $field, $direction) = explode('.', $paramValue);

        return new SortOrder($entity, $field, $direction);
    }

    /**
     * @param $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param $entity
     *
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param $field
     *
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param $dir
     *
     * @return string
     *
     * @throws Exception
     */
    public function setDirection($dir)
    {
        $direction = strtolower($dir);
        if (!in_array($direction, ['asc', 'desc', 'random'])) {
            throw new Exception(sprintf(
                'Invalid SortOrder direction `%s`. Expecting one of: `ASC`, `DESC`, or `RANDOM`.',
                $dir
            ));
        }

        $this->direction = $direction;

        return $this->direction;
    }

    /**
     * @return mixed
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @return string
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
            } else {
                return 'p.';
            }
        } elseif ($this->entity === 'manufacturer') {
            return 'm.';
        }
    }

    /**
     * @param bool $prefix
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
     * @return mixed
     */
    public function toLegacyOrderWay()
    {
        return $this->getDirection();
    }
}
