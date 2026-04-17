<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\CLDR;

use PrestaShop\PrestaShop\Core\Localization\CLDR\Locale as CldrLocale;

/**
 * CLDR Locale Repository.
 *
 * Provides CLDR Locale objects
 */
class LocaleRepository
{
    /**
     * @var LocaleDataSource
     */
    protected $dataSource;

    public function __construct(LocaleDataSource $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * Get a CLDR Locale by simplified IETF tag.
     *
     * @param string $localeCode e.g.: fr-FR, en-US...
     *
     * @return CldrLocale|null A CldrLocale object. Null if not found
     */
    public function getLocale($localeCode)
    {
        $localeData = $this->dataSource->getLocaleData($localeCode);

        if (null === $localeData) {
            return null;
        }

        return new CldrLocale($localeData);
    }
}
