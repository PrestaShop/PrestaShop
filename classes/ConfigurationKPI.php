<?php
/**
 * 2007-2018 PrestaShop.
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

/**
 * Class ConfigurationKPICore.
 */
class ConfigurationKPICore extends Configuration
{
    public static $definition_backup;

    /**
     * Set KPI definition.
     */
    public static function setKpiDefinition()
    {
        ConfigurationKPI::$definition_backup = Configuration::$definition;
        Configuration::$definition['table'] = 'configuration_kpi';
        Configuration::$definition['primary'] = 'id_configuration_kpi';
    }

    /**
     * Unset KPI definition.
     */
    public static function unsetKpiDefinition()
    {
        Configuration::$definition = ConfigurationKPI::$definition_backup;
    }

    /**
     * Get ID by name.
     *
     * @param string $key Configuration key
     * @param int|null $idShopGroup ShopGroup ID
     * @param int|null $idShop Shop ID
     *
     * @return int ConfigurationKPI ID
     */
    public static function getIdByName($key, $idShopGroup = null, $idShop = null)
    {
        ConfigurationKPI::setKpiDefinition();
        $configurationKpi = parent::getIdByName($key, $idShopGroup, $idShop);
        ConfigurationKPI::unsetKpiDefinition();

        return $configurationKpi;
    }

    /**
     * Load configuration.
     */
    public static function loadConfiguration()
    {
        ConfigurationKPI::setKpiDefinition();
        parent::loadConfiguration();
        ConfigurationKPI::unsetKpiDefinition();
    }

    /**
     * Get value.
     *
     * @param string $key Configuration key
     * @param null $idLang Language ID
     * @param null $idShopGroup ShopGroup ID
     * @param null $idShop Shop ID
     * @param bool $default Default value
     *
     * @return string
     */
    public static function get($key, $idLang = null, $idShopGroup = null, $idShop = null, $default = false)
    {
        ConfigurationKPI::setKpiDefinition();
        $value = parent::get($key, $idLang, $idShopGroup, $idShop, $default);
        ConfigurationKPI::unsetKpiDefinition();

        return $value;
    }

    /**
     * Get global vlaue.
     *
     * @param string $key Configuration key
     * @param int|null $idLang Language ID
     *
     * @return string Global value
     */
    public static function getGlobalValue($key, $idLang = null)
    {
        ConfigurationKPI::setKpiDefinition();
        $globalValue = parent::getGlobalValue($key, $idLang);
        ConfigurationKPI::unsetKpiDefinition();

        return $globalValue;
    }

    /**
     * Get value independent from language.
     *
     * @param string $key Configuration key
     * @param null $idShopGroup ShopGroup ID
     * @param null $idShop Shop ID
     *
     * @return array Values for key for all available languages
     */
    public static function getInt($key, $idShopGroup = null, $idShop = null)
    {
        ConfigurationKPI::setKpiDefinition();
        $values = parent::getInt($key, $idShopGroup, $idShop);
        ConfigurationKPI::unsetKpiDefinition();

        return $values;
    }

    /**
     * Get multiple keys.
     *
     * @param array $keys Configuation keys
     * @param int|null $idLang Language ID
     * @param int|null $idShopGroup ShopGroup ID
     * @param int|null $idShop Shop ID
     *
     * @return array Configuration values
     */
    public static function getMultiple($keys, $idLang = null, $idShopGroup = null, $idShop = null)
    {
        ConfigurationKPI::setKpiDefinition();
        $configurationValues = parent::getMultiple($keys, $idLang, $idShopGroup, $idShop);
        ConfigurationKPI::unsetKpiDefinition();

        return $configurationValues;
    }

    /**
     * Has key.
     *
     * @param string $key
     * @param int|null $idLang Language ID
     * @param int|null $idShopGroup ShopGroup ID
     * @param int|null $idShop Shop ID
     *
     * @return bool
     */
    public static function hasKey($key, $idLang = null, $idShopGroup = null, $idShop = null)
    {
        ConfigurationKPI::setKpiDefinition();
        $hasKey = parent::hasKey($key, $idLang, $idShopGroup, $idShop);
        ConfigurationKPI::unsetKpiDefinition();

        return $hasKey;
    }

    /**
     * Set key.
     *
     * @param string $key Configuration key
     * @param mixed $values Values
     * @param null $idShopGroup ShopGroup ID
     * @param null $idShop Shop ID
     */
    public static function set($key, $values, $idShopGroup = null, $idShop = null)
    {
        ConfigurationKPI::setKpiDefinition();
        parent::set($key, $values, $idShopGroup, $idShop);
        ConfigurationKPI::unsetKpiDefinition();
    }

    /**
     * Update global value.
     *
     * @param string $key Configuration key
     * @param mixed $values Values
     * @param bool $html Do the values contain HTML?
     *
     * @return bool Indicates whether the key was successfully updated
     */
    public static function updateGlobalValue($key, $values, $html = false)
    {
        ConfigurationKPI::setKpiDefinition();
        $updateSuccess = parent::updateGlobalValue($key, $values, $html);
        ConfigurationKPI::unsetKpiDefinition();

        return $updateSuccess;
    }

    /**
     * Update value.
     *
     * @param string $key Configuration key
     * @param mixed $values Values
     * @param bool $html Do the values contain HTML?
     * @param null $idShopGroup ShopGroup ID
     * @param null $idShop Shop ID
     *
     * @return bool Indicates whether the key was successfully updated
     */
    public static function updateValue($key, $values, $html = false, $idShopGroup = null, $idShop = null)
    {
        ConfigurationKPI::setKpiDefinition();
        $updateSuccess = parent::updateValue($key, $values, $html, $idShopGroup, $idShop);
        ConfigurationKPI::unsetKpiDefinition();

        return $updateSuccess;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public static function deleteByName($key)
    {
        ConfigurationKPI::setKpiDefinition();
        $deleteSuccess = parent::deleteByName($key);
        ConfigurationKPI::unsetKpiDefinition();

        return $deleteSuccess;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public static function deleteFromContext($key)
    {
        ConfigurationKPI::setKpiDefinition();
        $deleteSuccess = parent::deleteFromContext($key);
        ConfigurationKPI::unsetKpiDefinition();

        return $deleteSuccess;
    }

    /**
     * @param string $key
     * @param int $idLang
     * @param int $context
     *
     * @return bool
     */
    public static function hasContext($key, $idLang, $context)
    {
        ConfigurationKPI::setKpiDefinition();
        $hasContext = parent::hasContext($key, $idLang, $context);
        ConfigurationKPI::unsetKpiDefinition();

        return $hasContext;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public static function isOverridenByCurrentContext($key)
    {
        ConfigurationKPI::setKpiDefinition();
        $isOverriden = parent::isOverridenByCurrentContext($key);
        ConfigurationKPI::unsetKpiDefinition();

        return $isOverriden;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public static function isLangKey($key)
    {
        ConfigurationKPI::setKpiDefinition();
        $isLangKey = parent::isLangKey($key);
        ConfigurationKPI::unsetKpiDefinition();

        return $isLangKey;
    }

    /**
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return string
     */
    protected static function sqlRestriction($idShopGroup, $idShop)
    {
        ConfigurationKPI::setKpiDefinition();
        $sqlRestriction = parent::sqlRestriction($idShopGroup, $idShop);
        ConfigurationKPI::unsetKpiDefinition();

        return $sqlRestriction;
    }
}
