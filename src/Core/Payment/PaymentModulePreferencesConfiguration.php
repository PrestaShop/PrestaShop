<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Payment;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Module\Configuration\PaymentRestrictionsConfiguratorInterface;
use PrestaShop\PrestaShop\Core\Module\Configuration\TradeCreditPaymentModuleConfiguratorInterface;
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
     * @var TradeCreditPaymentModuleConfiguratorInterface
     */
    private $tradeCreditConfigurator;

    /**
     * @var int
     */
    private $shopId;

    /**
     * @param PaymentModuleListProviderInterface $paymentModuleProvider
     * @param PaymentRestrictionsConfiguratorInterface $paymentRestrictionsConfigurator
     * @param TradeCreditPaymentModuleConfiguratorInterface $tradeCreditConfigurator
     * @param int $shopId
     */
    public function __construct(
        PaymentModuleListProviderInterface $paymentModuleProvider,
        PaymentRestrictionsConfiguratorInterface $paymentRestrictionsConfigurator,
        TradeCreditPaymentModuleConfiguratorInterface $tradeCreditConfigurator,
        int $shopId
    ) {
        $this->paymentModuleProvider = $paymentModuleProvider;
        $this->paymentRestrictionsConfigurator = $paymentRestrictionsConfigurator;
        $this->tradeCreditConfigurator = $tradeCreditConfigurator;
        $this->shopId = $shopId;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $config = ['trade_credit_modules' => []];
        $paymentModules = $this->paymentModuleProvider->getPaymentModuleList();

        foreach ($paymentModules as $paymentModule) {
            if ($paymentModule->get('trade_credit')) {
                $config['trade_credit_modules'][] = $paymentModule->get('name');
            }

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
            $this->tradeCreditConfigurator->setTradeCreditModules(
                $this->shopId,
                $this->getModuleIds((array) $config['trade_credit_modules'])
            );
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
            $config['carrier_restrictions'],
            $config['trade_credit_modules']
        );
    }

    /**
     * Maps payment module names to their ids, ignoring unknown names.
     *
     * @param string[] $moduleNames
     *
     * @return int[]
     */
    private function getModuleIds(array $moduleNames): array
    {
        $paymentModules = $this->paymentModuleProvider->getPaymentModuleList();
        $moduleIds = [];

        foreach ($moduleNames as $moduleName) {
            if (isset($paymentModules[$moduleName])) {
                $moduleIds[] = (int) $paymentModules[$moduleName]->database->get('id');
            }
        }

        return $moduleIds;
    }
}
