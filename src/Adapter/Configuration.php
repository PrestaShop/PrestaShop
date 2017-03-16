<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter;

use PrestaShop\PrestaShop\Core\Foundation\Exception;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Shop;

class Configuration implements ConfigurationInterface
{
    private $shop;

    /**
     * Returns constant defined by given $key if exists or check directly into PrestaShop
     * \Configuration
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        if (defined($key)) {
            return constant($key);
        } else {
            return \Configuration::get($key);
        }
    }

    /**
     * Set configuration value
     * @param $key
     * @param $value
     * @return $this
     * @throws \Exception
     */
    public function set($key, $value)
    {
        // By default, set a piece of configuration for all available shops and shop groups
        $shopGroupId = 0;
        $shopId = 0;

        if ($this->shop instanceof Shop) {
            $shopGroupId = $this->shop->id_shop_group;
            $shopId = $this->shop->id;
        }

        $success = \Configuration::updateValue(
            $key,
            $value,
            false,
            $shopGroupId,
            $shopId
        );

        if (!$success) {
            throw new \Exception("Could not update configuration");
        }

        return $this;
    }

    /**
     * Return if Feature feature is active or not
     * @return bool
     */
    public function featureIsActive()
    {
        return \FeatureCore::isFeatureActive();
    }

    /**
     * Return if Combination feature is active or not
     * @return bool
     */
    public function combinationIsActive()
    {
        return  \CombinationCore::isFeatureActive();
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
