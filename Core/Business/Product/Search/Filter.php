<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search;

class Filter
{
    private $label;

    /**
     * Internal type, used by query logic.
     */
    private $type;

    /**
     * Whether or not the filter is used in the query.
     */
    private $active;

    /**
     * Whether or not the user can interact with the filter.
     */
    private $available = true;

    /**
     * Whether or not the filter is displayed.
     * A filter may be displayed but in a non-interactive state,
     * that's why we have $available and $displayed.
     */
    private $displayed = true;

    private $properties = [];
    private $magnitude;
    private $value;
    private $nextEncodedFacets;

    public function toArray()
    {
        return [
            'label'             => $this->label,
            'type'              => $this->type,
            'active'            => $this->active,
            'available'         => $this->available,
            'displayed'         => $this->displayed,
            'properties'        => $this->properties,
            'magnitude'         => $this->magnitude,
            'value'             => $this->value,
            'nextEncodedFacets' => $this->nextEncodedFacets
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

    public function setActive($active = true)
    {
        $this->active = $active;
        return $this;
    }

    public function isActive()
    {
        return $this->active;
    }

    public function setDisplayed($displayed = true)
    {
        $this->displayed = $displayed;
        return $this;
    }

    public function isDisplayed()
    {
        return $this->displayed;
    }

    public function setNextEncodedFacets($nextEncodedFacets)
    {
        $this->nextEncodedFacets = $nextEncodedFacets;
        return $this;
    }

    public function getNextEncodedFacets()
    {
        return $this->nextEncodedFacets;
    }
}
