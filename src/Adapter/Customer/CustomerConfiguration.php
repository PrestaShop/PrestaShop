<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Customer;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Class CustomerConfiguration is responsible for saving & loading customer configuration
 */
class CustomerConfiguration implements DataConfigurationInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'redisplay_cart_at_login' => (bool) $this->configuration->get('PS_CART_FOLLOWING'),
            'send_email_after_registration' => (bool) $this->configuration->get('PS_CUSTOMER_CREATION_EMAIL'),
            'password_reset_delay' => (int) $this->configuration->get('PS_PASSWD_TIME_FRONT'),
            'enable_b2b_mode' => (bool) $this->configuration->get('PS_B2B_ENABLE'),
            'ask_for_birthday' => (bool) $this->configuration->get('PS_CUSTOMER_BIRTHDATE'),
            'enable_offers' => (bool) $this->configuration->get('PS_CUSTOMER_OPTIN'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        if ($this->validateConfiguration($config)) {
            $this->configuration->set('PS_CART_FOLLOWING', (int) $config['redisplay_cart_at_login']);
            $this->configuration->set('PS_CUSTOMER_CREATION_EMAIL', (int) $config['send_email_after_registration']);
            $this->configuration->set('PS_PASSWD_TIME_FRONT', (int) $config['password_reset_delay']);
            $this->configuration->set('PS_B2B_ENABLE', (int) $config['enable_b2b_mode']);
            $this->configuration->set('PS_CUSTOMER_BIRTHDATE', (int) $config['ask_for_birthday']);
            $this->configuration->set('PS_CUSTOMER_OPTIN', (int) $config['enable_offers']);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $config)
    {
        return isset(
            $config['redisplay_cart_at_login'],
            $config['send_email_after_registration'],
            $config['password_reset_delay'],
            $config['enable_b2b_mode'],
            $config['ask_for_birthday'],
            $config['enable_offers']
        );
    }
}
