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

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class loads and saves general configuration for product.
 */
class GeneralConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var array<int, string>
     */
    private const CONFIGURATION_FIELDS = [
        'catalog_mode',
        'catalog_mode_with_prices',
        'new_days_number',
        'short_description_limit',
        'quantity_discount',
        'force_friendly_url',
        'default_status',
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'catalog_mode' => (bool) $this->configuration->get('PS_CATALOG_MODE', false, $shopConstraint),
            'catalog_mode_with_prices' => (bool) $this->configuration->get('PS_CATALOG_MODE_WITH_PRICES', false, $shopConstraint),
            'new_days_number' => (int) $this->configuration->get('PS_NB_DAYS_NEW_PRODUCT', null, $shopConstraint),
            'short_description_limit' => (int) $this->configuration->get('PS_PRODUCT_SHORT_DESC_LIMIT', null, $shopConstraint),
            'quantity_discount' => (int) $this->configuration->get('PS_QTY_DISCOUNT_ON_COMBINATION', null, $shopConstraint),
            'force_friendly_url' => (bool) $this->configuration->get('PS_FORCE_FRIENDLY_PRODUCT', false, $shopConstraint),
            'default_status' => (bool) $this->configuration->get('PS_PRODUCT_ACTIVATION_DEFAULT', false, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        $errors = [];

        if ($this->validateConfiguration($config)) {
            $shopConstraint = $this->getShopConstraint();

            $config['catalog_mode_with_prices'] = $config['catalog_mode'] ? $config['catalog_mode_with_prices'] : 0;

            $this->updateConfigurationValue('PS_CATALOG_MODE', 'catalog_mode', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_CATALOG_MODE_WITH_PRICES', 'catalog_mode_with_prices', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_NB_DAYS_NEW_PRODUCT', 'new_days_number', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_PRODUCT_SHORT_DESC_LIMIT', 'short_description_limit', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_QTY_DISCOUNT_ON_COMBINATION', 'quantity_discount', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_FORCE_FRIENDLY_PRODUCT', 'force_friendly_url', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_PRODUCT_ACTIVATION_DEFAULT', 'default_status', $config, $shopConstraint);
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
            ->setAllowedTypes('catalog_mode', 'bool')
            ->setAllowedTypes('catalog_mode_with_prices', 'bool')
            ->setAllowedTypes('new_days_number', 'int')
            ->setAllowedTypes('short_description_limit', 'int')
            ->setAllowedTypes('quantity_discount', 'int')
            ->setAllowedTypes('force_friendly_url', 'bool')
            ->setAllowedTypes('default_status', 'bool');

        return $resolver;
    }
}
