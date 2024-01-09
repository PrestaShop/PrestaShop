<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
    public function priceFormat(float $price, string $currencyCode = null, string $locale = null): string
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
