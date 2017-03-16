<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
        return $this->getDirection() === 'random';
    }

    public function toArray()
    {
        return [
            'entity'        => $this->entity,
            'field'         => $this->field,
            'direction'     => $this->direction,
            'label'         => $this->label,
            'urlParameter'  => $this->toString()
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

    public function toLegacyOrderWay()
    {
        return $this->getDirection();
    }
}
