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
class ChartCore
{
    protected static $poolId = 0;

    protected $width = 600;
    protected $height = 300;

    /* Time mode */
    protected $timeMode = false;
    protected $from;
    protected $to;
    protected $format;
    protected $granularity;

    protected $curves = [];

    /** @prototype void public static function init(void) */
    public static function init()
    {
        if (!self::$poolId) {
            ++self::$poolId;

            return true;
        }
    }

    /** @prototype void public function __construct() */
    public function __construct()
    {
        ++self::$poolId;
    }

    /** @prototype void public function setSize(int $width, int $height) */
    public function setSize($width, $height)
    {
        $this->width = (int) $width;
        $this->height = (int) $height;
    }

    /** @prototype void public function setTimeMode($from, $to, $granularity) */
    public function setTimeMode($from, $to, $granularity)
    {
        $this->granularity = $granularity;

        if (Validate::isDate($from)) {
            $from = strtotime($from);
        }
        $this->from = $from;
        if (Validate::isDate($to)) {
            $to = strtotime($to);
        }
        $this->to = $to;

        if ($granularity == 'd') {
            $this->format = '%d/%m/%y';
        }
        if ($granularity == 'w') {
            $this->format = '%d/%m/%y';
        }
        if ($granularity == 'm') {
            $this->format = '%m/%y';
        }
        if ($granularity == 'y') {
            $this->format = '%y';
        }

        $this->timeMode = true;
    }

    public function getCurve($i)
    {
        if (!array_key_exists($i, $this->curves)) {
            $this->curves[$i] = new Curve();
        }

        return $this->curves[$i];
    }

    /** @prototype void public function display() */
    public function display()
    {
        echo $this->fetch();
    }

    public function fetch()
    {
        if ($this->timeMode) {
            $options = 'xaxis:{mode:"time",timeformat:\'' . addslashes($this->format) . '\',min:' . $this->from . '000,max:' . $this->to . '000}';
            if ($this->granularity == 'd') {
                foreach ($this->curves as $curve) {
                    /* @var Curve $curve */
                    for ($i = $this->from; $i <= $this->to; $i = strtotime('+1 day', $i)) {
                        if (!$curve->getPoint($i)) {
                            $curve->setPoint($i, 0);
                        }
                    }
                }
            }
        }

        $jsCurves = [];
        foreach ($this->curves as $curve) {
            $jsCurves[] = $curve->getValues($this->timeMode);
        }

        if (count($jsCurves)) {
            return '
			<div id="flot' . self::$poolId . '" style="width:' . $this->width . 'px;height:' . $this->height . 'px"></div>
			<script type="text/javascript">
				$(function () {
					$.plot($(\'#flot' . self::$poolId . '\'), [' . implode(',', $jsCurves) . '], {' . $options . '});
				});
			</script>';
        } else {
            return ErrorFacade::Display(PS_ERROR_UNDEFINED, 'No values for this chart.');
        }
    }
}
