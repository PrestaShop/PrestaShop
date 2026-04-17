<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Localization;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * Class LocalUnitsConfiguration is responsible for 'Improve > International > Localization' page
 * 'Local units' form data.
 */
class LocalUnitsConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param Configuration $configuration
     */
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
            'weight_unit' => $this->configuration->get('PS_WEIGHT_UNIT'),
            'distance_unit' => $this->configuration->get('PS_DISTANCE_UNIT'),
            'volume_unit' => $this->configuration->get('PS_VOLUME_UNIT'),
            'dimension_unit' => $this->configuration->get('PS_DIMENSION_UNIT'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        $errors = [];

        if ($this->validateConfiguration($config)) {
            $this->configuration->set('PS_WEIGHT_UNIT', $config['weight_unit']);
            $this->configuration->set('PS_DISTANCE_UNIT', $config['distance_unit']);
            $this->configuration->set('PS_VOLUME_UNIT', $config['volume_unit']);
            $this->configuration->set('PS_DIMENSION_UNIT', $config['dimension_unit']);
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $config)
    {
        return isset(
            $config['weight_unit'],
            $config['distance_unit'],
            $config['volume_unit'],
            $config['dimension_unit']
        );
    }
}
