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

use Combination;
use Configuration as ConfigurationLegacy;
use Feature;
use Language;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShopBundle\Exception\NotImplementedException;
use Shop;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Adapter of Configuration ObjectModel.
 */
class Configuration extends ParameterBag implements ConfigurationInterface, ShopConfigurationInterface
{
    /**
     * @var Shop
     */
    private $shop;

    public function __construct(array $parameters = [])
    {
        // Do nothing
        if (!empty($parameters)) {
            throw new \LogicException('No parameter can be handled in constructor. Use method set() instead.');
        }
    }

    /**
     * @throws NotImplementedException
     */
    public function all()
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritdoc}
     */
    public function keys()
    {
        return array_keys($this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $parameters = [])
    {
        $this->add($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function add(array $parameters = [])
    {
        foreach ($parameters as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Returns constant defined by given $key if exists or check directly into PrestaShop
     * \Configuration.
     *
     * @param string $key
     * @param mixed $default The default value if the parameter key does not exist
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (defined($key)) {
            return constant($key);
        }

        return $this->doGet($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getForShop(string $key, ShopConstraint $shopConstraint)
    {
        $shopId = null !== $shopConstraint->getShopId() ? $shopConstraint->getShopId()->getValue() : null;
        $shopGroupId = null !== $shopConstraint->getShopGroupId() ? $shopConstraint->getShopGroupId()->getValue() : null;

        return $this->doGet($key, null, $shopId, $shopGroupId);
    }

    /**
     * @param $key
     * @param null $default
     * @param int|null $shopId
     * @param int|null $shopGroupId
     *
     * @return array|false|mixed|string|null
     */
    private function doGet($key, $default = null, ?int $shopId = null, ?int $shopGroupId = null)
    {
        //If configuration has never been accessed it is still empty and hasKey/isLangKey will always return false
        if (!ConfigurationLegacy::configurationIsLoaded()) {
            ConfigurationLegacy::loadConfiguration();
        }

        // if the key is multi lang related, we return an array with the value per language.
        if (ConfigurationLegacy::isLangKey($key)) {
            return $this->getLocalized($key, $shopId, $shopGroupId);
        }

        if (ConfigurationLegacy::hasKey($key, null, $shopGroupId, $shopId)) {
            return ConfigurationLegacy::get($key, null, $shopGroupId, $shopId);
        }

        return $default;
    }

    /**
     * Set configuration value.
     *
     * @param string $key
     * @param mixed $value
     * @param array $options Options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function set($key, $value, array $options = [])
    {
        // By default, set a piece of configuration for all available shops and shop groups
        $shopGroupId = null;
        $shopId = null;

        if ($this->shop instanceof Shop) {
            $shopGroupId = $this->shop->id_shop_group;
            $shopId = $this->shop->id;
        }

        $html = isset($options['html']) ? (bool) $options['html'] : false;

        $success = ConfigurationLegacy::updateValue(
            $key,
            $value,
            $html,
            $shopGroupId,
            $shopId
        );

        if (!$success) {
            throw new \Exception('Could not update configuration');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setForShop(string $key, $value, ShopConstraint $shopConstraint): void
    {
        $shopId = null !== $shopConstraint->getShopId() ? $shopConstraint->getShopId()->getValue() : null;
        $shopGroupId = null !== $shopConstraint->getShopGroupId() ? $shopConstraint->getShopGroupId()->getValue() : null;

        $success = ConfigurationLegacy::updateValue(
            $key,
            $value,
            false,
            $shopGroupId,
            $shopId
        );

        if (!$success) {
            throw new \Exception('Could not update configuration');
        }
    }


    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return ConfigurationLegacy::hasKey($key);
    }

    /**
     * {@inheritdoc}
     */
    public function hasForShop(string $key, ShopConstraint $shopConstraint): bool
    {
        $shopId = null !== $shopConstraint->getShopId() ? $shopConstraint->getShopId()->getValue() : null;
        $shopGroupId = null !== $shopConstraint->getShopGroupId() ? $shopConstraint->getShopGroupId()->getValue() : null;

        return ConfigurationLegacy::hasKey($key, null, $shopGroupId, $shopId);
    }


    /**
     * Removes a configuration key.
     *
     * @param type $key
     *
     * @return type
     */
    public function remove($key)
    {
        $success = \Configuration::deleteByName(
            $key
        );

        if (!$success) {
            throw new \Exception('Could not update configuration');
        }

        return $this;
    }

    /**
     * Unset configuration value.
     *
     * @param $key
     *
     * @return $this
     *
     * @throws \Exception
     *
     * @deprecated since version 1.7.4.0
     */
    public function delete($key)
    {
        $this->remove($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->all());
    }

    /**
     * Return if Feature feature is active or not.
     *
     * @return bool
     */
    public function featureIsActive()
    {
        return Feature::isFeatureActive();
    }

    /**
     * Return if Combination feature is active or not.
     *
     * @return bool
     */
    public function combinationIsActive()
    {
        return  Combination::isFeatureActive();
    }

    /**
     * Restrict updates of a piece of configuration to a single shop.
     *
     * @param Shop $shop
     */
    public function restrictUpdatesTo(Shop $shop)
    {
        $this->shop = $shop;
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
            $configuration[$langId] = ConfigurationLegacy::get($key, $langId, $shopGroupId, $shopId);
        }

        return $configuration;
    }
}
