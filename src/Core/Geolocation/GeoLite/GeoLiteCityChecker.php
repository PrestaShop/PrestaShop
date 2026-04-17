<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Geolocation\GeoLite;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Class GeoLiteCityChecker is responsible for checking if GeoLiteCity data is available.
 */
final class GeoLiteCityChecker implements GeoLiteCityCheckerInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        $geoIpDir = $this->configuration->get('_PS_GEOIP_DIR_');
        $geoLiteCityFile = $this->configuration->get('_PS_GEOIP_CITY_FILE_');

        return file_exists($geoIpDir . $geoLiteCityFile);
    }
}
