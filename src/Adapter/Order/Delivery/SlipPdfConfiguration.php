<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Order\Delivery;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Order\Invoice;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * This class manages Order delivery slip pdf configuration.
 */
final class SlipPdfConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Returns configuration used to manage Slip pdf in back office.
     *
     * @return array
     */
    public function getConfiguration()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            if (!Validate::isDate($configuration['date_to'])) {
                $errors[] = [
                    'key' => "Invalid 'to' date",
                    'domain' => 'Admin.Catalog.Notification',
                    'parameters' => [],
                ];
            }

            if (!Validate::isDate($configuration['date_from'])) {
                $errors[] = [
                    'key' => "Invalid 'from' date",
                    'domain' => 'Admin.Catalog.Notification',
                    'parameters' => [],
                ];
            }

            if (empty(Invoice::getByDeliveryDateInterval($configuration['date_from'], $configuration['date_to']))) {
                $errors[] = [
                    'key' => 'No delivery slip was found for this period.',
                    'domain' => 'Admin.Orderscustomers.Notification',
                    'parameters' => [],
                ];
            }
        }

        return !empty($errors) ? $errors : [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['date_from'],
            $configuration['date_to']
        );
    }
}
