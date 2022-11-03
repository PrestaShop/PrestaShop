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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Twig\Extension;

use Currency;
use DateTime;
use DateTimeInterface;
use Language;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class LocalizationExtension extends AbstractExtension
{
    /**
     * @var string
     */
    private $dateFormatFull;

    /**
     * @var string
     */
    private $dateFormatLight;

    /**
     * @var Repository
     */
    private $localeRepository;

    /**
     * @var Language|null
     */
    private $contextLanguage;

    /**
     * @var Currency|null
     */
    private $contextCurrency;

    /**
     * @param string $contextDateFormatFull
     * @param string $contextDateFormatLight
     * @param Repository $localeRepository
     * @param Language|null $contextLanguage No strict type for this one because another Language class is used during install
     * @param Currency|null $contextCurrency
     */
    public function __construct(
        string $contextDateFormatFull,
        string $contextDateFormatLight,
        Repository $localeRepository,
        $contextLanguage,
        ?Currency $contextCurrency
    ) {
        $this->dateFormatFull = $contextDateFormatFull;
        $this->dateFormatLight = $contextDateFormatLight;
        $this->localeRepository = $localeRepository;
        $this->contextLanguage = $contextLanguage;
        $this->contextCurrency = $contextCurrency;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('date_format_full', [$this, 'dateFormatFull']),
            new TwigFilter('date_format_lite', [$this, 'dateFormatLite']),
            new TwigFilter('price_format', [$this, 'priceFormat']),
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
        // If no locale is specified and the context language is not accessible we can't format, so we return the input
        // unchanged, same thing for the currency (use getLocale getter, it is smarter ^^)
        if ((null === $locale && (null === $this->contextLanguage || empty($this->contextLanguage->getLocale()))) ||
            (null === $currencyCode && (null === $this->contextCurrency || empty($this->contextCurrency->iso_code)))) {
            return (string) $price;
        }

        $locale = $this->localeRepository->getLocale($locale ?? $this->contextLanguage->getLocale());

        return $locale->formatPrice($price, $currencyCode ?? $this->contextCurrency->iso_code);
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

        return $date->format($this->dateFormatFull);
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

        return $date->format($this->dateFormatLight);
    }
}
