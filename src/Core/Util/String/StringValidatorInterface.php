<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Util\String;

/**
 * Defines reusable methods for checking strings under certain conditions.
 */
interface StringValidatorInterface
{
    /**
     * @param string $string
     * @param string $prefix
     *
     * @return bool
     */
    public function startsWith($string, $prefix);

    /**
     * @param string $string
     * @param string $suffix
     *
     * @return bool
     */
    public function endsWith($string, $suffix);

    /**
     * @param string $string
     * @param string $prefix
     * @param string $suffix
     *
     * @return bool
     */
    public function startsWithAndEndsWith($string, $prefix, $suffix);

    /**
     * @param string $string
     *
     * @return bool
     */
    public function doesContainsWhiteSpaces($string);
}
