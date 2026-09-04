<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @param string $hexColor
     *
     * @return float|int
     */
    private function calculate($hexColor)
    {
        $hexColor = (string) $hexColor;

        if (strtolower($hexColor) === 'transparent') {
            return self::BRIGHT_COLOR_MIN;
        }

        $hexColor = str_replace('#', '', $hexColor);

        if (strlen($hexColor) === 3) {
            $hexColor = $hexColor[0] . $hexColor[0] . $hexColor[1] . $hexColor[1] . $hexColor[2] . $hexColor[2];
        }

        // Anything that is not a six digit hexadecimal value - a CSS colour name, an rgb() call, an
        // empty string - has no brightness to derive. Without this guard hexdec() raises a
        // deprecation for every invalid character and then computes from whichever letters happen
        // to be hex digits, which is why 'red' reads as bright while 'orange' and 'LimeGreen' do
        // not. Treat an unusable value the same way as 'transparent'.
        if (!preg_match('/^[0-9a-fA-F]{6}$/', $hexColor)) {
            return self::BRIGHT_COLOR_MIN;
        }

        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));

        return (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    }
}
