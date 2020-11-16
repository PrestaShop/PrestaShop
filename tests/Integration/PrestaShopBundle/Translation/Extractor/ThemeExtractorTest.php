<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Translation\Extractor;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShopBundle\Translation\Extractor\ThemeExtractor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Yaml\Yaml;

class ThemeExtractorTest extends KernelTestCase
{
    /**
     * @var object|ThemeExtractor
     */
    private $themeExtractor;

    /**
     * @var string
     */
    private $themeDirectory;

    protected function setUp()
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();
        $this->themeExtractor = $container->get('prestashop.translation.extractor.theme');
        $this->themeDirectory = rtrim($container->getParameter('translations_theme_dir'), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . 'fakeThemeForTranslations';
    }

    public function testItExtractsCatalogueFromFiles()
    {
        $catalogue = $this->themeExtractor->extract($this->getFakeTheme(), 'en-US');

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        $this->assertNotEmpty($catalogue->all(), 'Extracted catalogue must not be empty');

        $this->assertTrue($catalogue->has('Show product', 'ShopThemeProduct'));
        $this->assertTrue($catalogue->has('Do something with cart', 'ShopFakethemefortranslations'));
        $this->assertSame($catalogue->get('Show another product', 'ShopThemeProduct'), 'Show another product');
    }

    protected function tearDown()
    {
        $this->themeExtractor = null;
    }

    private function getFakeTheme(): Theme
    {
        $configFile = $this->themeDirectory . '/config/theme.yml';
        $config = Yaml::parse(file_get_contents($configFile));

        $config['directory'] = $this->themeDirectory;
        $config['physical_uri'] = 'http://my-wonderful-shop.com';

        return new Theme($config);
    }
}
