<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Product\Search;

use Exception;

class SortOrder
{
    private $entity;
    private $field;
    private $direction;
    private $label;

    public function __construct($entity, $field, $direction = 'asc')
    {
        $this
            ->setEntity($entity)
            ->setField($field)
            ->setDirection($direction)
        ;
    }

    public static function random()
    {
        return new static('', '', 'random');
    }

    public function isRandom()
    {
        return 'random' === $this->getDirection();
    }

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

    public function toString()
    {
        return "{$this->entity}.{$this->field}.{$this->direction}";
    }

    public static function newFromString($paramValue)
    {
        list($entity, $field, $direction) = explode('.', $paramValue);

        return new SortOrder($entity, $field, $direction);
    }

    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    public function getField()
    {
        return $this->field;
    }

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

    public function getDirection()
    {
        return $this->direction;
    }

    private function getLegacyPrefix()
    {
        if ('product' === $this->entity) {
            if ('name' === $this->field) {
                return 'pl.';
            } elseif ('position' === $this->field) {
                return 'cp.';
            } elseif ('manufacturer_name' === $this->field) {
                $this->setField('name');

                return 'm.';
            } else {
                return 'p.';
            }
        } elseif ('manufacturer' === $this->entity) {
            return 'm.';
        }
    }

    public function toLegacyOrderBy($prefix = false)
    {
        if ($prefix) {
            return $this->getLegacyPrefix().$this->field;
        } elseif ('manufacturer' === $this->entity && 'name' === $this->field) {
            return 'manufacturer_name';
        } else {
            return $this->field;
        }
    }

    public function toLegacyOrderWay()
    {
        return $this->getDirection();
    }
}
