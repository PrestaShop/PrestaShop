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

namespace PrestaShop\PrestaShop\Adapter\Geolocation;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class GeolocationOptionsConfiguration is responsible for configuring geolocation options data.
 */
final class GeolocationOptionsConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = ['geolocation_behaviour', 'geolocation_na_behaviour', 'geolocation_countries'];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'geolocation_behaviour' => (int) $this->configuration->get('PS_GEOLOCATION_BEHAVIOR', 0, $shopConstraint),
            'geolocation_na_behaviour' => (int) $this->configuration->get('PS_GEOLOCATION_NA_BEHAVIOR', 0, $shopConstraint),
            'geolocation_countries' => (string) $this->configuration->get('PS_ALLOWED_COUNTRIES', null, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();
            $this->updateConfigurationValue('PS_GEOLOCATION_BEHAVIOR', 'geolocation_behaviour', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_GEOLOCATION_NA_BEHAVIOR', 'geolocation_na_behaviour', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_ALLOWED_COUNTRIES', 'geolocation_countries', $configuration, $shopConstraint);
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
            ->setAllowedTypes('geolocation_behaviour', 'int')
            ->setAllowedTypes('geolocation_na_behaviour', 'int')
            ->setAllowedTypes('geolocation_countries', 'string');

        return $resolver;
    }
}
