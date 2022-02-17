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
    public const FIELD_GEOLOCATION_BEHAVIOR = 'geolocation_behaviour';
    public const FIELD_GEOLOCATION_NA_BEHAVIOR = 'geolocation_na_behaviour';
    public const FIELD_ALLOWED_COUNTRIES = 'geolocation_countries';
    public const KEY_GEOLOCATION_BEHAVIOR = 'PS_GEOLOCATION_BEHAVIOR';
    public const KEY_GEOLOCATION_NA_BEHAVIOR = 'PS_GEOLOCATION_NA_BEHAVIOR';
    public const KEY_ALLOWED_COUNTRIES = 'PS_ALLOWED_COUNTRIES';

    private const CONFIGURATION_FIELDS = [self::FIELD_GEOLOCATION_BEHAVIOR,
        self::FIELD_GEOLOCATION_NA_BEHAVIOR,
        self::FIELD_ALLOWED_COUNTRIES,
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            self::FIELD_GEOLOCATION_BEHAVIOR => (int) $this->configuration->get(self::KEY_GEOLOCATION_BEHAVIOR, 0, $shopConstraint),
            self::FIELD_GEOLOCATION_NA_BEHAVIOR => (int) $this->configuration->get(self::KEY_GEOLOCATION_NA_BEHAVIOR, 0, $shopConstraint),
            self::FIELD_ALLOWED_COUNTRIES => (string) $this->configuration->get(self::KEY_ALLOWED_COUNTRIES, null, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();
            $this->updateConfigurationValue(self::KEY_GEOLOCATION_BEHAVIOR, self::FIELD_GEOLOCATION_BEHAVIOR, $configuration, $shopConstraint);
            $this->updateConfigurationValue(self::KEY_GEOLOCATION_NA_BEHAVIOR, self::FIELD_GEOLOCATION_NA_BEHAVIOR, $configuration, $shopConstraint);
            $this->updateConfigurationValue(self::KEY_ALLOWED_COUNTRIES, self::FIELD_ALLOWED_COUNTRIES, $configuration, $shopConstraint);
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
            ->setAllowedTypes(self::FIELD_GEOLOCATION_BEHAVIOR, 'int')
            ->setAllowedTypes(self::FIELD_GEOLOCATION_NA_BEHAVIOR, 'int')
            ->setAllowedTypes(self::FIELD_ALLOWED_COUNTRIES, 'string');

        return $resolver;
    }
}
