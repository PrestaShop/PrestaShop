<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\ModuleCatalogueProviderFactory;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\ThemeCatalogueLayersProvider;
use PrestaShop\PrestaShop\Core\Translation\TranslationRepositoryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Test the provider of frontOffice translations
 */
class ThemeCatalogueLayersProviderTestCase extends AbstractCatalogueLayersProviderTestCase
{
    /**
     * @var MockObject|ThemeExtractor
     */
    private $themeExtractor;

    /**
     * @var ThemeRepository
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

        $themeRepository = $this->createMock(ThemeRepository::class);
        $themeRepository
            ->method('getInstanceByName')
            ->willReturn(new Theme([
                'name' => 'fakeThemeForTranslations',
                'directory' => rtrim($this->themesDir, '/') . '/fakeThemeForTranslations',
            ])); // doesn't really matter
        /* @var ThemeRepository $themeRepository */
        $this->themeRepository = $themeRepository;

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
            $this->createMock(ModuleCatalogueProviderFactory::class),
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
                'theme' => Theme::getDefaultTheme(),
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
            $this->createMock(ModuleCatalogueProviderFactory::class),
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
