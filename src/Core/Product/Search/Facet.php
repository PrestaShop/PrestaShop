<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Product\Search;

class Facet
{
    private $label;
    private $type;
    private $displayed = true;
    private $properties = [];
    private $filters = [];
    private $multipleSelectionAllowed = true;
    private $widgetType = 'radio';

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

    public function setDisplayed($displayed = true)
    {
        $this->displayed = $displayed;

        return $this;
    }

    public function isDisplayed()
    {
        return $this->displayed;
    }

    public function setWidgetType($widgetType)
    {
        $this->widgetType = $widgetType;

        return $this;
    }

    public function getWidgetType()
    {
        return $this->widgetType;
    }
}
