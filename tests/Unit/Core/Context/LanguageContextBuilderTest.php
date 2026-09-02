<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Context;

use Language;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Language\Repository\LanguageRepository as ObjectModelLanguageRepository;
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
            $this->mockLocaleRepository($locale),
            $this->createMock(ContextStateManager::class),
            $this->createMock(ObjectModelLanguageRepository::class)
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

    public function testBuildLegacyContext(): void
    {
        $objectModelLanguage = $this->mockObjectModelLanguage();
        $objectModelLanguage->id = 42;
        $objectModelLanguage->method('getId')->willReturn(42);
        $contextManagerMock = $this->createMock(ContextStateManager::class);
        $objectModelLanguageMock = $this->mockObjectModelLanguageRepository($objectModelLanguage);
        $locale = $this->mockLocale();

        $builder = new LanguageContextBuilder(
            $this->createMock(LanguageRepositoryInterface::class),
            $this->mockLocaleRepository($locale),
            $contextManagerMock,
            $objectModelLanguageMock
        );
        $builder->setLanguageId($objectModelLanguage->getId());

        $contextManagerMock
            ->expects(static::once())
            ->method('setLanguage')
            ->with($objectModelLanguage);

        $contextManagerMock
            ->expects(static::once())
            ->method('setCurrentLocale')
            ->with($locale);

        $builder->buildLegacyContext();
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

    private function mockObjectModelLanguageRepository(Language $language): ObjectModelLanguageRepository
    {
        $repository = $this->createMock(ObjectModelLanguageRepository::class);
        $repository
            ->method('get')
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

    private function mockObjectModelLanguage(): Language|MockObject
    {
        $language = $this->createMock(Language::class);

        return $language;
    }
}
