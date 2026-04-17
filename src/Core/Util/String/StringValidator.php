<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Util\String;

/**
 * This class defines reusable methods for checking strings under certain conditions.
 */
final class StringValidator implements StringValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function startsWith($string, $prefix)
    {
        return str_starts_with($string, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    public function endsWith($string, $suffix)
    {
        $length = strlen($suffix);

        if (0 === $length) {
            return false;
        }

        return substr($string, -$length) === $suffix;
    }

    /**
     * {@inheritdoc}
     */
    public function startsWithAndEndsWith($string, $prefix, $suffix)
    {
        return $this->startsWith($string, $prefix) && $this->endsWith($string, $suffix);
    }

    /**
     * {@inheritdoc}
     */
    public function doesContainsWhiteSpaces($string)
    {
        return preg_match('/\s/', $string);
    }
}
