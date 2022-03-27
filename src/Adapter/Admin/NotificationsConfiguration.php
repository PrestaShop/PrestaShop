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

namespace PrestaShop\PrestaShop\Adapter\Admin;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration\NotificationsType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Manages the configuration data about notifications options.
 */
class NotificationsConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = [
        NotificationsType::FIELD_SHOW_NOTIFS_NEW_ORDERS,
        NotificationsType::FIELD_SHOW_NOTIFS_NEW_CUSTOMERS,
        NotificationsType::FIELD_SHOW_NOTIFS_NEW_MESSAGES,
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            NotificationsType::FIELD_SHOW_NOTIFS_NEW_ORDERS 
                => (bool) $this->configuration->get('PS_SHOW_NEW_ORDERS', null, $shopConstraint),
            NotificationsType::FIELD_SHOW_NOTIFS_NEW_CUSTOMERS 
                => (bool) $this->configuration->get('PS_SHOW_NEW_CUSTOMERS', null, $shopConstraint),
            NotificationsType::FIELD_SHOW_NOTIFS_NEW_MESSAGES 
                => (bool) $this->configuration->get('PS_SHOW_NEW_MESSAGES', null, $shopConstraint),
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

            $updateConfigurationValue = function(string $configurationKey, string $fieldName) use ($configuration, $shopConstraint): void {
                $this->updateConfigurationValue($configurationKey, $fieldName, $configuration, $shopConstraint);
            };

            $updateConfigurationValue('PS_SHOW_NEW_ORDERS', NotificationsType::FIELD_SHOW_NOTIFS_NEW_ORDERS);
            $updateConfigurationValue('PS_SHOW_NEW_CUSTOMERS', NotificationsType::FIELD_SHOW_NOTIFS_NEW_CUSTOMERS);
            $updateConfigurationValue('PS_SHOW_NEW_MESSAGES', NotificationsType::FIELD_SHOW_NOTIFS_NEW_MESSAGES);
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
            ->setAllowedTypes(NotificationsType::FIELD_SHOW_NOTIFS_NEW_ORDERS, ['bool'])
            ->setAllowedTypes(NotificationsType::FIELD_SHOW_NOTIFS_NEW_CUSTOMERS, ['bool'])
            ->setAllowedTypes(NotificationsType::FIELD_SHOW_NOTIFS_NEW_MESSAGES, ['bool'])
        ;

        return $resolver;
    }
}
