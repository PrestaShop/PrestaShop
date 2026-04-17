<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Carrier;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class HandlingConfiguration is responsible for saving and loading Handling options configuration.
 */
class HandlingConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = ['shipping_handling_charges', 'free_shipping_price', 'free_shipping_weight'];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'shipping_handling_charges' => (float) $this->configuration->get('PS_SHIPPING_HANDLING', null, $shopConstraint),
            'free_shipping_price' => (float) $this->configuration->get('PS_SHIPPING_FREE_PRICE', null, $shopConstraint),
            'free_shipping_weight' => (float) $this->configuration->get('PS_SHIPPING_FREE_WEIGHT', null, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();
            $this->updateConfigurationValue('PS_SHIPPING_HANDLING', 'shipping_handling_charges', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_SHIPPING_FREE_PRICE', 'free_shipping_price', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_SHIPPING_FREE_WEIGHT', 'free_shipping_weight', $configuration, $shopConstraint);
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
            ->setAllowedTypes('shipping_handling_charges', 'float')
            ->setAllowedTypes('free_shipping_price', 'float')
            ->setAllowedTypes('free_shipping_weight', 'float');

        return $resolver;
    }
}
