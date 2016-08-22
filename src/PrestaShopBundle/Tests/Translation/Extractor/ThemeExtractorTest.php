<?php
/**
 * 2007-2016 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2016 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Tests\Translation\Extractor;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;

class ThemeExtractorTest extends KernelTestCase
{
    const THEME_ROOT_DIR = __DIR__.'/../../resources/themes/fake-theme';
    const LEGACY_FOLDER = self::THEME_ROOT_DIR.'/lang';
    const XLIFF_FOLDER = self::THEME_ROOT_DIR.'/translations';
    
    private $container;
    private $filesystem;
    private $themeExtractor;

    public function setUp()
    {
        self::bootKernel();

        $this->container = self::$kernel->getContainer();
        $this->filesystem = new Filesystem();
        $this->themeExtractor = $this->container->get('prestashop.translations.theme_extractor');
    }

    public function tearDown()
    {
        if (file_exists(self::LEGACY_FOLDER)) {
            $this->filesystem->remove(self::LEGACY_FOLDER);
        }

        if (is_dir(self::THEME_ROOT_DIR)) {
            $this->filesystem->remove(self::XLIFF_FOLDER);
        }
    }

    public function testExtractWithLegacyFormat()
    {
        $translations = $this->themeExtractor->extract($this->getFakeTheme(), 'array');
        $legacyTranslationFile = self::LEGACY_FOLDER.'/en-US.php';
        $this->assertTrue($this->filesystem->exists($legacyTranslationFile));
    }

    public function testExtractWithXliffFormat()
    {
        $translations = $this->themeExtractor->extract($this->getFakeTheme());
        $isFilesExists = $this->filesystem->exists(array(
            self::XLIFF_FOLDER.'/en-US/Shop/Theme/Actions.xlf',
            self::XLIFF_FOLDER.'/en-US/Shop/Theme/Cart.xlf',
            self::XLIFF_FOLDER.'/en-US/Shop/Theme/Product.xlf',
            self::XLIFF_FOLDER.'/en-US/Shop/Foo/Bar.xlf',
        ));

        $this->assertTrue($isFilesExists);
    }

    private function getFakeTheme()
    {
        $rootThemeDir = self::THEME_ROOT_DIR;
        $configFile = $rootThemeDir.'/config/theme.yml';
        $config = Yaml::parse(file_get_contents($configFile));

        $config['directory'] = $rootThemeDir;
        $config['physical_uri'] = 'http://my-wonderful-shop.com';

        return new Theme($config);
    }
}
