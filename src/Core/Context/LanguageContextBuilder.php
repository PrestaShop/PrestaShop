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

namespace PrestaShop\PrestaShop\Core\Context;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository;

class LanguageContextBuilder
{
    private ?int $languageId = null;

    public function __construct(
        private readonly LanguageRepositoryInterface $languageRepository,
        private readonly Repository $localeRepository
    ) {
    }

    public function build(): LanguageContext
    {
        $this->assertArguments();

        /** @var LanguageInterface $language */
        $language = $this->languageRepository->find($this->languageId);

        $localizationLocale = $this->localeRepository->getLocale($language->getLocale());

        return new LanguageContext(
            id: $language->getId(),
            name: $language->getName(),
            isoCode: $language->getIsoCode(),
            locale: $language->getLocale(),
            languageCode: $language->getLanguageCode(),
            isRTL: $language->isRTL(),
            dateFormat: $language->getDateFormat(),
            dateTimeFormat: $language->getDateTimeFormat(),
            localizationLocale: $localizationLocale,
        );
    }

    public function setLanguageId(int $languageId): void
    {
        $this->languageId = $languageId;
    }

    private function assertArguments(): void
    {
        if (null === $this->languageId) {
            throw new InvalidArgumentException(sprintf(
                'Cannot build Language context as no languageId has been defined you need to call %s::setLanguageId to define it before building the Language context',
                self::class
            ));
        }
    }
}
