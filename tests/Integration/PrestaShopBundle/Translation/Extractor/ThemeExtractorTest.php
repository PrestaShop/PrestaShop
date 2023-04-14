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

    private function getFakeTheme(): Theme
    {
        $configFile = self::$rootDir . '/config/theme.yml';
        $config = Yaml::parse(file_get_contents($configFile));

        $config['directory'] = self::$rootDir;
        $config['physical_uri'] = 'http://my-wonderful-shop.com';

        return new Theme($config);
    }
}
