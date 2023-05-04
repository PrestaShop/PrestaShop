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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Model\DTO;

class LanguageDTO
{
    private $id;
    private $name;
    private $active;
    private $isoCode;
    private $languageCode;
    private $locale;
    private $dateFormatLite;
    private $dateFormatFull;
    private $isRtl;

    public function __construct(
        int $id,
        string $name,
        bool $active,
        string $isoCode,
        string $languageCode,
        string $locale,
        string $dateFormatLite,
        string $dateFormatFull,
        bool $isRtl
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->active = $active;
        $this->isoCode = $isoCode;
        $this->languageCode = $languageCode;
        $this->locale = $locale;
        $this->dateFormatLite = $dateFormatLite;
        $this->dateFormatFull = $dateFormatFull;
        $this->isRtl = $isRtl;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getDateFormatLite(): string
    {
        return $this->dateFormatLite;
    }

    public function getDateFormatFull(): string
    {
        return $this->dateFormatFull;
    }

    public function getIsRtl(): bool
    {
        return $this->isRtl;
    }
}
