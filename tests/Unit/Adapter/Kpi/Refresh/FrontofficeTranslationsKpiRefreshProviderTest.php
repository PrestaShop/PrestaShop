<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\FrontofficeTranslationsKpiRefreshProvider;
use PrestaShop\PrestaShop\Adapter\Language\LanguageDataProvider;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;

class FrontofficeTranslationsKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    /**
     * @dataProvider provideCounters
     */
    public function testGetValueSumsTheProgressOfEveryThemeAndLanguage(array $counters, string $expectedValue): void
    {
        $this->kpiConfiguration->method('get')->willReturnCallback(static fn ($key) => $counters[$key] ?? null);

        $value = $this->createProvider()->getValue();

        $this->assertSame($expectedValue, $value->getValue());
        $this->assertCachedValue('FRONTOFFICE_TRANSLATIONS', $expectedValue, '+2 min');
    }

    public static function provideCounters(): iterable
    {
        yield 'three quarters' => [
            [
                'TRANSLATE_TOTAL_CLASSIC_EN' => '100',
                'TRANSLATE_DONE_CLASSIC_EN' => '100',
                'TRANSLATE_TOTAL_CLASSIC_FR' => '100',
                'TRANSLATE_DONE_CLASSIC_FR' => '50',
            ],
            '75%',
        ];
        // Keys are cut to 16 characters
        yield 'long theme name' => [
            [
                'TRANSLATE_TOTAL_MY_LONG_THEME_NA' => '3',
                'TRANSLATE_DONE_MY_LONG_THEME_NA' => '1',
            ],
            '33.3%',
        ];
        yield 'nothing translated' => [['TRANSLATE_TOTAL_CLASSIC_EN' => '100'], '0%'];
    }

    private function createProvider(): FrontofficeTranslationsKpiRefreshProvider
    {
        $classic = $this->createMock(Theme::class);
        $classic->method('getName')->willReturn('classic');
        $longName = $this->createMock(Theme::class);
        $longName->method('getName')->willReturn('my_long_theme_name');
        $themeRepository = $this->createMock(ThemeRepository::class);
        $themeRepository->method('getList')->willReturn([$classic, $longName]);
        $languageDataProvider = $this->createMock(LanguageDataProvider::class);
        $languageDataProvider->method('getLanguages')->willReturn([['iso_code' => 'en'], ['iso_code' => 'fr']]);

        return new FrontofficeTranslationsKpiRefreshProvider($themeRepository, $languageDataProvider, $this->kpiConfiguration);
    }
}
