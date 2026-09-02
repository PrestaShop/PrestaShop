<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

            if (!empty($errors)) {
                return $errors;
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
