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

namespace PrestaShop\PrestaShop\Adapter\Customer;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CustomerConfiguration is responsible for saving & loading customer configuration.
 */
class CustomerConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var array<int, string>
     */
    private const CONFIGURATION_FIELDS = [
        'redisplay_cart_at_login',
        'send_email_after_registration',
        'password_reset_delay',
        'enable_b2b_mode',
        'ask_for_birthday',
        'enable_offers',
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'redisplay_cart_at_login' => (bool) $this->configuration->get('PS_CART_FOLLOWING', false, $shopConstraint),
            'send_email_after_registration' => (bool) $this->configuration->get('PS_CUSTOMER_CREATION_EMAIL', false, $shopConstraint),
            'password_reset_delay' => (int) $this->configuration->get('PS_PASSWD_TIME_FRONT', 0, $shopConstraint),
            'enable_b2b_mode' => (bool) $this->configuration->get('PS_B2B_ENABLE', false, $shopConstraint),
            'ask_for_birthday' => (bool) $this->configuration->get('PS_CUSTOMER_BIRTHDATE', false, $shopConstraint),
            'enable_offers' => (bool) $this->configuration->get('PS_CUSTOMER_OPTIN', false, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        if ($this->validateConfiguration($config)) {
            $shopConstraint = $this->getShopConstraint();

            $this->updateConfigurationValue('PS_CART_FOLLOWING', 'redisplay_cart_at_login', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_CUSTOMER_CREATION_EMAIL', 'send_email_after_registration', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_PASSWD_TIME_FRONT', 'password_reset_delay', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_B2B_ENABLE', 'enable_b2b_mode', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_CUSTOMER_BIRTHDATE', 'ask_for_birthday', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_CUSTOMER_OPTIN', 'enable_offers', $config, $shopConstraint);
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
            ->setAllowedTypes('redisplay_cart_at_login', 'bool')
            ->setAllowedTypes('send_email_after_registration', 'bool')
            ->setAllowedTypes('password_reset_delay', 'int')
            ->setAllowedTypes('enable_b2b_mode', 'bool')
            ->setAllowedTypes('ask_for_birthday', 'bool')
            ->setAllowedTypes('enable_offers', 'bool');

        return $resolver;
    }
}
