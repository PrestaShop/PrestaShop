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
     * Whether or not the filter is displayed.
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
