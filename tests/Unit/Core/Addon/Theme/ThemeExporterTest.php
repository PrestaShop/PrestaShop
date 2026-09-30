<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Addon\Theme;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeExporter;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Translation\Exporter\ThemeExporter as TranslationsExporter;
use Symfony\Component\Filesystem\Filesystem;

class ThemeExporterTest extends TestCase
{
    private const THEME_NAME = 'classic';
    private const CACHE_DIR = '/tmp/ps_cache/';
    private const THEMES_DIR = '/tmp/ps_themes/';

    /**
     * @var ConfigurationInterface&MockObject
     */
    private $configuration;

    /**
     * @var Filesystem&MockObject
     */
    private $fileSystem;

    /**
     * @var LangRepository&MockObject
     */
    private $langRepository;

    /**
     * @var TranslationsExporter&MockObject
     */
    private $translationsExporter;

    protected function setUp(): void
    {
        $this->configuration = $this->createMock(ConfigurationInterface::class);
        $this->configuration->method('get')->willReturnMap([
            ['_PS_CACHE_DIR_', null, null, self::CACHE_DIR],
            ['_PS_ALL_THEMES_DIR_', null, null, self::THEMES_DIR],
            ['_PS_MODULE_DIR_', null, null, '/tmp/ps_modules/'],
        ]);

        $this->fileSystem = $this->createMock(Filesystem::class);
        $this->langRepository = $this->createMock(LangRepository::class);
        $this->translationsExporter = $this->createMock(TranslationsExporter::class);
    }

    public function testExportDoesNotThrowWhenAllLanguagesAreCustom(): void
    {
        $customLang = $this->createLang('xy_XY');

        $this->langRepository->method('findAll')->willReturn([$customLang]);

        $this->translationsExporter
            ->method('exportCatalogues')
            ->willThrowException(new Exception('No translation files found for locale xy_XY'));

        $exporter = $this->buildExporter();

        // copyTranslations must not throw even if exportCatalogues fails for every language
        $exporter->copyTranslations($this->createTheme(), self::CACHE_DIR);

        // mirror must NOT be called when no catalogue could be exported
        $this->fileSystem->expects($this->never())->method('mirror');

        $exporter->copyTranslations($this->createTheme(), self::CACHE_DIR);
    }

    public function testExportSkipsCustomLanguageAndContinues(): void
    {
        $standardLang = $this->createLang('fr_FR');
        $customLang = $this->createLang('xy_XY');

        $this->langRepository->method('findAll')->willReturn([$customLang, $standardLang]);

        $this->translationsExporter
            ->method('exportCatalogues')
            ->willReturnCallback(function (string $themeName, string $locale): string {
                if ($locale === 'xy_XY') {
                    throw new Exception('No translation files found for locale xy_XY');
                }

                return '/tmp/export/' . $themeName . '/' . $locale;
            });

        $this->fileSystem->expects($this->once())->method('mirror');

        $this->buildExporter()->copyTranslations($this->createTheme(), self::CACHE_DIR);
    }

    public function testExportMirrorsCataloguesWhenAllLanguagesSucceed(): void
    {
        $this->langRepository->method('findAll')->willReturn([
            $this->createLang('en_US'),
            $this->createLang('fr_FR'),
        ]);

        $this->translationsExporter
            ->method('exportCatalogues')
            ->willReturnCallback(fn (string $themeName, string $locale) => '/tmp/export/' . $themeName . '/' . $locale);

        $this->fileSystem->expects($this->once())->method('mirror');

        $this->buildExporter()->copyTranslations($this->createTheme(), self::CACHE_DIR);
    }

    public function testExportDoesNotMirrorWhenNoLanguages(): void
    {
        $this->langRepository->method('findAll')->willReturn([]);

        $this->translationsExporter->expects($this->never())->method('exportCatalogues');
        $this->fileSystem->expects($this->never())->method('mirror');

        $this->buildExporter()->copyTranslations($this->createTheme(), self::CACHE_DIR);
    }

    private function buildExporter(): ThemeExporter
    {
        $configuration = $this->configuration;
        $fileSystem = $this->fileSystem;
        $langRepository = $this->langRepository;
        $translationsExporter = $this->translationsExporter;

        return new class($configuration, $fileSystem, $langRepository, $translationsExporter) extends ThemeExporter {
            public function copyTranslations(Theme $theme, $cacheDir): void
            {
                parent::copyTranslations($theme, $cacheDir);
            }
        };
    }

    private function createTheme(): Theme
    {
        return new Theme(
            ['name' => self::THEME_NAME, 'directory' => '/tmp/themes/classic/'],
            '',
            ''
        );
    }

    private function createLang(string $locale): Lang
    {
        $lang = $this->createMock(Lang::class);
        $lang->method('getLocale')->willReturn($locale);

        return $lang;
    }
}
