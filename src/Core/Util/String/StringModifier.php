<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Util\String;

use Transliterator;

/**
 * This class defines reusable methods for strings modifications.
 */
final class StringModifier implements StringModifierInterface
{
    /**
     * @var Transliterator|null
     */
    private $transliterator;

    /**
     * {@inheritdoc}
     */
    public function splitByCamelCase($string)
    {
        $regex = '/(?)(?<=[a-z])(?=[A-Z]) | (?<=[A-Z])(?=[A-Z][a-z])/x';

        $splitString = preg_split($regex, $string);

        return implode(' ', $splitString);
    }

    /**
     * {@inheritdoc}
     */
    public function cutEnd(string $string, int $expectedLength): string
    {
        $length = strlen($string);

        if ($length > $expectedLength) {
            // cut symbols difference from the end of the string
            $string = substr($string, 0, $expectedLength - $length);
        }

        return $string;
    }

    /**
     * Return a friendly url made from the provided string
     * If the mbstring library is available, the output is the same as the js function of the same name.
     *
     * @param string $string
     *
     * @return string
     */
    public function str2url(string $string, bool $allow_accented_chars): string
    {
        $return_str = trim($string);
        $return_str = mb_strtolower($return_str, 'UTF-8');

        if ($allow_accented_chars) {
            $return_str = preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]\-\p{L}]/u', '', $return_str);
        } else {
            $return_str = $this->replaceAccentedChars($return_str);
            $return_str = preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]\-]/', '', $return_str);
        }

        $return_str = preg_replace('/[\s\'\:\/\[\]\-]+/', ' ', $return_str);

        return str_replace([' ', '/'], '-', $return_str);
    }

    /**
     * Replace all accented chars by their equivalent non-accented chars.
     *
     * @param string $string
     *
     * @return string
     */
    public function replaceAccentedChars(string $string): string
    {
        if (null === $this->transliterator) {
            $this->transliterator = Transliterator::create('Any-Latin; Latin-ASCII');
        }

        return $this->transliterator->transliterate($string);
    }
}
