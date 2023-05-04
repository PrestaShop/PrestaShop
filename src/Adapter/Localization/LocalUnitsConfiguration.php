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

namespace PrestaShop\PrestaShop\Adapter\Localization;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LocalUnitsConfiguration is responsible for 'Improve > International > Localization' page
 * 'Local units' form data.
 */
class LocalUnitsConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = ['weight_unit', 'distance_unit', 'volume_unit', 'dimension_unit'];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'weight_unit' => $this->configuration->get('PS_WEIGHT_UNIT', null, $shopConstraint),
            'distance_unit' => $this->configuration->get('PS_DISTANCE_UNIT', null, $shopConstraint),
            'volume_unit' => $this->configuration->get('PS_VOLUME_UNIT', null, $shopConstraint),
            'dimension_unit' => $this->configuration->get('PS_DIMENSION_UNIT', null, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];

        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();
            $this->updateConfigurationValue('PS_WEIGHT_UNIT', 'weight_unit', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_DISTANCE_UNIT', 'distance_unit', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_VOLUME_UNIT', 'volume_unit', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_DIMENSION_UNIT', 'dimension_unit', $configuration, $shopConstraint);
        }

        return $errors;
    }

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        $resolver = (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('weight_unit', 'string')
            ->setAllowedTypes('distance_unit', 'string')
            ->setAllowedTypes('volume_unit', 'string')
            ->setAllowedTypes('dimension_unit', 'string');

        return $resolver;
    }
}
