<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Product\Search;

class Filter
{
    /**
     * @var string the filter label
     */
    private $label = '';

    /**
     * @var string internal type, used by query logic
     */
    private $type = '';

    /**
     * @var bool whether or not the filter is used in the query
     */
    private $active = false;

    /**
     * @var bool whether or not the filter is displayed
     */
    private $displayed = true;

    /**
     * @var array the filter properties
     */
    private $properties = [];

    /**
     * @var int the filter magnitude
     */
    private $magnitude = 0;

    /**
     * @var mixed the filter value
     */
    private $value;

    /**
     * @var array the filter next encoded facets
     */
    private $nextEncodedFacets = [];

    /**
     * @return array an array representation of the filter
     */
    public function toArray()
    {
        return [
            'label' => $this->label,
            'type' => $this->type,
            'active' => $this->active,
            'displayed' => $this->displayed,
            'properties' => $this->properties,
            'magnitude' => $this->magnitude,
            'value' => $this->value,
            'nextEncodedFacets' => $this->nextEncodedFacets,
        ];
    }

    /**
     * @param string $label the filter label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string the filter label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $type the filter type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string the filter type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $name the filter property name
     * @param mixed $value the filter property value
     *
     * @return $this
     */
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;

        return $this;
    }

    /**
     * @param string $name the filter property name
     *
     * @return mixed|null
     */
    public function getProperty($name)
    {
        if (!array_key_exists($name, $this->properties)) {
            return null;
        }

        return $this->properties[$name];
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int $magnitude the filter magnitude
     *
     * @return $this
     */
    public function setMagnitude($magnitude)
    {
        $this->magnitude = (int) $magnitude;

        return $this;
    }

    /**
     * @return int the filter magnitude
     */
    public function getMagnitude()
    {
        return $this->magnitude;
    }

    /**
     * @param bool $active sets the activation of the filter
     *
     * @return $this
     */
    public function setActive($active = true)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return bool returns true if the filter is active
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $displayed sets the display of the filter
     *
     * @return $this
     */
    public function setDisplayed($displayed = true)
    {
        $this->displayed = $displayed;

        return $this;
    }

    /**
     * @return bool returns true if the filter is displayed
     */
    public function isDisplayed()
    {
        return $this->displayed;
    }

    /**
     * @param array $nextEncodedFacets
     *
     * @return $this
     */
    public function setNextEncodedFacets($nextEncodedFacets)
    {
        $this->nextEncodedFacets = $nextEncodedFacets;

        return $this;
    }

    /**
     * @return array
     */
    public function getNextEncodedFacets()
    {
        return $this->nextEncodedFacets;
    }
}
