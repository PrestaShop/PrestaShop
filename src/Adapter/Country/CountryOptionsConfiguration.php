<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Country;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Loads and saves the "Options" block configuration of the Countries page
 * (Improve > International > Locations > Countries).
 */
class CountryOptionsConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = ['restrict_country_to_delivery'];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'restrict_country_to_delivery' => (bool) $this->configuration->get('PS_RESTRICT_DELIVERED_COUNTRIES', false, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();
            $this->updateConfigurationValue('PS_RESTRICT_DELIVERED_COUNTRIES', 'restrict_country_to_delivery', $configuration, $shopConstraint);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('restrict_country_to_delivery', 'bool');
    }
}
