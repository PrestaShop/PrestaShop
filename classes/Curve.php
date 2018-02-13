<?php
/**
 * 2007-2018 PrestaShop
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

class CurveCore
{
    protected $values = array();
    protected $label;
    protected $type;

    /** @prototype void public function setValues($values) */
    public function setValues($values)
    {
        $this->values = $values;
    }

    public function getValues($time_mode = false)
    {
        ksort($this->values);
        $string = '';
        foreach ($this->values as $key => $value) {
            $string .= '['.addslashes((string)$key).($time_mode ? '000' : '').','.(float)$value.'],';
        }
        return '{data:['.rtrim($string, ',').']'.(!empty($this->label) ? ',label:"'.$this->label.'"' : '').''.(!empty($this->type) ? ','.$this->type : '').'}';
    }

    /** @prototype void public function setPoint(float $x, float $y) */
    public function setPoint($x, $y)
    {
        $this->values[(string)$x] = (float)$y;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function setType($type)
    {
        $this->type = '';
        if ($type == 'bars') {
            $this->type = 'bars:{show:true,lineWidth:10}';
        }
        if ($type == 'steps') {
            $this->type = 'lines:{show:true,steps:true}';
        }
    }

    public function getPoint($x)
    {
        if (array_key_exists((string)$x, $this->values)) {
            return $this->values[(string)$x];
        }
    }
}
