<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Util\String;

/**
 * Defines reusable methods for strings modifications.
 *
 * @method str2url(string $string) will be added in 9.0
 * @method replaceAccentedChars(string $string) will be added in 9.0
 */
interface StringModifierInterface
{
    /**
     * @param string $string
     *
     * @return string
     */
    public function splitByCamelCase($string);

    /**
     * Cuts string end if it exceeds expected length
     *
     * @param string $string
     * @param int $expectedLength
     *
     * @return string
     */
    public function cutEnd(string $string, int $expectedLength): string;
}
