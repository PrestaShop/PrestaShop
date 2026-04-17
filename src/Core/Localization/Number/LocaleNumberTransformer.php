<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Localization\Number;

use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;

class LocaleNumberTransformer
{
    /** @var string[] */
    private const FORCED_LOCALES_TO_EN_NUMBERS = ['ar', 'bn', 'fa'];

    public function __construct(
        private LocaleInterface $locale
    ) {
    }

    /**
     * Retrieve locale for numbers.
     * (to avoid use of persian/arabic numbers)
     *
     * @return string
     */
    public function getLocaleForNumberInputs()
    {
        $locale = substr($this->locale->getCode(), 0, 2);

        return in_array($locale, self::FORCED_LOCALES_TO_EN_NUMBERS) ? 'en' : $locale;
    }
}
