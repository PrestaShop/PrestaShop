<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
