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

namespace Tests\Integration\Core\Translation\Storage\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Extractor\ThemeExtractor;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\CoreCatalogueLayersProvider;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\FrontofficeProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ThemeProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\ThemeCatalogueLayersProvider;
use PrestaShop\PrestaShop\Core\Translation\TranslationRepositoryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Test the provider of frontOffice translations
 */
class ThemeCatalogueLayersProviderTest extends AbstractCatalogueLayersProviderTest
{
    /**
     * @var MockObject|ThemeExtractor
     */
    private $themeExtractor;

    /**
     * @var MockObject|LoaderInterface
     */
    private $themeRepository;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var mixed
     */
    private $themesDir;

    public function setUp(): void
    {
        parent::setUp();

        $this->themesDir = self::$kernel->getContainer()->getParameter('translations_theme_dir');
        $this->themeExtractor = self::$kernel->getContainer()->get('prestashop.translation.extractor.theme');

        $this->themeRepository = $this->createMock(ThemeRepository::class);
        $this->themeRepository
            ->method('getInstanceByName')
            ->willReturn(new Theme([
                'name' => 'fakeThemeForTranslations',
                'directory' => rtrim($this->themesDir, '/') . '/fakeThemeForTranslations',
            ])); // doesn't really matter
        $this->filesystem = new Filesystem();
    }

    /**
     * Test it loads a XLIFF catalogue from the locale's `translations` directory
     */
    public function testItLoadsCatalogueFromXliffFilesInLocaleDirectory(): void
    {
        // load catalogue from translations/fr-FR
        $catalogue = $this->getFileTranslatedCatalogue('fr-FR');

        $expected = [
            'ShopNotificationsWarning' => [
                'count' => 8,
                'translations' => [],
            ],
            'ShopTheme' => [
                'count' => 64,
                'translations' => [
                    'The page you are looking for was not found.' => 'La page que vous cherchez n\'a pas été trouvée.',
                ],
            ],
            'ShopThemeCustomeraccount' => [
                'count' => 83,
                'translations' => [],
            ],
        ];

        // verify all catalogues are loaded
        $this->assertResultIsAsExpected($expected, $catalogue);
    }

    /**
     * Test it loads a default catalogue from the `translations` default directory
     */
    public function testItExtractsDefaultCatalogueFromThemeFiles(): void
    {
        $databaseTranslationLoader = new MockDatabaseTranslationLoader(
            [],
            $this->createMock(LanguageRepositoryInterface::class),
            $this->createMock(TranslationRepositoryInterface::class)
        );

        $providerDefinition = new ThemeProviderDefinition('fakeThemeForTranslations');
        $coreFrontProviderDefinition = new FrontofficeProviderDefinition();
        $coreFrontProvider = new CoreCatalogueLayersProvider(
            $databaseTranslationLoader,
            $this->translationsDir,
            $coreFrontProviderDefinition->getFilenameFilters(),
            $coreFrontProviderDefinition->getTranslationDomains()
        );

        $provider = new ThemeCatalogueLayersProvider(
            $coreFrontProvider,
            $databaseTranslationLoader,
            $this->themeExtractor,
            $this->themeRepository,
            $this->filesystem,
            $this->themesDir,
            $providerDefinition->getThemeName()
        );

        // load catalogue from translations/default
        $catalogue = $provider->getDefaultCatalogue('fr-FR');

        // The domains are from smarty templates in tests/Resources/themes/fakeThemeForTranslations
        $expected = [
            'ShopFooBar' => [
                'count' => 1,
                'translations' => [],
            ],
            'ShopThemeActions' => [
                'count' => 1,
                'translations' => [
                    'Refresh' => 'Refresh',
                ],
            ],
            'ShopThemeCart' => [
                'count' => 1,
                'translations' => [
                    'Apply cart' => 'Apply cart',
                ],
            ],
            'ShopThemeProduct' => [
                'count' => 1,
                'translations' => [
                    'Show product' => 'Show product',
                ],
            ],
        ];

        // verify all catalogues are loaded
        $this->assertResultIsAsExpected($expected, $catalogue);
    }

    public function testItDoesntLoadsCustomizedTranslationsWithThemeNotDefinedOrDifferentFromDatabase(): void
    {
        $databaseContent = [
            [
                'lang' => 'fr-FR',
                'key' => 'Uninstall',
                'translation' => 'Uninstall Traduction customisée',
                'domain' => 'ShopThemeCart',
                'theme' => null,
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Install',
                'translation' => 'Install Traduction customisée',
                'domain' => 'ShopThemeActions',
                'theme' => 'classic',
            ],
        ];

        // load catalogue from database translations
        $catalogue = $this->getUserTranslatedCatalogue('fr-FR', $databaseContent);

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $domains = $catalogue->getDomains();
        sort($domains);

        // If the theme name is null, the translations which have theme = 'classic' are taken
        $this->assertEmpty($domains);
        $this->assertEmpty($messages);
    }

    public function testItLoadsCustomizedTranslationsWithThemeDefinedFromDatabase(): void
    {
        $databaseContent = [
            [
                'lang' => 'fr-FR',
                'key' => 'Uninstall',
                'translation' => 'Uninstall Traduction customisée',
                'domain' => 'ShopThemeCart',
                'theme' => 'fakeThemeForTranslations',
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Install',
                'translation' => 'Install Traduction customisée',
                'domain' => 'ShopThemeActions',
                'theme' => 'fakeThemeForTranslations',
            ],
        ];

        // load catalogue from database translations
        $catalogue = $this->getUserTranslatedCatalogue('fr-FR', $databaseContent);

        $expected = [
            'ShopThemeActions' => [
                'count' => 1,
                'translations' => [
                    'Install' => 'Install Traduction customisée',
                ],
            ],
            'ShopThemeCart' => [
                'count' => 1,
                'translations' => [
                    'Uninstall' => 'Uninstall Traduction customisée',
                ],
            ],
        ];

        // verify all catalogues are loaded
        $this->assertResultIsAsExpected($expected, $catalogue);
    }

    /**
     * @param array $databaseContent
     *
     * @return ThemeCatalogueLayersProvider
     */
    protected function getProvider(array $databaseContent = []): ThemeCatalogueLayersProvider
    {
        $databaseTranslationLoader = new MockDatabaseTranslationLoader(
            $databaseContent,
            $this->createMock(LanguageRepositoryInterface::class),
            $this->createMock(TranslationRepositoryInterface::class)
        );
        $providerDefinition = new ThemeProviderDefinition('fakeThemeForTranslations');
        $coreFrontProviderDefinition = new FrontofficeProviderDefinition();
        $coreFrontProvider = new CoreCatalogueLayersProvider(
            $databaseTranslationLoader,
            $this->translationsDir,
            $coreFrontProviderDefinition->getFilenameFilters(),
            $coreFrontProviderDefinition->getTranslationDomains()
        );

        return new ThemeCatalogueLayersProvider(
            $coreFrontProvider,
            $databaseTranslationLoader,
            $this->themeExtractor,
            $this->themeRepository,
            $this->filesystem,
            $this->themesDir,
            $providerDefinition->getThemeName()
        );
    }
}
