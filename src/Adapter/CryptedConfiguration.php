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

namespace PrestaShop\PrestaShop\Adapter;

use CryptedConfiguration as CryptedCryptedConfigurationLegacy;
use Configuration as CryptedConfigurationLegacy;
use PrestaShop\PrestaShop\Adapter\Configuration;
use Language;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Shop;

/**
 * Adapter of Configuration ObjectModel.
 */
class CryptedConfiguration extends Configuration implements ShopConfigurationInterface
{
    /**
     * Returns constant defined by given $key if exists or check directly into PrestaShop
     * \Configuration.
     *
     * @param string $key
     * @param mixed $default The default value if the parameter key does not exist
     * @param ShopConstraint|null $shopConstraint This parameter should always be given, if not, a ShopConstraint will
     *                                            be built based on the current shop context
     *
     * @return mixed
     */
    public function get($key, $default = null, ShopConstraint $shopConstraint = null)
    {
        if (null === $shopConstraint) {
            $shopConstraint = $this->buildShopConstraintFromContext();
        }

        if (defined($key)) {
            return constant($key);
        }

        $shopId = $this->getShopId($shopConstraint);
        $shopGroupId = $this->getShopGroupId($shopConstraint);

        //If configuration has never been accessed it is still empty and hasKey/isLangKey will always return false
        if (!CryptedCryptedConfigurationLegacy::configurationIsLoaded()) {
            CryptedCryptedConfigurationLegacy::loadConfiguration();
        }

        // if the key is multi lang related, we return an array with the value per language.
        if (CryptedCryptedConfigurationLegacy::isLangKey($key)) {
            return $this->getLocalized($key, $shopId, $shopGroupId);
        }

        if ($shopConstraint->isStrict()) {
            return $this->getStrictValue($key, $shopConstraint);
        }

        // Since hasKey doesn't manage the fallback shop > shop group > global, we handle it manually
        if (null !== $shopId && CryptedCryptedConfigurationLegacy::hasKey($key, null, null, $shopId)) {
            return CryptedCryptedConfigurationLegacy::get($key, null, null, $shopId);
        }

        if (null !== $shopGroupId && CryptedCryptedConfigurationLegacy::hasKey($key, null, $shopGroupId)) {
            return CryptedCryptedConfigurationLegacy::get($key, null, $shopGroupId);
        }

        if (CryptedCryptedConfigurationLegacy::hasKey($key)) {
            return CryptedCryptedConfigurationLegacy::get($key);
        }

        return $default;
    }

    /**
     * Set configuration value.
     *
     * @param string $key
     * @param mixed $value
     * @param ShopConstraint|null $shopConstraint If this parameter is not given, a ShopConstraint will
     *                                            be built based on the current shop context, except if $this->shop is set
     * @param array $options Options @deprecated Will be removed in next major
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function set($key, $value, ShopConstraint $shopConstraint = null, array $options = [])
    {
        if ($this->shop instanceof Shop && null === $shopConstraint) {
            $shopGroupId = $this->shop->id_shop_group;
            $shopId = $this->shop->id;
        } else {
            $shopConstraint = $shopConstraint ?: $this->buildShopConstraintFromContext();
            $shopId = $this->getShopId($shopConstraint);
            $shopGroupId = $this->getShopGroupId($shopConstraint);
        }

        $html = isset($options['html']) ? (bool) $options['html'] : false;

        $success = CryptedCryptedConfigurationLegacy::updateValue(
            $key,
            $value,
            $html,
            $shopGroupId ?: 0,
            $shopId ?: 0
        );

        if (!$success) {
            throw new \Exception('Could not update configuration');
        }

        return $this;
    }

    /**
     * Get localized configuration in all languages
     *
     * @param string $key
     * @param int|null $shopId
     * @param int|null $shopGroupId
     *
     * @return array Array of langId => localizedConfiguration
     */
    private function getLocalized($key, ?int $shopId = null, ?int $shopGroupId = null)
    {
        $configuration = [];

        foreach (Language::getIDs(false, $shopId ?: false) as $langId) {
            $configuration[$langId] = CryptedConfigurationLegacy::get($key, $langId, $shopGroupId, $shopId);
        }

        return $configuration;
    }

    /**
     * @param string $key
     * @param ShopConstraint $shopConstraint
     *
     * @return mixed
     */
    private function getStrictValue(string $key, ShopConstraint $shopConstraint)
    {
        if (null !== $shopConstraint->getShopId()) {
            $hasKey = CryptedConfigurationLegacy::hasKey($key, null, null, $shopConstraint->getShopId()->getValue());

            return $hasKey ? CryptedConfigurationLegacy::get($key, null, null, $shopConstraint->getShopId()->getValue()) : null;
        }

        if (null !== $shopConstraint->getShopGroupId()) {
            $hasKey = CryptedConfigurationLegacy::hasKey($key, null, $shopConstraint->getShopGroupId()->getValue());

            return $hasKey ? CryptedConfigurationLegacy::get($key, null, $shopConstraint->getShopGroupId()->getValue()) : null;
        }

        if (CryptedConfigurationLegacy::hasKey($key)) {
            return CryptedConfigurationLegacy::get($key);
        }

        return null;
    }
}
