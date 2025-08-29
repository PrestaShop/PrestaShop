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
