<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

class Facet
{
    private $label;
    private $type;
    private $properties = [];
    private $filters = [];
    private $multipleSelectionAllowed = true;

    public function toArray()
    {
        return [
            'label'         => $this->label,
            'type'          => $this->type,
            'properties'    => $this->properties,
            'filters'       => array_map(function (Filter $filter) {
                return $filter->toArray();
            }, $this->filters),
            'multipleSelectionAllowed' => $this->multipleSelectionAllowed
        ];
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

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;
        return $this;
    }

    public function getProperty($name)
    {
        if (!array_key_exists($name, $this->properties)) {
            return null;
        }
        return $this->properties[$name];
    }

    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
        return $this;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function setMultipleSelectionAllowed($yes = true)
    {
        $this->multipleSelectionAllowed = $yes;
        return $this;
    }

    public function isMultipleSelectionAllowed()
    {
        return $this->multipleSelectionAllowed;
    }
}
