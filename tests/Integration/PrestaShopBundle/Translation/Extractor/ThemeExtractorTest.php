<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Translation\Extractor;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\TranslationToolsBundle\Translation\Dumper\PhpDumper;
use PrestaShopBundle\Translation\Extractor\ThemeExtractor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class ThemeExtractorTest extends KernelTestCase
{
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var ThemeExtractor|null
     */
    private $themeExtractor;

    /**
     * @var string
     */
    private static $rootDir;
    /**
     * @var string
     */
    private static $legacyFolder;
    /**
     * @var string
     */
    private static $xliffFolder;

    public static function setUpBeforeClass(): void
    {
        self::$rootDir = dirname(__DIR__, 4) . '/Resources/themes/fake-theme';
        self::$legacyFolder = self::$rootDir . '/lang';
        self::$xliffFolder = self::$rootDir . '/translations';
    }

    protected function setUp(): void
    {
        self::bootKernel();

        $this->filesystem = new Filesystem();
        $this->themeExtractor = self::getContainer()->get('prestashop.translation.theme_extractor');

        $themeProvider = self::getContainer()->get('prestashop.translation.theme_provider');
        $this->themeExtractor->setThemeProvider($themeProvider);
    }

    protected function tearDown(): void
    {
        if (file_exists(self::$legacyFolder)) {
            $this->filesystem->remove(self::$legacyFolder);
        }

        if (is_dir(self::$xliffFolder)) {
            $this->filesystem->remove(self::$xliffFolder);
        }

        $this->themeExtractor = null;

        parent::tearDown();
    }

    public function testExtractWithLegacyFormat(): void
    {
        $this->themeExtractor
            ->addDumper(new PhpDumper())
            ->setFormat('php')
            ->extract($this->getFakeTheme());

        $legacyTranslationFile = self::$legacyFolder . '/en-US.php';
        $this->assertTrue($this->filesystem->exists($legacyTranslationFile));
    }

    public function testExtractWithXliffFormat(): void
    {
        $this->themeExtractor
            ->setOutputPath(self::$xliffFolder)
            ->extract($this->getFakeTheme());

        $isFilesExists = $this->filesystem->exists([
            self::$xliffFolder . '/en-US/Shop/Theme/Actions.xlf',
            self::$xliffFolder . '/en-US/Shop/Theme/Cart.xlf',
            self::$xliffFolder . '/en-US/Shop/Theme/Product.xlf',
            self::$xliffFolder . '/en-US/Shop/Foo/Bar.xlf',
        ]);

        $this->assertTrue($isFilesExists);
    }

    /**
     * A theme may override a third-party module's template, and strings added in that override have to
     * reach the translation catalogue under the module's own domain — otherwise they cannot be
     * translated anywhere. Reported as #13392.
     */
    public function testExtractsStringsFromAModuleTemplateOverriddenByTheTheme(): void
    {
        $this->themeExtractor
            ->setOutputPath(self::$xliffFolder)
            ->extract($this->getFakeTheme());

        $this->assertTrue(
            $this->filesystem->exists(self::$xliffFolder . '/en-US/Modules/Fakemodule/Shop.xlf'),
            'The theme override of a module template should be extracted under the module domain'
        );
    }

    private function getFakeTheme(): Theme
    {
        $configFile = self::$rootDir . '/config/theme.yml';
        $config = Yaml::parse(file_get_contents($configFile));

        $config['directory'] = self::$rootDir;
        $config['physical_uri'] = 'http://my-wonderful-shop.com';

        return new Theme($config);
    }
}
