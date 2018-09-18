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

namespace PrestaShop\PrestaShop\Adapter\Feature;

use Shop;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShopBundle\Entity\Repository\ShopRepository;

/**
 * This class manages MultiShop feature.
 */
class MultiShopFeature implements FeatureInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ShopRepository
     */
    private $shopRepository;

    public function __construct(Configuration $configuration, ShopRepository $shopRepository)
    {
        $this->configuration = $configuration;
        $this->shopRepository = $shopRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isUsed()
    {
        return $this->isActive() && $this->shopRepository->haveMultipleShops();
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return Shop::isFeatureActive();
    }

    /**
     * {@inheritdoc}
     */
    public function enable()
    {
        $this->configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', true);
    }

    /**
     * {@inheritdoc}
     */
    public function disable()
    {
        $this->configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', false);
    }

    /**
     * {@inheritdoc}
     */
    public function update($status)
    {
        true === $status ? $this->enable() : $this->disable();
    }
}
