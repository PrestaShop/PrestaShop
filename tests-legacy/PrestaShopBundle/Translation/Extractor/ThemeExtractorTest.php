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

namespace LegacyTests\PrestaShopBundle\Translation\Extractor;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\TranslationToolsBundle\Translation\Dumper\PhpDumper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Yaml\Yaml;

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

    protected function setUp()
    {
        self::bootKernel();

        $this->container = self::$kernel->getContainer();
        $this->filesystem = new Filesystem();
        $this->themeExtractor = $this->container->get('prestashop.translation.theme_extractor');
    }

    protected function tearDown()
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
        $catalogue = $this->themeExtractor
            ->extract($this->getFakeTheme());

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);
    }

    public function testExtractWithXliffFormat()
    {
        $catalogue = $this->themeExtractor
            ->extract($this->getFakeTheme());

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);
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
