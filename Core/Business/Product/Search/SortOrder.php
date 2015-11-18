<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

use Exception;

class SortOrder
{
    private $entity;
    private $field;
    private $direction;

    public function __construct($entity, $field, $direction = 'asc')
    {
        $this
            ->setEntity($entity)
            ->setField($field)
            ->setDirection($direction)
        ;
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
        if (!in_array($direction, ['asc', 'desc'])) {
            throw new Exception(sprintf(
                'Invalid SortOrder direction `%s`. Expecting one of: `ASC`, `DESC`.',
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
