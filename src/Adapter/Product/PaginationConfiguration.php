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
 * Class PaginationConfiguration is responsible for saving & loading pagination configuration for products.
 */
class PaginationConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var array<int, string>
     */
    private const CONFIGURATION_FIELDS = [
        'products_per_page',
        'default_order_by',
        'default_order_way',
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'products_per_page' => $this->configuration->get('PS_PRODUCTS_PER_PAGE', null, $shopConstraint),
            'default_order_by' => $this->configuration->get('PS_PRODUCTS_ORDER_BY', null, $shopConstraint),
            'default_order_way' => $this->configuration->get('PS_PRODUCTS_ORDER_WAY', null, $shopConstraint),
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

            $this->updateConfigurationValue('PS_PRODUCTS_PER_PAGE', 'products_per_page', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_PRODUCTS_ORDER_BY', 'default_order_by', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_PRODUCTS_ORDER_WAY', 'default_order_way', $config, $shopConstraint);
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
            ->setAllowedTypes('products_per_page', 'int')
            ->setAllowedTypes('default_order_by', 'int')
            ->setAllowedTypes('default_order_way', 'int');

        return $resolver;
    }
}
