<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Util;

/**
 * Utility class to convert arabic/persian digits to latin digits
 */
class ArabicToLatinDigitConverter
{
    public const ARABIC = 1;

    public const PERSIAN = 2;

    private const TRANSLATION_TABLE = [
        // arabic numbers
        '٠' => '0',
        '١' => '1',
        '٢' => '2',
        '٣' => '3',
        '٤' => '4',
        '٥' => '5',
        '٦' => '6',
        '٧' => '7',
        '٨' => '8',
        '٩' => '9',
        // persian numbers (NOT the same UTF codes!)
        '۰' => '0',
        '۱' => '1',
        '۲' => '2',
        '۳' => '3',
        '۴' => '4',
        '۵' => '5',
        '۶' => '6',
        '۷' => '7',
        '۸' => '8',
        '۹' => '9',
    ];

    /**
     * Convert from arabic/persian digits to latin digits
     *
     * @param string $str
     *
     * @return string
     */
    public function convert(string $str): string
    {
        return strtr($str, self::TRANSLATION_TABLE);
    }

    /**
     * Convert from latin digits to arabic or persian digits
     *
     * @param string $str
     * @param int $lang
     *
     * @return string
     */
    public function reverseConvert(string $str, int $lang = self::ARABIC): string
    {
        $table = array_slice(self::TRANSLATION_TABLE, $lang === self::ARABIC ? 0 : 10, 10, true);

        return strtr($str, array_flip($table));
    }
}
