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
namespace PrestaShop\PrestaShop\Adapter;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopBundle\Exception\NotImplementedException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Shop;
use Combination;
use Feature;
use Configuration as ConfigurationLegacy;

/**
 * Adapter of Configuration ObjectModel.
 */
class Configuration extends ParameterBag implements ConfigurationInterface
{
    /**
     * @var Shop
     */
    private $shop;

    public function __construct(array $parameters = array())
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
    public function replace(array $parameters = array())
    {
        $this->add($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function add(array $parameters = array())
    {
        foreach ($parameters as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Returns constant defined by given $key if exists or check directly into PrestaShop
     * \Configuration
     *
     * @param string $key
     * @param mixed $default The default value if the parameter key does not exist
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (defined($key)) {
            return constant($key);
        }

        // if the key is multi lang related, we return an array with the value per language.
        // getInt() meaning probably getInternational()
        if (ConfigurationLegacy::isLangKey($key)) {
            return ConfigurationLegacy::getInt($key);
        }

        if (ConfigurationLegacy::hasKey($key)) {
            return ConfigurationLegacy::get($key);
        }

        return $default;
    }

    /**
     * Set configuration value
     *
     * @param string $key
     * @param mixed  $value
     * @param array  $options Options
     *
     * @return $this
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
            throw new \Exception("Could not update configuration");
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return ConfigurationLegacy::hasKey($key);
    }

    /**
     * Removes a configuration key.
     *
     * @param type $key
     * @return type
     */
    public function remove($key)
    {
        $success = \Configuration::deleteByName(
            $key
        );

        if (!$success) {
            throw new \Exception("Could not update configuration");
        }

        return $this;
    }

    /**
     * Unset configuration value
     * @param $key
     * @return $this
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
     * Return if Feature feature is active or not
     * @return bool
     */
    public function featureIsActive()
    {
        return Feature::isFeatureActive();
    }

    /**
     * Return if Combination feature is active or not
     * @return bool
     */
    public function combinationIsActive()
    {
        return  Combination::isFeatureActive();
    }

    /**
     * Restrict updates of a piece of configuration to a single shop.
     * @param Shop $shop
     */
    public function restrictUpdatesTo(Shop $shop)
    {
        $this->shop = $shop;
    }
}
