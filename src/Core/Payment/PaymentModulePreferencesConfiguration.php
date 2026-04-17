<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Payment;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Module\Configuration\PaymentRestrictionsConfiguratorInterface;
use PrestaShop\PrestaShop\Core\Module\DataProvider\PaymentModuleListProviderInterface;

/**
 * Class PaymentModulePreferencesConfiguration is responsible for configuring payment module restrictions.
 */
final class PaymentModulePreferencesConfiguration implements DataConfigurationInterface
{
    /**
     * @var PaymentModuleListProviderInterface
     */
    private $paymentModuleProvider;

    /**
     * @var PaymentRestrictionsConfiguratorInterface
     */
    private $paymentRestrictionsConfigurator;

    /**
     * @param PaymentModuleListProviderInterface $paymentModuleProvider
     * @param PaymentRestrictionsConfiguratorInterface $paymentRestrictionsConfigurator
     */
    public function __construct(
        PaymentModuleListProviderInterface $paymentModuleProvider,
        PaymentRestrictionsConfiguratorInterface $paymentRestrictionsConfigurator
    ) {
        $this->paymentModuleProvider = $paymentModuleProvider;
        $this->paymentRestrictionsConfigurator = $paymentRestrictionsConfigurator;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $config = [];
        $paymentModules = $this->paymentModuleProvider->getPaymentModuleList();

        foreach ($paymentModules as $paymentModule) {
            $config['currency_restrictions'][$paymentModule->get('name')] = $paymentModule->get('currencies');
            $config['country_restrictions'][$paymentModule->get('name')] = $paymentModule->get('countries');
            $config['group_restrictions'][$paymentModule->get('name')] = $paymentModule->get('groups');
            $config['carrier_restrictions'][$paymentModule->get('name')] = $paymentModule->get('carriers');
        }

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        $errors = [];

        if ($this->validateConfiguration($config)) {
            $this->paymentRestrictionsConfigurator->configureCurrencyRestrictions($config['currency_restrictions']);
            $this->paymentRestrictionsConfigurator->configureCountryRestrictions($config['country_restrictions']);
            $this->paymentRestrictionsConfigurator->configureGroupRestrictions($config['group_restrictions']);
            $this->paymentRestrictionsConfigurator->configureCarrierRestrictions($config['carrier_restrictions']);
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $config)
    {
        return isset(
            $config['currency_restrictions'],
            $config['country_restrictions'],
            $config['group_restrictions'],
            $config['carrier_restrictions']
        );
    }
}
