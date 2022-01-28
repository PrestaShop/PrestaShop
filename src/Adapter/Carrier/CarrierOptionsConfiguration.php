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

namespace PrestaShop\PrestaShop\Adapter\Carrier;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CarrierOptionsConfiguration is responsible for saving and loading Carrier Options configuration.
 */
class CarrierOptionsConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = ['default_carrier', 'carrier_default_order_by', 'carrier_default_order_way'];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'default_carrier' => (int) $this->configuration->get('PS_CARRIER_DEFAULT', null, $shopConstraint),
            'carrier_default_order_by' => (int) $this->configuration->get('PS_CARRIER_DEFAULT_SORT', null, $shopConstraint),
            'carrier_default_order_way' => (int) $this->configuration->get('PS_CARRIER_DEFAULT_ORDER', null, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();
            $this->updateConfigurationValue('PS_CARRIER_DEFAULT', 'default_carrier', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_CARRIER_DEFAULT_SORT', 'carrier_default_order_by', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_CARRIER_DEFAULT_ORDER', 'carrier_default_order_way', $configuration, $shopConstraint);
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
            ->setAllowedTypes('default_carrier', 'int')
            ->setAllowedTypes('carrier_default_order_by', 'int')
            ->setAllowedTypes('carrier_default_order_way', 'int');

        return $resolver;
    }
}
