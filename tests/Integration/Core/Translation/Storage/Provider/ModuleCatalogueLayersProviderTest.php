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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Tests\Integration\Core\Translation\Storage\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Extractor\LegacyModuleExtractor;
use PrestaShop\PrestaShop\Core\Translation\Storage\Extractor\LegacyModuleExtractorInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Loader\LegacyFileLoader;
use PrestaShop\PrestaShop\Core\Translation\Storage\Loader\LegacyFileReader;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ModuleProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\ModuleCatalogueLayersProvider;
use PrestaShop\PrestaShop\Core\Translation\TranslationRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Test the provider of frontOffice translations
 */
class ModuleCatalogueLayersProviderTest extends KernelTestCase
{
    /**
     * @var string
     */
    private $translationsDir;

    /**
     * @var MockObject|LegacyModuleExtractorInterface
     */
    private $legacyModuleExtractor;

    /**
     * @var MockObject|LoaderInterface
     */
    private $legacyFileLoader;

    /**
     * @var mixed
     */
    private $modulesDir;

    /**
     * @var array<int, string>
     */
    private $moduleExtractorExcludedDirs = ['vendor', 'lib', 'tests'];

    public function setUp(): void
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
         * - ModulesXlftranslatedmoduleAdmin.fr-FR.xlf
         * - ShopNotificationsWarning.fr-FR.xlf
         */
        $this->translationsDir = self::$kernel->getContainer()->getParameter('test_translations_dir');
        $this->modulesDir = self::$kernel->getContainer()->getParameter('translations_modules_dir');

