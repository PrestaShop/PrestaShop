<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        $finderMock = $this->getMockBuilder(Finder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $finderMock->method('in')
            ->willReturn($finderMock);

        Flattenizer::$finder = $finderMock;
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
