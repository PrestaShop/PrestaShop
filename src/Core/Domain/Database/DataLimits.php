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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Database;

/**
 * This class holds maximmum character count for database TINYTEXT, TEXT, MEDIUMTEXT and LONGTEXT fields.
 * Why? These fields are limited by byte count, not character count. And in UTF-8 encoding, every character,
 * can have different byte length, 1 to 4 bytes per character.
 *
 * These values should be used as limits in forms and other places to safely store the data provided.
 * It's recommended to use 4 byte lengths to be 100% sure that the data will be properly saved.
 */
class DataLimits
{
    // 1byte UTF8 character limit
    public const LIMIT_TINYTEXT_UTF8_1BYTE = 255;
    public const LIMIT_TEXT_UTF8_1BYTE = 65535;
    public const LIMIT_MEDIUMTEXT_UTF8_1BYTE = 16777215;
    public const LIMIT_LONGTEXT_UTF8_1BYTE = 4294967295;

    // 2byte UTF8 character limit
    public const LIMIT_TINYTEXT_UTF8_2BYTE = 127;
    public const LIMIT_TEXT_UTF8_2BYTE = 32767;
    public const LIMIT_MEDIUMTEXT_UTF8_2BYTE = 8388607;
    public const LIMIT_LONGTEXT_UTF8_2BYTE = 2147483647;

    // 3byte UTF8 character limit
    public const LIMIT_TINYTEXT_UTF8_3BYTE = 85;
    public const LIMIT_TEXT_UTF8_3BYTE = 21845;
    public const LIMIT_MEDIUMTEXT_UTF8_3BYTE = 5592405;
    public const LIMIT_LONGTEXT_UTF8_3BYTE = 1431655765;
  
    // 4byte UTF8 character limit
    public const LIMIT_TINYTEXT_UTF8_4BYTE = 63;
    public const LIMIT_TEXT_UTF8_4BYTE = 16383;
    public const LIMIT_MEDIUMTEXT_UTF8_4BYTE = 4194303;
    public const LIMIT_LONGTEXT_UTF8_4BYTE = 1073741823;

    // This class shouldn't be instantiated as its purpose is to hold some setting values
    private function __construct()
    {
    }
}
