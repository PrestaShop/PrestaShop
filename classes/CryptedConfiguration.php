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

/**
 * Class CryptedConfiguration.
 */
class CryptedConfiguration extends Configuration
{
    /**
     * @var PhpEncryption Instance of PhpEncryption
     */
    private static $phpEncryption = null;

    /**
     * Get instance of PhpEncryption
     *
     * @return PhpEncryption
     */
    public static function getPhpEncryption()
    {
        if (self::$phpEncryption === null) {
            self::$phpEncryption = new PhpEncryption(_NEW_COOKIE_KEY_);
        }

        return self::$phpEncryption;
    }

    /**
     * Get a single configuration value (in one language only).
     *
     * @param string $key Key wanted
     * @param int $idLang Language ID
     *
     * @return string|bool Value
     */
    public static function get($key, $idLang = null, $idShopGroup = null, $idShop = null, $default = false)
    {
        $rawValue = parent::get($key, $idLang, $idShopGroup, $idShop, $default);
        if (empty($rawValue) === true || is_bool($rawValue)) {
            return $rawValue;
        }
        $value = self::getPhpEncryption()->decrypt($rawValue);

        return $value;
    }

    /**
     * Get global value.
     *
     * @param string $key Configuration key
     * @param int|null $idLang Language ID
     *
     * @return string
     */
    public static function getGlobalValue($key, $idLang = null)
    {
        return CryptedConfiguration::get($key, $idLang, 0, 0);
    }

    /**
     * Get a single configuration value for all shops.
     *
     * @param string $key Key wanted
     * @param int $idLang
     *
     * @return array Values for all shops
     */
    public static function getMultiShopValues($key, $idLang = null)
    {
        $shops = Shop::getShops(false, null, true);
        $resultsArray = [];
        foreach ($shops as $idShop) {
            $resultsArray[$idShop] = CryptedConfiguration::get($key, $idLang, null, $idShop);
        }

        return $resultsArray;
    }

    /**
     * Get several configuration values (in one language only).
     *
     * @throws PrestaShopException
     *
     * @param array $keys Keys wanted
     * @param int $idLang Language ID
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return array Values
     */
    public static function getMultiple($keys, $idLang = null, $idShopGroup = null, $idShop = null)
    {
        if (!is_array($keys)) {
            throw new PrestaShopException('keys var is not an array');
        }

        $idLang = (int) $idLang;
        if ($idShop === null) {
            $idShop = Shop::getContextShopID(true);
        }
        if ($idShopGroup === null) {
            $idShopGroup = Shop::getContextShopGroupID(true);
        }

        $results = [];
        foreach ($keys as $key) {
            $results[$key] = CryptedConfiguration::get($key, $idLang, $idShopGroup, $idShop);
        }

        return $results;
    }

    /**
     * Set TEMPORARY a single configuration value (in one language only).
     *
     * @param string $key Configuration key
     * @param mixed $values `$values` is an array if the configuration is multilingual, a single string else
     * @param int $idShopGroup
     * @param int $idShop
     */
    public static function set($key, $values, $idShopGroup = null, $idShop = null)
    {
        if (is_array($values) === true) {
            foreach ($values as $key => $value) {
                $values[$key] = self::getPhpEncryption()->encrypt($value);
            }
        } else {
            $values = self::getPhpEncryption()->encrypt($values);
        }

        parent::set($key, $values, $idShopGroup, $idShop);
    }

    /**
     * Update configuration key for global context only.
     *
     * @param string $key
     * @param mixed $values
     * @param bool $html
     *
     * @return bool
     */
    public static function updateGlobalValue($key, $values, $html = false)
    {
        return CryptedConfiguration::updateValue($key, $values, $html, 0, 0);
    }

    /**
     * Update configuration key and value into database (automatically insert if key does not exist).
     *
     * Values are inserted/updated directly using SQL, because using (Configuration) ObjectModel
     * may not insert values correctly (for example, HTML is escaped, when it should not be).
     *
     * @TODO Fix saving HTML values in Configuration model
     *
     * @param string $key Configuration key
     * @param mixed $values $values is an array if the configuration is multilingual, a single string else
     * @param bool $html Specify if html is authorized in value
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return bool Update result
     */
    public static function updateValue($key, $values, $html = false, $idShopGroup = null, $idShop = null)
    {
        if (is_array($values) === true) {
            foreach ($values as $key => $value) {
                if (empty($values) === false) {
                    $values[$key] = self::getPhpEncryption()->encrypt($value);
                }
            }
        } else {
            if (empty($values) === false) {
                $values = self::getPhpEncryption()->encrypt($values);
            }
        }
        $result = parent::updateValue($key, $values, $html = false, $idShopGroup = null, $idShop = null);

        return (bool) $result;
    }
}
