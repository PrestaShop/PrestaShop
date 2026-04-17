<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\CLDR;

use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleDataLayerInterface as CldrLocaleDataLayerInterface;

/**
 * LocaleDataSource provides CLDR LocaleData objects.
 *
 * This class uses Locale data layers as middlewares stack to read CLDR data.
 */
class LocaleDataSource
{
    /**
     * @var CldrLocaleDataLayerInterface
     */
    private $topLayer;

    /**
     * LocaleDataSource constructor needs a CldrLocaleDataLayerInterface layer object.
     * This top layer might be chained with lower layers and will be the entry point of this middleware stack.
     *
     * @param CldrLocaleDataLayerInterface $topLayer
     */
    public function __construct(CldrLocaleDataLayerInterface $topLayer)
    {
        $this->topLayer = $topLayer;
    }

    /**
     * @param string $localeCode
     *
     * @return LocaleData|null
     */
    public function getLocaleData($localeCode)
    {
        return $this->topLayer->read($localeCode);
    }
}
