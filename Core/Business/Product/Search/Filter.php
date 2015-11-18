<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

class Filter
{
    private $label;
    private $type;
    private $active;
    private $available;
    private $properties = [];
    private $magnitude;
    private $value;

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

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setMagnitude($magnitude)
    {
        $this->magnitude = (int)$magnitude;
        return $this;
    }

    public function getMagnitude()
    {
        return $this->magnitude;
    }
}
