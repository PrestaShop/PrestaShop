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

namespace Tests\Integration\Core\Translation\Provider;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\Translation\Provider\CoreCatalogueLayersProvider;
use PrestaShop\PrestaShop\Core\Translation\Provider\Definition\FrontofficeProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Provider\Definition\ThemeProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Provider\ThemeCatalogueLayersProvider;
use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractorInterface;
use PrestaShopBundle\Translation\Provider\ThemeProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Test the provider of frontOffice translations
 */
class ThemeCatalogueLayersProviderTest extends KernelTestCase
{
    /**
     * @var string
     */
    private $translationsDir;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|LegacyModuleExtractorInterface
     */
    private $themeExtractor;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|LoaderInterface
     */
    private $themeRepository;
    /**
     * @var mixed
     */
    private $modulesDir;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var mixed
     */
    private $themesDir;

    public function setUp()
    {
        self::bootKernel();
        /*
         * The translation directory actually contains these files for locale = fr-FR
         * - AdminActions.fr-FR.xlf
         * - EmailsBody.fr-FR.xlf
         * - EmailsSubject.fr-FR.xlf
         * - messages.fr-FR.xlf
         * - ModulesCheckpaymentAdmin.fr-FR.xlf
         * - ModulesCheckpaymentShop.fr-FR.xlf
         * - ModulesWirepaymentAdmin.fr-FR.xlf
         * - ModulesWirepaymentShop.fr-FR.xlf
         * - ShopNotificationsWarning.fr-FR.xlf
         */
        $this->translationsDir = self::$kernel->getContainer()->getParameter('test_translations_dir');
        $this->modulesDir = self::$kernel->getContainer()->getParameter('test_translations_dir');
        $this->themesDir = self::$kernel->getContainer()->getParameter('translations_theme_dir');
        $this->themeExtractor = self::$kernel->getContainer()->get('prestashop.translation.theme_extractor');

        $this->themeRepository = $this->createMock(ThemeRepository::class);
        $this->themeRepository
            ->method('getInstanceByName')
            ->willReturn(new Theme([
                'name' => 'fakeThemeForTranslations',
                'directory' => rtrim($this->themesDir, '/') . '/fakeThemeForTranslations',
            ])); //doesn't really matter
        $this->filesystem = new Filesystem();
    }

    /**
     * Test it loads a XLIFF catalogue from the locale's `translations` directory
     */
    public function testItLoadsCatalogueFromXliffFilesInLocaleDirectory(): void
    {
        $databaseTranslationLoader = new MockDatabaseTranslationLoader([], $this->createMock(EntityManagerInterface::class));
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

        // load catalogue from translations/fr-FR
        $catalogue = $provider->getFileTranslatedCatalogue('fr-FR');

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $domains = $catalogue->getDomains();
        sort($domains);

        // verify all catalogues are loaded
        // this is a merge of core Shop domains and Theme's domains
        $this->assertSame([
            'ModulesCheckpaymentShop',
            'ModulesWirepaymentShop',
            'ShopNotificationsWarning',
            'ShopTheme',
            'ShopThemeCustomeraccount',
        ], $domains);

        // verify that the catalogues are complete
        $this->assertCount(19, $messages['ModulesCheckpaymentShop']);
        $this->assertCount(15, $messages['ModulesWirepaymentShop']);
        $this->assertCount(8, $messages['ShopNotificationsWarning']);
        $this->assertCount(64, $messages['ShopTheme']);
        $this->assertCount(83, $messages['ShopThemeCustomeraccount']);

        $this->assertSame('Envoyez votre chèque à cette adresse', $catalogue->get('Send your check to this address', 'ModulesCheckpaymentShop'));
        $this->assertSame('La page que vous cherchez n\'a pas été trouvée.', $catalogue->get('The page you are looking for was not found.', 'ShopTheme'));
    }

    /**
     * Test it loads a default catalogue from the `translations` default directory
     */
    public function testItExtractsDefaultCatalogueFromThemeFiles(): void
    {
        $databaseTranslationLoader = new MockDatabaseTranslationLoader([], $this->createMock(EntityManagerInterface::class));

        /**
         * @TODO: In the next PR, break that silly dependency
         */
        $themeProvider = new ThemeProvider(
            $databaseTranslationLoader,
            $this->themesDir
        );
        $themeProvider->themeResourcesDirectory = $this->themesDir;
        $themeProvider->defaultTranslationDir = $this->translationsDir;
        $themeProvider->filesystem = $this->filesystem;
        $themeProvider->themeRepository = $this->themeRepository;
        $themeProvider->themeExtractor = $this->themeExtractor;
        $this->themeExtractor->setThemeProvider($themeProvider);
        $this->themeExtractor->setOutputPath('/tmp/ThemeExtract');

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

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $domains = $catalogue->getDomains();
        sort($domains);

        // verify all catalogues are loaded
        // The domains from smarty templates in tests/Resources/themes/fakeThemeForTranslations
        $this->assertSame([
            'ShopFooBar',
            'ShopThemeActions',
            'ShopThemeCart',
            'ShopThemeProduct',
        ], $domains);

        // verify that the catalogues are complete
        $this->assertCount(1, $messages['ShopFooBar']);
        $this->assertCount(1, $messages['ShopThemeActions']);
        $this->assertCount(1, $messages['ShopThemeCart']);
        $this->assertCount(1, $messages['ShopThemeProduct']);

        $this->assertSame('Refresh', $catalogue->get('Refresh', 'ShopThemeActions'));
        $this->assertSame('Apply cart', $catalogue->get('Apply cart', 'ShopThemeCart'));
        $this->assertSame('Show product', $catalogue->get('Show product', 'ShopThemeProduct'));
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

        $databaseTranslationLoader = new MockDatabaseTranslationLoader($databaseContent, $this->createMock(EntityManagerInterface::class));
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

        // load catalogue from database translations
        $catalogue = $provider->getUserTranslatedCatalogue('fr-FR');

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

        $databaseTranslationLoader = new MockDatabaseTranslationLoader($databaseContent, $this->createMock(EntityManagerInterface::class));
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

        // load catalogue from database translations
        $catalogue = $provider->getUserTranslatedCatalogue('fr-FR');

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $domains = $catalogue->getDomains();
        sort($domains);

        // If the theme name is null, the translations which have theme = 'classic' are taken
        $this->assertSame([
            'ShopThemeActions',
            'ShopThemeCart',
        ], $domains);

        $this->assertSame('Install Traduction customisée', $catalogue->get('Install', 'ShopThemeActions'));
        $this->assertSame('Uninstall Traduction customisée', $catalogue->get('Uninstall', 'ShopThemeCart'));
    }
}
