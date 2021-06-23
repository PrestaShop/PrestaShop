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

/**
 * Data structure to store curves
 */
class CurveCore
{
    /**
     * @var float[] indexed by string
     */
    protected $values = [];
    /**
     * @var string
     */
    protected $label;
    /**
     * Can be: bars, steps
     *
     * @var string
     */
    protected $type;

    /**
     * @param array $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    /**
     * @param bool $time_mode
     *
     * @return string
     */
    public function getValues($time_mode = false)
    {
        ksort($this->values);
        $string = '';
        foreach ($this->values as $key => $value) {
            $string .= '[' . addslashes((string) $key) . ($time_mode ? '000' : '') . ',' . (float) $value . '],';
        }

        return '{data:[' . rtrim($string, ',') . ']'
            . (!empty($this->label) ? ',label:"' . $this->label . '"' : '') . ''
            . (!empty($this->type) ? ',' . $this->type : '') . '}';
    }

    /**
     * @param string $x
     * @param float $y
     */
    public function setPoint($x, $y)
    {
        $this->values[(string) $x] = (float) $y;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @param string $type accepts only 'bars' or 'steps'
     */
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

    /**
     * @param string $x
     *
     * @return float|null return point if found, null else
     */
    public function getPoint($x)
    {
        if (array_key_exists((string) $x, $this->values)) {
            return $this->values[(string) $x];
        }
    }
}
