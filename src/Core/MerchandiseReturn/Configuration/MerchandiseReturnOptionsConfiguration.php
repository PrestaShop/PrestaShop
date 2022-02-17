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

namespace PrestaShop\PrestaShop\Core\MerchandiseReturn\Configuration;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShopBundle\Form\Admin\Sell\CustomerService\MerchandiseReturn\MerchandiseReturnOptionsType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Provides data configuration for merchandise returns options form
 */
class MerchandiseReturnOptionsConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = [
        MerchandiseReturnOptionsType::FIELD_ENABLE_ORDER_RETURN,
        MerchandiseReturnOptionsType::FIELD_ORDER_RETURN_PERIOD_IN_DAYS,
        MerchandiseReturnOptionsType::FIELD_ORDER_RETURN_PREFIX,
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            MerchandiseReturnOptionsType::FIELD_ENABLE_ORDER_RETURN => (bool) $this->configuration->get('PS_ORDER_RETURN', null, $shopConstraint),
            MerchandiseReturnOptionsType::FIELD_ORDER_RETURN_PERIOD_IN_DAYS => (int) $this->configuration->get('PS_ORDER_RETURN_NB_DAYS', null, $shopConstraint),
            MerchandiseReturnOptionsType::FIELD_ORDER_RETURN_PREFIX => $this->configuration->get('PS_RETURN_PREFIX', null, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if (!$this->validateConfiguration($configuration)) {
            return [
                [
                    'key' => 'Invalid configuration',
                    'parameters' => [],
                    'domain' => 'Admin.Notifications.Warning',
                ],
            ];
        }
        $shopConstraint = $this->getShopConstraint();
        $this->updateConfigurationValue(
            'PS_ORDER_RETURN',
            MerchandiseReturnOptionsType::FIELD_ENABLE_ORDER_RETURN,
            $configuration,
            $shopConstraint
        );
        $this->updateConfigurationValue(
            'PS_ORDER_RETURN_NB_DAYS',
            MerchandiseReturnOptionsType::FIELD_ORDER_RETURN_PERIOD_IN_DAYS,
            $configuration,
            $shopConstraint
        );
        $this->updateConfigurationValue(
            'PS_RETURN_PREFIX',
            MerchandiseReturnOptionsType::FIELD_ORDER_RETURN_PREFIX,
            $configuration,
            $shopConstraint
        );

        return [];
    }

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes(MerchandiseReturnOptionsType::FIELD_ENABLE_ORDER_RETURN, 'bool')
            ->setAllowedTypes(MerchandiseReturnOptionsType::FIELD_ORDER_RETURN_PERIOD_IN_DAYS, 'int')
            ->setAllowedTypes(MerchandiseReturnOptionsType::FIELD_ORDER_RETURN_PREFIX, 'array');
    }
}
