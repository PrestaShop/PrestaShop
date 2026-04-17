<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\QueryResult;

/**
 * Transfers editable language's data
 */
class EditableLanguage
{
    public function __construct(
        private readonly int $languageId,
        private readonly string $name,
        private readonly string $isoCode,
        private readonly string $tagIETF,
        private readonly string $locale,
        private readonly string $shortDateFormat,
        private readonly string $fullDateFormat,
        private readonly bool $isRtl,
        private readonly bool $isActive,
        private readonly array $shopAssociation,
    ) {
    }

    public function getLanguageId(): int
    {
        return $this->languageId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    public function getTagIETF(): string
    {
        return $this->tagIETF;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getShortDateFormat(): string
    {
        return $this->shortDateFormat;
    }

    public function getFullDateFormat(): string
    {
        return $this->fullDateFormat;
    }

    public function isRtl(): bool
    {
        return $this->isRtl;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getShopAssociation(): array
    {
        return $this->shopAssociation;
    }
}
