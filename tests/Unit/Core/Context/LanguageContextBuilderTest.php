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

namespace Tests\Unit\Core\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;
use PrestaShop\PrestaShop\Core\Localization\Specification\Number;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberSymbolList;

class LanguageContextBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $language = $this->mockLanguage();
        $locale = $this->mockLocale();
        $builder = new LanguageContextBuilder(
            $this->mockLanguageRepository($language),
            $this->mockLocaleRepository($locale)
        );
        $builder->setLanguageId($language->getId());

        $languageContext = $builder->build();
        // Check language data
        $this->assertEquals($language->getId(), $languageContext->getId());
        $this->assertEquals($language->getName(), $languageContext->getName());
        $this->assertEquals($language->getIsoCode(), $languageContext->getIsoCode());
        $this->assertEquals($language->getLocale(), $languageContext->getLocale());
        $this->assertEquals($language->getLanguageCode(), $languageContext->getLanguageCode());
        $this->assertEquals($language->isRTL(), $languageContext->isRTL());
        $this->assertEquals($language->getDateFormat(), $languageContext->getDateFormat());
        $this->assertEquals($language->getDateTimeFormat(), $languageContext->getDateTimeFormat());

        // Check locale methods
        $this->assertEquals($locale->getCode(), $languageContext->getCode());
        $this->assertEquals($locale->formatNumber(42), $languageContext->formatNumber(42));
        $this->assertEquals($locale->formatPrice(42, 'EUR'), $languageContext->formatPrice(42, 'EUR'));
        $this->assertEquals($locale->getPriceSpecification('EUR'), $languageContext->getPriceSpecification('EUR'));
        $this->assertEquals($locale->getNumberSpecification(), $languageContext->getNumberSpecification());
    }

    private function mockLanguageRepository(LanguageInterface $language): LanguageRepositoryInterface
    {
        $repository = $this->createMock(LanguageRepositoryInterface::class);
        $repository
            ->method('find')
            ->willReturn($language)
        ;

        return $repository;
    }

    private function mockLocaleRepository(LocaleInterface $locale): Repository|MockObject
    {
        $repository = $this->createMock(Repository::class);
        $repository
            ->method('getLocale')
            ->willReturn($locale)
        ;

        return $repository;
    }

    private function mockLocale(): LocaleInterface|MockObject
    {
        $locale = $this->createMock(LocaleInterface::class);
        $locale
            ->method('getCode')
            ->willReturn('fr-FR')
        ;
        $locale
            ->method('formatNumber')
            ->willReturn('1.000,45')
        ;
        $locale
            ->method('formatPrice')
            ->willReturn('1.000,45 €')
        ;

        $priceSpecification = new Number(
            '#,##0.### $',
            '-#,##0.### $',
            [
                new NumberSymbolList(
                    '.',
                    ',',
                    ' ',
                    '%',
                    '-',
                    '+',
                    'e',
                    'E',
                    '/m',
                    'inf',
                    'NaN'
                ),
            ],
            2,
            1,
            false,
            3,
            2
        );
        $locale
            ->method('getPriceSpecification')
            ->willReturn($priceSpecification)
        ;

        $numberSpecification = new Number(
            '#,##0.###',
            '-#,##0.###',
            [
                new NumberSymbolList(
                    '.',
                    ',',
                    ' ',
                    '%',
                    '-',
                    '+',
                    'e',
                    'E',
                    '/m',
                    'inf',
                    'NaN'
                ),
            ],
            3,
            2,
            true,
            2,
            3
        );
        $locale
            ->method('getNumberSpecification')
            ->willReturn($numberSpecification)
        ;

        return $locale;
    }

    private function mockLanguage(): LanguageInterface|MockObject
    {
        $language = $this->createMock(LanguageInterface::class);
        $language
            ->method('getId')
            ->willReturn(42)
        ;
        $language
            ->method('getName')
            ->willReturn('French')
        ;
        $language
            ->method('getIsoCode')
            ->willReturn('fr')
        ;
        $language
            ->method('getLocale')
            ->willReturn('fr-FR')
        ;
        $language
            ->method('getLanguageCode')
            ->willReturn('fr')
        ;
        $language
            ->method('isRTL')
            ->willReturn(false)
        ;
        $language
            ->method('getDateFormat')
            ->willReturn('d/m/Y')
        ;
        $language
            ->method('getDateTimeFormat')
            ->willReturn('d/m/Y H:i:s')
        ;

        return $language;
    }
}
