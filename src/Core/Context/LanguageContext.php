<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberInterface;

/**
 * This context service gives access to all contextual data related to language.
 *
 * It also implements some core interfaces that are used in many places in the code so that the
 * context can be injected and used in place of these interfaces.
 */
class LanguageContext implements LanguageInterface, LocaleInterface
{
    public function __construct(
        protected readonly int $id,
        protected readonly string $name,
        protected readonly string $isoCode,
        protected readonly string $locale,
        protected readonly string $languageCode,
        protected readonly bool $isRTL,
        protected readonly string $dateFormat,
        protected readonly string $dateTimeFormat,
        protected readonly LocaleInterface $localizationLocale
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    public function isRTL(): bool
    {
        return $this->isRTL;
    }

    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    public function getDateTimeFormat(): string
    {
        return $this->dateTimeFormat;
    }

    public function getCode(): string
    {
        return $this->localizationLocale->getCode();
    }

    public function formatNumber(int|float|string $number): string
    {
        return $this->localizationLocale->formatNumber($number);
    }

    public function formatPrice(int|float|string $number, string $currencyCode): string
    {
        return $this->localizationLocale->formatPrice($number, $currencyCode);
    }

    public function getPriceSpecification(string $currencyCode): NumberInterface
    {
        return $this->localizationLocale->getPriceSpecification($currencyCode);
    }

    public function getNumberSpecification(): NumberInterface
    {
        return $this->localizationLocale->getNumberSpecification();
    }
}
