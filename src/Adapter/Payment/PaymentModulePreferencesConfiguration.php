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

namespace PrestaShop\PrestaShop\Adapter\Payment;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Module\DataProvider\PaymentModuleProviderInterface;

final class PaymentModulePreferencesConfiguration implements DataConfigurationInterface
{
    /**
     * @var PaymentModuleProviderInterface
     */
    private $paymentModuleProvider;

    /**
     * @param PaymentModuleProviderInterface $paymentModuleProvider
     */
    public function __construct(PaymentModuleProviderInterface $paymentModuleProvider)
    {
        $this->paymentModuleProvider = $paymentModuleProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $config = [];
        $paymentModules = $this->paymentModuleProvider->getPaymentModuleList();

        foreach ($paymentModules as $paymentModule) {
            $config['country_restriction'][$paymentModule->get('name')] = $paymentModule->get('countries');
            $config['group_restriction'][$paymentModule->get('name')] = $paymentModule->get('groups');
            $config['carrier_restriction'][$paymentModule->get('name')] = $paymentModule->get('carriers');
        }

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        // TODO: Implement updateConfiguration() method.
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $config)
    {
        // TODO: Implement validateConfiguration() method.
    }
}
