<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\PrestaShopBundle\Translation\Extractor;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\TranslationToolsBundle\Translation\Dumper\PhpDumper;

/**
 * @group sf
 */
class ThemeExtractorTest extends KernelTestCase
{
    private $container;
    private $filesystem;
    private $themeExtractor;

    private static $rootDir;
    private static $legacyFolder;
    private static $xliffFolder;

    public static function setUpBeforeClass()
    {
        self::$rootDir = __DIR__.'/../../resources/themes/fake-theme';
        self::$legacyFolder = self::$rootDir.'/lang';
        self::$xliffFolder = self::$rootDir.'/translations';
    }

    public function setUp()
    {
        self::bootKernel();

        $this->container = self::$kernel->getContainer();
        $this->filesystem = new Filesystem();
        $this->themeExtractor = $this->container->get('prestashop.translation.theme_extractor');

        $themeProvider = $this->container->get('prestashop.translation.theme_provider');
        $this->themeExtractor->setThemeProvider($themeProvider);
    }

    public function tearDown()
    {
        if (file_exists(self::$legacyFolder)) {
            $this->filesystem->remove(self::$legacyFolder);
        }

        if (is_dir(self::$xliffFolder)) {
            $this->filesystem->remove(self::$xliffFolder);
        }

        $this->themeExtractor = null;
    }

    public function testExtractWithLegacyFormat()
    {
        $this->themeExtractor
            ->addDumper(new PhpDumper())
            ->setFormat('php')
            ->extract($this->getFakeTheme())
        ;

        $legacyTranslationFile = self::$legacyFolder.'/en-US.php';
        $this->assertTrue($this->filesystem->exists($legacyTranslationFile));
    }

    public function testExtractWithXliffFormat()
    {
        $this->themeExtractor
            ->setOutputPath(self::$xliffFolder)
            ->extract($this->getFakeTheme())
        ;

        $isFilesExists = $this->filesystem->exists(array(
            self::$xliffFolder.'/en-US/Shop/Theme/Actions.xlf',
            self::$xliffFolder.'/en-US/Shop/Theme/Cart.xlf',
            self::$xliffFolder.'/en-US/Shop/Theme/Product.xlf',
            self::$xliffFolder.'/en-US/Shop/Foo/Bar.xlf',
        ));

        $this->assertTrue($isFilesExists);
    }

    private function getFakeTheme()
    {
        $configFile = self::$rootDir.'/config/theme.yml';
        $config = Yaml::parse(file_get_contents($configFile));

        $config['directory'] = self::$rootDir;
        $config['physical_uri'] = 'http://my-wonderful-shop.com';

        return new Theme($config);
    }
}
