<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Geolocation;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * Class GeolocationOptionsConfiguration is responsible for configuring geolocation options data.
 */
final class GeolocationOptionsConfiguration implements DataConfigurationInterface
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
            'geolocation_behaviour' => $this->configuration->get('PS_GEOLOCATION_BEHAVIOR'),
            'geolocation_na_behaviour' => $this->configuration->getInt('PS_GEOLOCATION_NA_BEHAVIOR'),
            'geolocation_countries' => $this->configuration->get('PS_ALLOWED_COUNTRIES'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        if ($this->validateConfiguration($config)) {
            $this->configuration->set('PS_GEOLOCATION_BEHAVIOR', $config['geolocation_behaviour']);
            $this->configuration->set('PS_GEOLOCATION_NA_BEHAVIOR', $config['geolocation_na_behaviour']);
            $this->configuration->set('PS_ALLOWED_COUNTRIES', $config['geolocation_countries']);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $config)
    {
        return isset(
            $config['geolocation_behaviour'],
            $config['geolocation_na_behaviour']
        );
    }
}
