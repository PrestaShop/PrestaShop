<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Util;

/**
 * Calculates color brightness
 */
final class ColorBrightnessCalculator
{
    /**
     * Minimum color value after which it's considered bright
     */
    public const BRIGHT_COLOR_MIN = 130;

    /**
     * @param string $hexColor
     *
     * @return bool
     */
    public function isBright($hexColor)
    {
        return $this->calculate($hexColor) >= self::BRIGHT_COLOR_MIN;
    }

    /**
     * @param $hexColor
     *
     * @return float|int
     */
    private function calculate($hexColor)
    {
        if (strtolower($hexColor) === 'transparent') {
            return self::BRIGHT_COLOR_MIN;
        }

        $hexColor = str_replace('#', '', $hexColor);

        if (strlen($hexColor) === 3) {
            $hexColor .= $hexColor;
        }

        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));

        return (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    }
}
