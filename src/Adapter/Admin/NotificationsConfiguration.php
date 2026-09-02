<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Admin;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Manages the configuration data about notifications options.
 */
class NotificationsConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = [
        'show_notifs_new_orders',
        'show_notifs_new_customers',
        'show_notifs_new_messages',
    ];

    public function __construct(Configuration $configuration, Context $shopContext, FeatureInterface $multistoreFeature)
    {
        parent::__construct($configuration, $shopContext, $multistoreFeature);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'show_notifs_new_orders' => (bool) $this->configuration->get('PS_SHOW_NEW_ORDERS', false, $shopConstraint),
            'show_notifs_new_customers' => (bool) $this->configuration->get('PS_SHOW_NEW_CUSTOMERS', false, $shopConstraint),
            'show_notifs_new_messages' => (bool) $this->configuration->get('PS_SHOW_NEW_MESSAGES', false, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];

        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();
            $this->updateConfigurationValue('PS_SHOW_NEW_ORDERS', 'show_notifs_new_orders', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_SHOW_NEW_CUSTOMERS', 'show_notifs_new_customers', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_SHOW_NEW_MESSAGES', 'show_notifs_new_messages', $configuration, $shopConstraint);
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('show_notifs_new_orders', 'bool')
            ->setAllowedTypes('show_notifs_new_customers', 'bool')
            ->setAllowedTypes('show_notifs_new_messages', 'bool');
    }
}
