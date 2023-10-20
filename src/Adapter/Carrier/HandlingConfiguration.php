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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Carrier;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class HandlingConfiguration is responsible for saving and loading Handling options configuration.
 */
class HandlingConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = ['shipping_handling_charges', 'free_shipping_price', 'free_shipping_weight'];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'shipping_handling_charges' => (float) $this->configuration->get('PS_SHIPPING_HANDLING', null, $shopConstraint),
            'free_shipping_price' => (float) $this->configuration->get('PS_SHIPPING_FREE_PRICE', null, $shopConstraint),
            'free_shipping_weight' => (float) $this->configuration->get('PS_SHIPPING_FREE_WEIGHT', null, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();
            $this->updateConfigurationValue('PS_SHIPPING_HANDLING', 'shipping_handling_charges', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_SHIPPING_FREE_PRICE', 'free_shipping_price', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_SHIPPING_FREE_WEIGHT', 'free_shipping_weight', $configuration, $shopConstraint);
        }

        return [];
    }

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        $resolver = (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('shipping_handling_charges', 'float')
            ->setAllowedTypes('free_shipping_price', 'float')
            ->setAllowedTypes('free_shipping_weight', 'float');

        return $resolver;
    }
}
