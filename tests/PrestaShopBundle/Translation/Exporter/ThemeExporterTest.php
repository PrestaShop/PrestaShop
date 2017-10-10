<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\PrestaShopBundle\Translation\Exporter;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\TranslationToolsBundle\Translation\Dumper\XliffFileDumper;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\Util\Flattenizer;
use PrestaShopBundle\Translation\Exporter\ThemeExporter;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\MessageCatalogue;
use PHPUnit\Framework\TestCase;

/**
 * @group sf
 */
class ThemeExporterTest extends TestCase
{
    const THEME_NAME = 'theme';

    const LOCALE = 'ab-CD';

    /**
     * @var ThemeExporter
     */
    private $themeExporter;

    private $extractorMock;

    private $providerMock;

    private $repositoryMock;

    private $dumperMock;

    private $zipManagerMock;

    private $filesystemMock;

    private $finderMock;

    public function setUp()
    {
        $this->mockThemeExtractor();

        $this->mockThemeProvider();

        $this->mockThemeRepository();

        $this->dumperMock = new XliffFileDumper();

        $this->zipManagerMock = $this->getMockBuilder('\PrestaShopBundle\Utils\ZipManager')
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
        $cacheDir = dirname(__FILE__) . '/' .
            str_repeat('../', 5) .
            'app/cache/test';
        $this->themeExporter->exportDir = $cacheDir . '/export';
        $this->themeExporter->cacheDir = $cacheDir;
    }

    public function testCreateZipArchive()
    {
        $this->themeExporter->createZipArchive(self::THEME_NAME, self::LOCALE);

        $loader = new XliffFileLoader();
        $archiveContentsParentDir = $this->themeExporter->exportDir . '/' . self::THEME_NAME . '/' . self::LOCALE;

        $finder = Finder::create();
        $catalogue = new MessageCatalogue(self::LOCALE, array());

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

    protected function mockThemeExtractor()
    {
        $this->extractorMock = $this->getMockBuilder('\PrestaShopBundle\Translation\Extractor\ThemeExtractor')
            ->disableOriginalConstructor()
            ->getMock();

        $this->extractorMock->method('setOutputPath')
            ->willReturn($this->extractorMock);
    }

    protected function mockThemeRepository()
    {
        $this->repositoryMock = $this->getMockBuilder('\PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repositoryMock->method('getInstanceByName')
            ->willReturn(new Theme(array(
                'directory' => '',
                'name' => self::THEME_NAME
            )));
    }

    protected function mockFilesystem()
    {
        $this->filesystemMock = $this->getMockBuilder('\Symfony\Component\Filesystem\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();

        $this->filesystemMock->method('mkdir')
            ->willReturn(null);

        Flattenizer::$filesystem = $this->filesystemMock;
    }

    protected function mockFinder()
    {
        $this->finderMock = $this->getMockBuilder('\Symfony\Component\Finder\Finder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->finderMock->method('in')
            ->willReturn($this->finderMock);

        $this->finderMock->method('files')
            ->willReturn(array());

        Flattenizer::$finder = $this->finderMock;
    }

    protected function mockThemeProvider()
    {
        $this->providerMock = $this->getMockBuilder('\PrestaShopBundle\Translation\Provider\ThemeProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $this->providerMock->method('getCatalogueFromPaths')
            ->willReturn(new MessageCatalogue(
                self::LOCALE,
                array(
                    'ShopActions.' . self::LOCALE => array(
                        'Add Product' => 'Add',
                        'Override Me' => '',
                        'Override Me Twice' => '',
                    )
                )
            ));

        $this->providerMock->method('getThemeCatalogue')
            ->willReturn(new MessageCatalogue(
                self::LOCALE,
                array(
                    'ShopActions.' . self::LOCALE => array(
                        'Edit Product' => 'Edit',
                        'Override Me' => 'Overridden',
                        'Override Me Twice' => 'Overridden Once',
                    )
                )
            ))
        ;

        $this->providerMock->method('getDatabaseCatalogue')
            ->willReturn(new MessageCatalogue(
                self::LOCALE,
                array(
                    'ShopActions' => array(
                        'Delete Product' => 'Delete',
                        'Override Me Twice' => 'Overridden Twice',
                    )
                )
            ))
        ;
    }
}
