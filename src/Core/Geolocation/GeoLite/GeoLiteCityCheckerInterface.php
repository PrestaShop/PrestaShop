<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Geolocation\GeoLite;

/**
 * Interface GeoLiteCityInterface defines contract for GeoLiteCity.
 */
interface GeoLiteCityCheckerInterface
{
    /**
     * Check if GeoLiteCity data is available in PrestaShop installation.
     *
     * @return bool
     */
    public function isAvailable();
}
