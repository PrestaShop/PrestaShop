<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Twig\Component;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Context\CountryContext;
use PrestaShop\PrestaShop\Core\Context\CurrencyContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\LegacyControllerContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;
use PrestaShopBundle\Twig\Component\HeadTag;
use PrestaShopBundle\Twig\Layout\MenuBuilder;
use PrestaShopBundle\Twig\Layout\TemplateVariables;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class HeadTagLanguageTest extends TestCase
{
    /**
     * @dataProvider provideShippedLanguages
     */
    public function testTheEditorAndMomentGetALanguageTheyHave(string $isoCode, string $locale, string $languageCode, string $editorLanguage, string $momentLanguage): void
    {
        $headTag = $this->createHeadTag($isoCode, $locale, $languageCode);

        $this->assertSame($editorLanguage, $headTag->getIsoUserForEditor());
        $this->assertSame($momentLanguage, $headTag->getMomentLanguageCode());
    }

    public static function provideShippedLanguages(): iterable
    {
        // iso code, locale and language code as in install-dev/langs, then the TinyMCE file and moment locale
        yield 'English' => ['en', 'en-US', 'en-us', 'en', 'en-us'];
        yield 'French' => ['fr', 'fr-FR', 'fr', 'fr', 'fr-fr'];
        yield 'Brazilian Portuguese' => ['br', 'pt-BR', 'pt-br', 'br', 'pt-br'];
        yield 'Mexican Spanish' => ['mx', 'es-MX', 'es', 'es_MX', 'es-mx'];
        yield 'Norwegian' => ['no', 'no-NO', 'no', 'nb', 'nb'];
        yield 'Quebec French' => ['qc', 'fr-CA', 'qc', 'fr', 'fr-ca'];
        yield 'Slovenian' => ['si', 'sl-SI', 'sl-si', 'sl', 'sl-si'];
        yield 'Traditional Chinese' => ['tw', 'zh-TW', 'tw-tw', 'zh_TW', 'zh-tw'];
        yield 'Vietnamese' => ['vn', 'vi-VN', 'vi-vn', 'vi', 'vi-vn'];
        yield 'Hindi, no editor file' => ['hi', 'hi-IN', 'hi', 'en', 'hi-in'];
    }

    private function createHeadTag(string $isoCode, string $locale, string $languageCode): HeadTag
    {
        $languageContext = new LanguageContext(1, 'Language', $isoCode, $locale, $languageCode, false, 'Y-m-d', 'Y-m-d H:i:s', $this->createMock(LocaleInterface::class));

        return new HeadTag(
            $this->createMock(Configuration::class),
            $this->createMock(MenuBuilder::class),
            $this->createMock(TranslatorInterface::class),
            $this->createMock(HookDispatcherInterface::class),
            $this->createMock(TemplateVariables::class),
            $this->createMock(CountryContext::class),
            $this->createMock(ShopContext::class),
            $languageContext,
            $languageContext,
            $this->createMock(CurrencyContext::class),
            $this->createMock(LegacyControllerContext::class),
            $this->createMock(RouterInterface::class),
        );
    }
}
