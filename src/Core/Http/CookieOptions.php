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

namespace PrestaShop\PrestaShop\Core\Http;

final class CookieOptions
{
    /**
     * If you set cookie lifetime value too high there can be multiple problems.
     * Hours are converted to seconds, so int might be turned to float if it's way to high.
     * Cookie classes crash if lifetime goes beyond year 9999, there are probably multiple other things.
     * So we need to set some sort of max value. 100 years seems like a lifetime beyond reasonable use.
     */
    public const MAX_COOKIE_VALUE = 876000;

    public const SAMESITE_NONE = 'None';
    public const SAMESITE_LAX = 'Lax';
    public const SAMESITE_STRICT = 'Strict';

    public const SAMESITE_AVAILABLE_VALUES = [
        self::SAMESITE_NONE => self::SAMESITE_NONE,
        self::SAMESITE_LAX => self::SAMESITE_LAX,
        self::SAMESITE_STRICT => self::SAMESITE_STRICT,
    ];

    // This class should not be instanciated
    private function __construct()
    {
    }
}
