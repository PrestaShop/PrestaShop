<?php

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
