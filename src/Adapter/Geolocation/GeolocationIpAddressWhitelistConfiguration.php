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
 * Class GeolocationIpAddressWhitelistConfiguration is responsible for configuring geolocation IP address whitelist data.
 */
final class GeolocationIpAddressWhitelistConfiguration extends AbstractMultistoreConfiguration
{
    public const FIELD_GEOLOCATION_WHITELIST = 'geolocation_whitelist';
    public const KEY_GEOLOCATION_WHITELIST = 'PS_GEOLOCATION_WHITELIST';
    private const CONFIGURATION_FIELDS = [self::FIELD_GEOLOCATION_WHITELIST];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            self::FIELD_GEOLOCATION_WHITELIST => $this->configuration->get(self::KEY_GEOLOCATION_WHITELIST, null, $this->getShopConstraint()),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $this->updateConfigurationValue(self::KEY_GEOLOCATION_WHITELIST, self::FIELD_GEOLOCATION_WHITELIST, $configuration, $this->getShopConstraint());
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
            ->setAllowedTypes(self::FIELD_GEOLOCATION_WHITELIST, 'string');

        return $resolver;
    }
}
