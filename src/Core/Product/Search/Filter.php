<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
     * @param $value
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
     * @param $nextEncodedFacets
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
