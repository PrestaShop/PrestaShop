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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
