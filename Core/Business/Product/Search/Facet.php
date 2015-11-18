<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

class Facet
{
    private $label;
    private $type;
    private $properties = [];
    private $filters = [];

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
        return $this->properties[$name];
    }
}
