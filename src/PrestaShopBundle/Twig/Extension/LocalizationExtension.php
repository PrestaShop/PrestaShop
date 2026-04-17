<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Twig\Extension;

use DateTime;
use DateTimeInterface;
use PrestaShop\PrestaShop\Core\Context\CurrencyContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class LocalizationExtension extends AbstractExtension
{
    public function __construct(
        private readonly Repository $localeRepository,
        private readonly LanguageContext $languageContext,
        private readonly CurrencyContext $currencyContext,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('date_format_full', [$this, 'dateFormatFull']),
            new TwigFilter('date_format_lite', [$this, 'dateFormatLite']),
            new TwigFilter('price_format', [$this, 'priceFormat']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'format_date',
                function ($date) {
                    return (new DateTime($date))->format($this->languageContext->getDateFormat());
                }
            ),
        ];
    }

    /**
     * @param float $price
     * @param string|null $currencyCode
     * @param string|null $locale
     *
     * @return string
     */
    public function priceFormat(float $price, ?string $currencyCode = null, ?string $locale = null): string
    {
        if (null !== $locale) {
            $cldrLocale = $this->localeRepository->getLocale($locale);

            return $cldrLocale->formatPrice($price, $currencyCode ?? $this->currencyContext->getIsoCode());
        } else {
            return $this->languageContext->formatPrice($price, $currencyCode ?? $this->currencyContext->getIsoCode());
        }
    }

    /**
     * @param DateTimeInterface|string $date
     *
     * @return string
     */
    public function dateFormatFull($date): string
    {
        if (!$date instanceof DateTimeInterface) {
            $date = new DateTime($date);
        }

        return $date->format($this->languageContext->getDateTimeFormat());
    }

    /**
     * @param DateTimeInterface|string $date
     *
     * @return string
     */
    public function dateFormatLite($date): string
    {
        if (!$date instanceof DateTimeInterface) {
            $date = new DateTime($date);
        }

        return $date->format($this->languageContext->getDateFormat());
    }
}
