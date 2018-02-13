<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Cldr;

/**
 * Class Localize
 * @package PrestaShop\PrestaShop\Core\Cldr
 */
class Localize
{
    const DEFAULT_LOCALE = 'en';

    protected static $filters = array(
        'language' => array('filter' => 'strtolower'),
        'script' => array('filter' => array('strtolower', 'ucfirst')),
        'territory' => array('filter' => 'strtoupper'),
        'variant' => array('filter' => 'strtoupper')
    );

    private static $browserLocales;
    private static $environmentLocale;
    private static $locale;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return self::toString();
    }

    /**
     * Get the Browser locales (checking the HTTP header 'accept language')
     *
     * @return array The accepted locales
     */
    public static function getBrowserLocales()
    {
        if (self::$browserLocales !== null) {
            return self::$browserLocales;
        }

        $regex  = '(?P<locale>[\w\-]+)+(?:;q=(?P<quality>[0-9]+\.[0-9]+))?';
        $result = array();

        $httpLanguages = getenv('HTTP_ACCEPT_LANGUAGE');

        if (empty($httpLanguages)) {
            if (array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
                $httpLanguages = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            } else {
                return $result;
            }
        }

        foreach (explode(',', $httpLanguages) as $language) {
            if (preg_match("/{$regex}/", $language, $matches)) {
                $quality = isset($matches['quality']) ? $matches['quality'] : 1;
                $result[self::canonicalize($matches['locale'])] = $quality;
            }
        }

        arsort($result);
        $result = array_keys($result);
        self::$browserLocales = $result;
        return $result;
    }

    public static function getEnvironmentLocale()
    {
        if (self::$environmentLocale !== null) {
            return self::$environmentLocale;
        }

        $regex = '(?P<locale>[\w\_]+)(\.|@|$)+';
        $result = array();

        $value = setlocale(LC_ALL, 0);

        if ($value != 'C' && $value != 'POSIX' && preg_match("/{$regex}/", $value, $matches)) {
            $result = (array) $matches['locale'];

        // TODO: Add region handle
        }

        self::$environmentLocale = $result;
        return $result;
    }

    /**
     * Parse the language part of a given locale string
     *
     * @param null|string $locale The local to parse
     * @return string The language parsed from the $locale input
     */
    public static function getLanguage($locale = null)
    {
        if (!isset($locale)) {
            $locale = self::getLocale();
        }

        $locale = explode('_', $locale);
        return $locale[0];
    }

    /**
     * Sets a locale value as a singleton
     *
     * @param string $value The local to set
     */
    public static function setLocale($value)
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Invalid type for setLocale function');
        }

        self::$locale = self::canonicalize($value);
    }

    /**
     * Gets the locale value (singleton)
     *
     * @return string The locale singleton
     */
    public static function getLocale()
    {
        if (!isset(self::$locale)) {
            self::$locale = self::getPreferedLocale();
        }

        return self::$locale;
    }

    /**
     * Gets the best locale value, looking after the input, the browser and the environment locales.
     *
     * @param null|string|Localize $locale
     * @return string The best fitting locale
     */
    public static function getPreferedLocale($locale = null)
    {
        if ($locale instanceof self) {
            $locale = $locale->toString();
        }

        if ($locale === 'browser') {
            $locale = self::getBrowserLocales();
        }

        if ($locale === 'environment') {
            $locale = self::getEnvironmentLocale();
        }

        if (($locale === 'auto') || ($locale === null)) {
            $locale = self::getBrowserLocales();
            $locale += self::getEnvironmentLocale();
        }

        if (is_array($locale) === true) {
            reset($locale);
            $locale = current($locale);
        }

        if ($locale === null || trim($locale) == '') {
            $locale = self::DEFAULT_LOCALE;
        }

        return (string)self::canonicalize($locale);
    }

    /**
     * Parse the region part of the given locale string.
     *
     * @param null|string $locale The locale value to parse
     * @return string The region parsed from the input
     */
    public static function getRegion($locale = null)
    {
        if (!isset($locale)) {
            $locale = self::getLocale();
        }

        $locale = explode('_', strtoupper($locale));

        if (isset($locale[1]) === true) {
            return $locale[1];
        }

        return $locale[0];
    }

    /**
     * Cast the current Localize instance into a string.
     *
     * @return string The locale of the current Localize instance, in a string format.
     */
    public static function toString()
    {
        return (string)self::getLocale();
    }

    /**
     * Parse and fix the given locale to ensure format.
     *
     * @param $locale The locale to fix.
     * @return array|null|string The fixed locale format.
     */
    private static function canonicalize($locale)
    {
        if (empty($locale) || $locale == '') {
            return null;
        }

        $regex = '(?P<language>[a-z]{2,3})(?:[_-](?P<script>[a-z]{4}))?(?:[_-](?P<territory>[a-z]{2}))?(?:[_-](?P<variant>[a-z]{5,}))?';

        if (!preg_match("/^{$regex}$/i", $locale, $matches)) {
            throw new \InvalidArgumentException('Locale "'.$locale.'" could not be parsed');
        }

        $tags = array_filter(array_intersect_key($matches, static::$filters));

        foreach ($tags as $name => &$tag) {
            foreach ((array)static::$filters[$name]['filter'] as $filter) {
                $tag = $filter($tag);
            }
        }

        $result = array();

        foreach (static::$filters as $name => $value) {
            if (isset($tags[$name])) {
                $result[] = $tags[$name];
            }
        }

        return $result ? implode('_', $result) : $result;
    }
}
