<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\WithdrawalRequest\Configuration;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Provides data configuration for the withdrawal function options form.
 */
final class WithdrawalRequestOptionsConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = [
        'enable_withdrawal_function',
        'withdrawal_period_in_days',
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'enable_withdrawal_function' => (bool) $this->configuration->get('PS_WITHDRAWAL_FUNCTION', null, $shopConstraint),
            'withdrawal_period_in_days' => (int) $this->configuration->get('PS_WITHDRAWAL_PERIOD', null, $shopConstraint),
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
        $this->updateConfigurationValue('PS_WITHDRAWAL_FUNCTION', 'enable_withdrawal_function', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_WITHDRAWAL_PERIOD', 'withdrawal_period_in_days', $configuration, $shopConstraint);

        return [];
    }

    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('enable_withdrawal_function', 'bool')
            ->setAllowedTypes('withdrawal_period_in_days', 'int');
    }
}
