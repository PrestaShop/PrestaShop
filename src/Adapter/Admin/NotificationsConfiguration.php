<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Admin;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * Manages the configuration data about notifications options.
 */
class NotificationsConfiguration implements DataConfigurationInterface
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
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'show_notifs_new_orders' => $this->configuration->getBoolean('PS_SHOW_NEW_ORDERS'),
            'show_notifs_new_customers' => $this->configuration->getBoolean('PS_SHOW_NEW_CUSTOMERS'),
            'show_notifs_new_messages' => $this->configuration->getBoolean('PS_SHOW_NEW_MESSAGES'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];

        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_SHOW_NEW_ORDERS', (bool) $configuration['show_notifs_new_orders']);
            $this->configuration->set('PS_SHOW_NEW_CUSTOMERS', (bool) $configuration['show_notifs_new_customers']);
            $this->configuration->set('PS_SHOW_NEW_MESSAGES', (bool) $configuration['show_notifs_new_messages']);
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['show_notifs_new_orders'],
            $configuration['show_notifs_new_customers'],
            $configuration['show_notifs_new_messages']
        );
    }
}