        $this->legacyModuleExtractor = $this->createMock(LegacyModuleExtractorInterface::class);
        $this->legacyFileLoader = $this->createMock(LoaderInterface::class);
    }

    /**
     * Test it loads a XLIFF catalogue from the locale's `translations` directory
     */
    public function testItLoadsCatalogueFromXliffFilesInLocaleDirectory(): void
    {
        // load catalogue from translations/fr-FR
        $catalogue = $this->getProvider('Checkpayment')->getFileTranslatedCatalogue('fr-FR');

        $expected = [
            'ModulesCheckpaymentAdmin' => [
                'count' => 15,
                'translations' => [
                    'No currency has been set for this module.' => 'Aucune devise disponible pour ce module',
                ],
            ],
            'ModulesCheckpaymentShop' => [
                'count' => 19,
                'translations' => [
                    'Send your check to this address' => 'Envoyez votre chèque à cette adresse',
                ],
            ],
        ];

        // verify all catalogues are loaded
        $this->assertResultIsAsExpected($expected, $catalogue);
    }

    /**
     * Test it loads a XLIFF catalogue from the module's locale `translations` directory
     */
    public function testItLoadsCatalogueFromXliffFilesFromModuleDirectory(): void
    {
        // load catalogue from translations/fr-FR
        $catalogue = $this->getProvider('xlftranslatedmodule')->getFileTranslatedCatalogue('fr-FR');

        $expected = [
            'ModulesXlftranslatedmoduleAdmin' => [
                'count' => 2,
                'translations' => [
                    'Generates a .CSV file for mass mailings' => 'Ceci est la traduction provenant des fichiers du module',
                    'Some default translation from module files' => 'Traduction par défaut du module traduite dans le module',
                ],
            ],
        ];

        // verify all catalogues are loaded
        $this->assertResultIsAsExpected($expected, $catalogue);
    }

    /**
     * Test it extracts a XLIFF catalogue from the module's templates id locale's `translations` directory does not exist
     */
    public function testItLoadsCatalogueFromXliffWhenLocaleDirectoryNotFound(): void
    {
        $converter = self::$kernel->getContainer()->get('prestashop.core.translation.locale.converter');
        $legacyFileReader = new LegacyFileReader($converter);
        $legacyFileLoader = new LegacyFileLoader($legacyFileReader);

        $phpExtractor = self::$kernel->getContainer()->get('prestashop.translation.extractor.php');
        $smartyExtractor = self::$kernel->getContainer()->get('prestashop.translation.extractor.smarty.legacy');
        $twigExtractor = self::$kernel->getContainer()->get('prestashop.translation.extractor.twig');
        $legacyModuleExtractor = new LegacyModuleExtractor(
            $phpExtractor,
            $smartyExtractor,
            $twigExtractor,
            $this->modulesDir,
            $this->moduleExtractorExcludedDirs
        );

        $providerDefinition = new ModuleProviderDefinition('translationtest');
        $provider = new ModuleCatalogueLayersProvider(
            new MockDatabaseTranslationLoader(
                [],
                $this->createMock(LanguageRepositoryInterface::class),
                $this->createMock(TranslationRepositoryInterface::class)
            ),
            $legacyModuleExtractor,
            $legacyFileLoader,
            $this->modulesDir,
            $this->translationsDir,
            $providerDefinition->getModuleName(),
            $providerDefinition->getFilenameFilters(),
            $providerDefinition->getTranslationDomains()
        );

        // load catalogue from translations/fr-FR
        $catalogue = $provider->getFileTranslatedCatalogue('fr-FR');

        $expected = [
            'ModulesTranslationtestAdmin' => [
                'count' => 1,
                'translations' => [
                    'Modern controller' => 'Contrôleur moderne',
                ],
            ],
            'ModulesTranslationtestSomefile.with-things' => [
                'count' => 1,
                'translations' => [
                    'Smarty template' => 'Le template Smarty',
                ],
            ],
            'ModulesTranslationtestTranslationtest' => [
                'count' => 1,
                'translations' => [
                    'Hello World' => 'Bonjour le monde',
                ],
            ],
        ];

        // verify all catalogues are loaded
        $this->assertResultIsAsExpected($expected, $catalogue);
    }

    /**
     * Test it loads a default catalogue from the `translations` default directory
     */
    public function testItExtractsDefaultCatalogueFromTranslationsDefaultFiles(): void
    {
        // load catalogue from translations/default
        // even if module exists with translations built in
        $catalogue = $this->getProvider('Checkpayment')->getDefaultCatalogue('fr-FR');

        $expected = [
            'ModulesCheckpaymentAdmin' => [
                'count' => 15,
                'translations' => [
                    'No currency has been set for this module.' => '',
                ],
            ],
            'ModulesCheckpaymentShop' => [
                'count' => 19,
                'translations' => [
                    '(order processing will be longer)' => '',
                ],
            ],
        ];

        // verify all catalogues are loaded
        $this->assertResultIsAsExpected($expected, $catalogue);
    }

    /**
     * Test it loads a default catalogue from the `translations` default directory
     */
    public function testItBuildsCatalogueFromLegacyWhenDefaultCatalogueNotFound(): void
    {
        $phpExtractor = self::$kernel->getContainer()->get('prestashop.translation.extractor.php');
        $smartyExtractor = self::$kernel->getContainer()->get('prestashop.translation.extractor.smarty.legacy');
        $twigExtractor = self::$kernel->getContainer()->get('prestashop.translation.extractor.twig');
        $legacyModuleExtractor = new LegacyModuleExtractor(
            $phpExtractor,
            $smartyExtractor,
            $twigExtractor,
            $this->modulesDir,
            $this->moduleExtractorExcludedDirs
        );
        $providerDefinition = new ModuleProviderDefinition('translationtest');
        $provider = new ModuleCatalogueLayersProvider(
            new MockDatabaseTranslationLoader(
                [],
                $this->createMock(LanguageRepositoryInterface::class),
                $this->createMock(TranslationRepositoryInterface::class)
            ),
            $legacyModuleExtractor,
            $this->legacyFileLoader,
            $this->modulesDir,
            $this->translationsDir,
            $providerDefinition->getModuleName(),
            $providerDefinition->getFilenameFilters(),
            $providerDefinition->getTranslationDomains()
        );

        // load catalogue from translations/default
        $catalogue = $provider->getDefaultCatalogue('fr-FR');

        $expected = [
            'ModulesTranslationtestAdmin' => [
                'count' => 1,
                'translations' => [
                    'Modern controller' => 'Modern controller',
                ],
            ],
            'ModulesTranslationtestSomefile.with-things' => [
                'count' => 1,
                'translations' => [
                    'Smarty template' => 'Smarty template',
                ],
            ],
            'ModulesTranslationtestTranslationtest' => [
                'count' => 3,
                'translations' => [
                    'Hello World' => 'Hello World',
                    'An error occured, please check your zip file' => 'An error occured, please check your zip file',
                    'his wording belongs to the module file' => 'his wording belongs to the module file',
                ],
            ],
        ];

        // verify all catalogues are loaded
        $this->assertResultIsAsExpected($expected, $catalogue);
    }

    public function testItDoesntLoadsCustomizedTranslationsWithThemeDefinedFromDatabase(): void
    {
        $databaseContent = [
            [
                'lang' => 'fr-FR',
                'key' => 'Uninstall',
                'translation' => 'Uninstall Traduction customisée',
                'domain' => 'ModulesCheckpaymentAdmin',
                'theme' => 'classic',
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Install',
                'translation' => 'Install Traduction customisée',
                'domain' => 'ModulesCheckpaymentShop',
                'theme' => 'classic',
            ],
        ];

        // load catalogue from database translations
        $catalogue = $this->getProvider('Checkpayment', $databaseContent)->getUserTranslatedCatalogue('fr-FR');

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $domains = $catalogue->getDomains();
        sort($domains);

        // If the theme name is null, the translations which have theme = 'classic' are taken
        $this->assertEmpty($domains);
        $this->assertEmpty($messages);
    }

    public function testItLoadsCustomizedTranslationsWithNoThemeFromDatabase(): void
    {
        $databaseContent = [
            [
                'lang' => 'fr-FR',
                'key' => 'Uninstall',
                'translation' => 'Uninstall Traduction customisée',
                'domain' => 'ModulesCheckpaymentAdmin',
                'theme' => null,
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Install',
                'translation' => 'Install Traduction customisée',
                'domain' => 'ModulesCheckpaymentShop',
                'theme' => null,
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Some made up text 1',
                'translation' => 'Un texte inventé 1',
                'domain' => 'AdminActions',
                'theme' => 'classic',
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Some made up text 2',
                'translation' => 'Un texte inventé 2',
                'domain' => 'ModulesCheckpaymentAdmin',
                'theme' => 'classic',
            ],
        ];

        // load catalogue from database translations
        $catalogue = $this->getProvider('Checkpayment', $databaseContent)->getUserTranslatedCatalogue('fr-FR');

        $expected = [
            'ModulesCheckpaymentAdmin' => [
                'count' => 1,
                'translations' => [
                    'Uninstall' => 'Uninstall Traduction customisée',
                ],
            ],
            'ModulesCheckpaymentShop' => [
                'count' => 1,
                'translations' => [
                    'Install' => 'Install Traduction customisée',
                ],
            ],
        ];

        // verify all catalogues are loaded
        $this->assertResultIsAsExpected($expected, $catalogue);
    }

    /**
     * @param string $moduleName
     * @param array $databaseContent
     *
     * @return ModuleCatalogueLayersProvider
     */
    private function getProvider(string $moduleName, array $databaseContent = []): ModuleCatalogueLayersProvider
    {
        $providerDefinition = new ModuleProviderDefinition($moduleName);

        return new ModuleCatalogueLayersProvider(
            new MockDatabaseTranslationLoader(
                $databaseContent,
                $this->createMock(LanguageRepositoryInterface::class),
                $this->createMock(TranslationRepositoryInterface::class)
            ),
            $this->legacyModuleExtractor,
            $this->legacyFileLoader,
            $this->modulesDir,
            $this->translationsDir,
            $providerDefinition->getModuleName(),
            $providerDefinition->getFilenameFilters(),
            $providerDefinition->getTranslationDomains()
        );
    }

    /**
     * @param array $expected
     * @param MessageCatalogue $catalogue
     */
    private function assertResultIsAsExpected(array $expected, MessageCatalogue $catalogue): void
    {
        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame(array_keys($expected), $domains);

        // verify that the catalogues are complete
        foreach ($expected as $expectedDomain => $expectedValues) {
            $this->assertCount($expectedValues['count'], $messages[$expectedDomain]);

            foreach ($expectedValues['translations'] as $translationKey => $translationValue) {
                $this->assertSame($translationValue, $catalogue->get($translationKey, $expectedDomain));
            }
        }
    }
}
