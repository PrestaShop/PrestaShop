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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\PrestaShopBundle\Translation\Provider;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShopBundle\Translation\Extractor\ThemeExtractorInterface;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use PrestaShopBundle\Translation\Provider\ThemeProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Test the provider of theme translations
 */
class ThemeProviderTest extends KernelTestCase
{
    const THEMES_DIR = __DIR__ . '/../../../../Resources/themes/';

    /**
     * @var string
     */
    private $themeName;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface|null
     */
    private $container;

    /**
     * @var string
     */
    private $cacheDir;
    /**
     * @var string
     */
    private $configDir;

    /**
     * @var Filesystem
     */
    private $filesystem;

    protected function setUp()
    {
        self::bootKernel();
        $this->container = static::$kernel->getContainer();
        $this->themeName = 'fakeThemeForTranslations';
        $this->cacheDir = $this->container->getParameter('themes_translations_dir');
        $this->configDir = $this->container->getParameter('kernel.cache_dir') . '/themes-config/';
        $this->filesystem = $this->container->get('filesystem');
    }

    protected function tearDown()
    {
        // clean up
        $this->filesystem->remove([
            $this->cacheDir . '*',
            $this->configDir,
        ]);
    }

    /**
     * Test it loads a XLIFF catalogue from the theme's `translations` directory
     */
    public function testItLoadsCatalogueFromXliffFilesInThemeDirectory()
    {
        $provider = new ThemeProvider(
            $this->createMock(DatabaseTranslationLoader::class),
            $this->createMock(ThemeExtractorInterface::class),
            $this->buildThemeRepository(),
            $this->filesystem,
            self::THEMES_DIR,
            $this->container->getParameter('translations_dir')
        );

        $provider->setThemeName($this->themeName);

        // load catalogue from Xliff files within the theme
        $catalogue = $provider->getXliffCatalogue();

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $this->assertArrayHasKey('ShopTheme', $messages);
        $this->assertArrayHasKey('ShopThemeCustomeraccount', $messages);

        $this->assertCount(29, $catalogue->all('ShopTheme'));
        $this->assertSame('Contact us!', $catalogue->get('Contact us', 'ShopTheme'));
    }

    /**
     * Test it extracts the default catalogue from the theme's templates
     *
     * @param ThemeExtractorInterface $mockExtractor
     * @param bool $emptyCatalogue
     * @param array[] $expectedCatalogue
     *
     * @dataProvider provideFixturesForExtractDefaultCatalogue
     */
    public function testItExtractsDefaultThemeFromFiles(ThemeExtractorInterface $mockExtractor, $emptyCatalogue, array $expectedCatalogue)
    {
        $provider = new ThemeProvider(
            $this->createMock(DatabaseTranslationLoader::class),
            // note: extractor is mocked because actual extraction is already covered by its own test
            $mockExtractor,
            $this->buildThemeRepository(),
            $this->filesystem,
            self::THEMES_DIR,
            $this->container->getParameter('translations_dir')
        );

        $provider->setThemeName($this->themeName);

        // load catalogue from Xliff files within the theme
        $catalogue = $provider->getDefaultCatalogue($emptyCatalogue);

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        $catalogueArray = $catalogue->all();
        $this->assertSame($expectedCatalogue, $catalogueArray);
    }

    public function provideFixturesForExtractDefaultCatalogue()
    {
        $extractedMessages = [
            'SomeDomain' => [
                'Foo' => 'Foo',
                'Foo bar' => 'Foo bar',
            ],
            'SomeOtherDomain' => [
                'Barbaz' => 'Barbaz',
            ],
        ];

        $extractedCatalogue = $this->buildCatalogueFromMessages($extractedMessages);

        $emptyCatalogue = [
            'SomeDomain' => [
                'Foo' => '',
                'Foo bar' => '',
            ],
            'SomeOtherDomain' => [
                'Barbaz' => '',
            ],
        ];

        return  [
            'not empty catalogue' => [
                $this->buildThemeExtractorMock($extractedCatalogue),
                false,
                $extractedMessages,
            ],
            'empty catalogue' => [
                $this->buildThemeExtractorMock($extractedCatalogue),
                true,
                $emptyCatalogue,
            ],
        ];
    }

    /**
     * @return ThemeRepository
     */
    private function buildThemeRepository()
    {
        $configuration = $this->createMock(Configuration::class);

        $configuration
            ->method('get')
            ->willReturnCallback(function ($param) {
                $configs = [
                    '_PS_ALL_THEMES_DIR_' => self::THEMES_DIR,
                    '_PS_CONFIG_DIR_' => $this->configDir,
                ];

                return isset($configs[$param]) ? $configs[$param] : null;
            });

        $shop = $this->container->get('prestashop.adapter.legacy.context')->getContext()->shop;

        return new ThemeRepository($configuration, $this->container->get('filesystem'), $shop);
    }

    /**
     * @param MessageCatalogue $catalogueToReturn
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|ThemeExtractorInterface
     */
    private function buildThemeExtractorMock(MessageCatalogue $catalogueToReturn)
    {
        $mock = $this->createMock(ThemeExtractorInterface::class);

        $mock->expects($this->once())
            ->method('extract')
            ->willReturn($catalogueToReturn);

        return $mock;
    }

    /**
     * @param array $messages
     *
     * @return MessageCatalogue
     */
    private function buildCatalogueFromMessages(array $messages)
    {
        $catalogue = new MessageCatalogue(ThemeExtractorInterface::DEFAULT_LOCALE);
        foreach ($messages as $domain => $domainMessages) {
            $catalogue->add($domainMessages, $domain);
        }

        return $catalogue;
    }
}
