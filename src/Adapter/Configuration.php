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
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Exception\NotImplementedException;
use Shop;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Adapter of Configuration ObjectModel.
 */
class Configuration extends ParameterBag implements ShopConfigurationInterface
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
     * @param ShopConstraint|null $shopConstraint
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
        $isStrict = $this->isStrict($shopConstraint);

        //If configuration has never been accessed it is still empty and hasKey/isLangKey will always return false
        if (!ConfigurationLegacy::configurationIsLoaded()) {
            ConfigurationLegacy::loadConfiguration();
        }

        // if the key is multi lang related, we return an array with the value per language.
        if (ConfigurationLegacy::isLangKey($key)) {
            return $this->getLocalized($key, $shopId, $shopGroupId);
        }

        // Since hasKey doesn't check manage the fallback shop > shop group > global, we handle it manually
        $hasKey = ConfigurationLegacy::hasKey($key, null, null, $shopId);
        if ($hasKey || ($isStrict && null !== $shopConstraint->getShopId())) {
            return $hasKey ? ConfigurationLegacy::get($key, null, null, $shopId) : null;
        }

        $hasKey = ConfigurationLegacy::hasKey($key, null, $shopGroupId);
        if ($hasKey || ($isStrict && null !== $shopConstraint->getShopGroupId())) {
            return $hasKey ? ConfigurationLegacy::get($key, null, $shopGroupId) : null;
        }

        $hasKey = ConfigurationLegacy::hasKey($key);
        if ($hasKey) {
            return ConfigurationLegacy::get($key);
        }

        return $default;
    }

    /**
     * Set configuration value.
     *
     * @param string $key
     * @param mixed $value
     * @param ShopConstraint|null $shopConstraint
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
     * @param string $key
     * @param ShopConstraint|null $shopConstraint
     *
     * @return bool
     */
    public function has($key, ShopConstraint $shopConstraint = null)
    {
        if (null === $shopConstraint) {
            $shopConstraint = $this->buildShopConstraintFromContext();
        }

        $shopId = $this->getShopId($shopConstraint);
        $shopGroupId = $this->getShopGroupId($shopConstraint);
        $isStrict = $this->isStrict($shopConstraint);

        if (ConfigurationLegacy::isLangKey($key)) {
            return $this->hasMultilang($key, $shopId, $shopGroupId, $isStrict);
        }

        $hasKey = ConfigurationLegacy::hasKey($key, null, $shopGroupId, $shopId);
        if ($hasKey || $isStrict) {
            return $hasKey;
        }

        $hasKey = ConfigurationLegacy::hasKey($key, null, $shopGroupId);
        if ($hasKey) {
            return $hasKey;
        }

        return ConfigurationLegacy::hasKey($key);
    }

    /**
     * Same as 'has' method, but for multilang configuration keys
     *
     * @param string $key
     * @param int|null $shopId
     * @param int|null $shopGroupId
     * @param bool $isStrict
     *
     * @return bool
     */
    private function hasMultilang(string $key, ?int $shopId, ?int $shopGroupId, bool $isStrict): bool
    {
        $langIds = Language::getIDs(false, $shopId ?: false);

        // check that we have a key for at least one of the used languages for given constraints
        foreach ($langIds as $langId) {
            if (ConfigurationLegacy::hasKey($key, $langId, $shopGroupId, $shopId)) {
                return true;
            }

            // If strict mode is enable, only rely on the first check
            if ($isStrict) {
                continue;
            }

            if (ConfigurationLegacy::hasKey($key, $langId, $shopGroupId) || ConfigurationLegacy::hasKey($key, $langId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Removes a configuration key.
     *
     * @param string $key
     *
     * @return self
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
     * @param string $key
     *
     * @return void
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
        return Combination::isFeatureActive();
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

    /**
     * @param ShopConstraint $shopConstraint
     *
     * @return int|null
     */
    private function getShopId(ShopConstraint $shopConstraint): ?int
    {
        return null !== $shopConstraint->getShopId()
            ? $shopConstraint->getShopId()->getValue()
            : null
        ;
    }

    /**
     * @param ShopConstraint $shopConstraint
     *
     * @return int|null
     */
    private function getShopGroupId(ShopConstraint $shopConstraint): ?int
    {
        if (null !== $shopConstraint->getShopGroupId()) {
            return $shopConstraint->getShopGroupId()->getValue();
        } elseif (null !== $shopConstraint->getShopId()) {
            return (int) Shop::getGroupFromShop((int) $shopConstraint->getShopId()->getValue(), true);
        }

        return null;
    }

    /**
     * @param ShopConstraint|null $shopConstraint
     *
     * @return bool
     */
    private function isStrict(?ShopConstraint $shopConstraint): bool
    {
        return null !== $shopConstraint ? $shopConstraint->isStrict() : false;
    }

    /**
     * @param string $key
     * @param ShopConstraint $shopConstraint
     */
    public function deleteFromContext(string $key, ShopConstraint $shopConstraint): void
    {
        $shopId = $shopConstraint->getShopId();
        $shopGroupId = $shopConstraint->getShopGroupId();

        ConfigurationLegacy::deleteFromContext(
            $key,
            !empty($shopGroupId) ? $shopGroupId->getValue() : null,
            !empty($shopId) ? $shopId->getValue() : null
        );
    }

    /**
     * @return ShopConstraint
     */
    private function buildShopConstraintFromContext(): ShopConstraint
    {
        @trigger_error(
            'Not specifying the optional ShopConstraint parameter is deprecated since version 1.7.8.0',
            E_USER_DEPRECATED
        );

        if (Shop::getContext() === Shop::CONTEXT_SHOP) {
            return ShopConstraint::shop((int) Shop::getContextShopID());
        } elseif (Shop::getContext() === Shop::CONTEXT_GROUP) {
            return ShopConstraint::shopGroup((int) Shop::getContextShopGroupID());
        }

        return ShopConstraint::allShops();
    }
}
