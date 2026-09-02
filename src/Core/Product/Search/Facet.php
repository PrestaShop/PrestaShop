<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Product\Search;

/**
 * We call a facet a set of filters combined with logical operators.
 */
class Facet
{
    /**
     * @var string the facet label
     */
    private $label = '';

    /**
     * @var string the facet type
     */
    private $type = '';

    /**
     * @var bool if true, the facet is displayed
     */
    private $displayed = true;

    /**
     * @var array the facet properties
     */
    private $properties = [];

    /**
     * @var array the facet filters
     */
    private $filters = [];

    /**
     * @var bool if true, allows the multiple selection
     */
    private $multipleSelectionAllowed = true;

    /**
     * @var string the widget type
     */
    private $widgetType = 'radio';

    /**
     * @return array an array representation of the facet
     */
    public function toArray()
    {
        return [
            'label' => $this->label,
            'displayed' => $this->displayed,
            'type' => $this->type,
            'properties' => $this->properties,
            'filters' => array_map(function (Filter $filter) {
                return $filter->toArray();
            }, $this->filters),
            'multipleSelectionAllowed' => $this->multipleSelectionAllowed,
            'widgetType' => $this->widgetType,
        ];
    }

    /**
     * @param string $label the facet label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string the facet label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $type the facet type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string the facet type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $name the facet property name
     * @param mixed $value the facet property value
     *
     * @return $this
     */
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;

        return $this;
    }

    /**
     * @param string $name the facet property name
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
     * @param Filter $filter the facet filter
     *
     * @return $this
     */
    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * @return array the list of facet filters
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param bool $isAllowed allows/disallows the multiple selection
     *
     * @return $this
     */
    public function setMultipleSelectionAllowed($isAllowed = true)
    {
        $this->multipleSelectionAllowed = $isAllowed;

        return $this;
    }

    /**
     * @return bool returns true if multiple selection is allowed
     */
    public function isMultipleSelectionAllowed()
    {
        return $this->multipleSelectionAllowed;
    }

    /**
     * @param bool $displayed sets the display of the facet
     *
     * @return $this
     */
    public function setDisplayed($displayed = true)
    {
        $this->displayed = $displayed;

        return $this;
    }

    /**
     * @return bool returns true if the facet is displayed
     */
    public function isDisplayed()
    {
        return $this->displayed;
    }

    /**
     * @param string $widgetType sets the widget type of the facet
     *
     * @return $this
     */
    public function setWidgetType($widgetType)
    {
        $this->widgetType = $widgetType;

        return $this;
    }

    /**
     * @return string returns the facet widget type
     */
    public function getWidgetType()
    {
        return $this->widgetType;
    }
}
