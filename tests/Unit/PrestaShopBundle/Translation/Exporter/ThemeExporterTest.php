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

namespace Tests\Unit\PrestaShopBundle\Translation\Exporter;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\TranslationToolsBundle\Translation\Dumper\XliffFileDumper;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\Util\Flattenizer;
use PrestaShopBundle\Translation\Exporter\ThemeExporter;
use PrestaShopBundle\Translation\Extractor\ThemeExtractor;
use PrestaShopBundle\Translation\Provider\ThemeProvider;
use PrestaShopBundle\Utils\ZipManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\MessageCatalogue;

class ThemeExporterTest extends TestCase
{
    private const THEME_NAME = 'theme';

    private const LOCALE = 'ab-CD';

    /**
     * @var ThemeExporter
     */
    private $themeExporter;

    /**
     * @var ThemeExtractor
     */
    private $extractorMock;

    /**
     * @var ThemeProvider
     */
    private $providerMock;

    /**
     * @var ThemeRepository
     */
    private $repositoryMock;

    /**
     * @var XliffFileDumper
     */
    private $dumperMock;

    /**
     * @var ZipManager
     */
    private $zipManagerMock;

    /**
     * @var Filesystem
     */
    private $filesystemMock;

    /**
     * @var Finder
     */
    private $finderMock;

    protected function setUp(): void
    {
        $this->mockThemeExtractor();

        $this->mockThemeProvider();

        $this->mockThemeRepository();

        $this->dumperMock = new XliffFileDumper();

        $this->zipManagerMock = $this->getMockBuilder(ZipManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockFilesystem();

        $this->mockFinder();

        $this->themeExporter = new ThemeExporter(
            $this->extractorMock,
            $this->providerMock,
            $this->repositoryMock,
            $this->dumperMock,
            $this->zipManagerMock,
            new Filesystem()
        );

        $this->themeExporter->finder = $this->finderMock;
        $cacheDir = dirname(__DIR__, 5) . '/var/cache/test';
        $this->themeExporter->exportDir = $cacheDir . '/export';
        $this->themeExporter->cacheDir = $cacheDir;
    }

    public function testCreateZipArchive(): void
    {
        $this->themeExporter->createZipArchive(self::THEME_NAME, self::LOCALE);

        $loader = new XliffFileLoader();
        $archiveContentsParentDir = $this->themeExporter->exportDir . '/' . self::THEME_NAME . '/' . self::LOCALE;

        $finder = Finder::create();
        $catalogue = new MessageCatalogue(self::LOCALE, []);

        foreach ($finder->in($archiveContentsParentDir)->files() as $file) {
            $catalogue->addCatalogue(
                $loader->load(
                    $file->getPathname(),
                    self::LOCALE,
                    $file->getBasename('.' . $file->getExtension())
                )
            );
        }

        $messages = $catalogue->all();
        $domain = 'ShopActions.' . self::LOCALE;
        $this->assertArrayHasKey($domain, $messages);

        $this->assertArrayHasKey('Edit Product', $messages[$domain]);
        $this->assertArrayHasKey('Add Product', $messages[$domain]);
        $this->assertArrayHasKey('Delete Product', $messages[$domain]);

        $this->assertArrayHasKey('Override Me', $messages[$domain]);
        $this->assertSame('Overridden', $messages[$domain]['Override Me']);

        $this->assertArrayHasKey('Override Me Twice', $messages[$domain]);
        $this->assertSame('Overridden Twice', $messages[$domain]['Override Me Twice']);
    }

    protected function mockThemeExtractor(): void
    {
        $this->extractorMock = $this->getMockBuilder(ThemeExtractor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->extractorMock->method('setOutputPath')
            ->willReturn($this->extractorMock);
    }

    protected function mockThemeRepository(): void
    {
        $this->repositoryMock = $this->getMockBuilder(ThemeRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->repositoryMock->method('getInstanceByName')
            ->willReturn(new Theme([
                'directory' => '',
                'name' => self::THEME_NAME,
            ]));
    }

    protected function mockFilesystem(): void
    {
        $this->filesystemMock = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->filesystemMock->method('mkdir')
            ->willReturn(null);

        Flattenizer::$filesystem = $this->filesystemMock;
    }

    protected function mockFinder(): void
    {
        $this->finderMock = $this->getMockBuilder(Finder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->finderMock->method('in')
            ->willReturn($this->finderMock);

        $this->finderMock->method('files')
            ->willReturn([]);

        Flattenizer::$finder = $this->finderMock;
    }

    protected function mockThemeProvider(): void
    {
        $this->providerMock = $this->getMockBuilder(ThemeProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->providerMock->method('getCatalogueFromPaths')
            ->willReturn(new MessageCatalogue(
                self::LOCALE,
                [
                    'ShopActions.' . self::LOCALE => [
                        'Add Product' => 'Add',
                        'Override Me' => '',
                        'Override Me Twice' => '',
                    ],
                ]
            ));

        $this->providerMock->method('getThemeCatalogue')
            ->willReturn(new MessageCatalogue(
                self::LOCALE,
                [
                    'ShopActions.' . self::LOCALE => [
                        'Edit Product' => 'Edit',
                        'Override Me' => 'Overridden',
                        'Override Me Twice' => 'Overridden Once',
                    ],
                ]
            ));

        $this->providerMock->method('getDatabaseCatalogue')
            ->willReturn(new MessageCatalogue(
                self::LOCALE,
                [
                    'ShopActions' => [
                        'Delete Product' => 'Delete',
                        'Override Me Twice' => 'Overridden Twice',
                    ],
                ]
            ));
    }
}
