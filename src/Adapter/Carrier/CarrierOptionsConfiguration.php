<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Carrier;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CarrierOptionsConfiguration is responsible for saving and loading Carrier Options configuration.
 */
class CarrierOptionsConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = ['default_carrier', 'carrier_default_order_by', 'carrier_default_order_way'];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'default_carrier' => (int) $this->configuration->get('PS_CARRIER_DEFAULT', null, $shopConstraint),
            'carrier_default_order_by' => (int) $this->configuration->get('PS_CARRIER_DEFAULT_SORT', null, $shopConstraint),
            'carrier_default_order_way' => (int) $this->configuration->get('PS_CARRIER_DEFAULT_ORDER', null, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();
            $this->updateConfigurationValue('PS_CARRIER_DEFAULT', 'default_carrier', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_CARRIER_DEFAULT_SORT', 'carrier_default_order_by', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_CARRIER_DEFAULT_ORDER', 'carrier_default_order_way', $configuration, $shopConstraint);
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
            ->setAllowedTypes('default_carrier', 'int')
            ->setAllowedTypes('carrier_default_order_by', 'int')
            ->setAllowedTypes('carrier_default_order_way', 'int');

        return $resolver;
    }
}
