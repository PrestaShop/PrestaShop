<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\MerchandiseReturn\Configuration;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Provides data configuration for merchandise returns options form
 */
class MerchandiseReturnOptionsConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = [
        'enable_order_return',
        'order_return_period_in_days',
        'order_return_prefix',
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'enable_order_return' => (bool) $this->configuration->get('PS_ORDER_RETURN', null, $shopConstraint),
            'order_return_period_in_days' => (int) $this->configuration->get('PS_ORDER_RETURN_NB_DAYS', null, $shopConstraint),
            'order_return_prefix' => $this->configuration->get('PS_RETURN_PREFIX', null, $shopConstraint),
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
            'enable_order_return',
            $configuration,
            $shopConstraint
        );
        $this->updateConfigurationValue(
            'PS_ORDER_RETURN_NB_DAYS',
            'order_return_period_in_days',
            $configuration,
            $shopConstraint
        );
        $this->updateConfigurationValue(
            'PS_RETURN_PREFIX',
            'order_return_prefix',
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
            ->setAllowedTypes('enable_order_return', 'bool')
            ->setAllowedTypes('order_return_period_in_days', 'int')
            ->setAllowedTypes('order_return_prefix', 'array');
    }
}
