<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Core\Translation\Storage\Provider;

use Generator;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Extractor\ExtraPropertyTranslationExtractor;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\CoreCatalogueLayersProvider;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\CoreDomainProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\TranslationRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Test the core translations provider filtering by domain
 */
class CoreDomainCatalogueLayersProviderTest extends KernelTestCase
{
    /**
     * @var string
     */
    protected $translationsDir;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->translationsDir = self::$kernel->getContainer()->getParameter('test_translations_dir');
    }

    /**
     * Test it loads a XLIFF catalogue from the locale's `translations` directory
     *
     * @dataProvider getValuesForLoadCatalogueFromXliff
     */
    public function testItLoadsCatalogueFromXliffFilesInLocaleDirectory(string $domain, array $expectedCatalogue): void
    {
        // load catalogue from translations/fr-FR
        $catalogue = $this->getFileTranslatedCatalogue($domain, 'fr-FR');

        // verify all catalogues are loaded
        $this->assertResultIsAsExpected($expectedCatalogue, $catalogue);
    }

    public function getValuesForLoadCatalogueFromXliff(): Generator
    {
        yield [
            // domain
            'AdminActions',
            // expectedCatalogue
            [
                'AdminActions' => [
                    'count' => 90,
                    'translations' => [
                        'Save and stay' => 'Enregistrer et rester',
                        'Uninstall' => 'Désinstaller',
                    ],
                ],
            ],
        ];

        yield [
            // domain
            'ModulesCheckpaymentAdmin',
            // expectedCatalogue
            [
                'ModulesCheckpaymentAdmin' => [
                    'count' => 15,
                    'translations' => [
                        'The "Payee" and "Address" fields must be configured before using this module.' => 'Les champs "Nom du bénéficiaire" et "Adresse" doivent être configurés avant d\'utiliser ce module.',
                        'No currency has been set for this module.' => 'Aucune devise disponible pour ce module',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test it loads a default catalogue from the `translations` default directory
     *
     * @dataProvider getValuesForExtractDefaultCatalogue
     */
    public function testItExtractsDefaultCatalogueFromTranslationsDefaultFiles(string $domain, array $expectedCatalogue): void
    {
        // load catalogue from translations/default
        $catalogue = $this->getDefaultCatalogue($domain, 'fr-FR');

        // verify all catalogues are loaded
        $this->assertResultIsAsExpected($expectedCatalogue, $catalogue);
    }

    public function getValuesForExtractDefaultCatalogue(): Generator
    {
        yield [
            // domain
            'AdminActions',
            // expectedCatalogue
            [
                'AdminActions' => [
                    'count' => 91,
                    'translations' => [
                        'Save and stay' => '',
                        'Uninstall' => '',
                    ],
                ],
            ],
        ];

        yield [
            // domain
            'ModulesCheckpaymentAdmin',
            // expectedCatalogue
            [
                'ModulesCheckpaymentAdmin' => [
                    'count' => 15,
                    'translations' => [
                        'The "Payee" and "Address" fields must be configured before using this module.' => '',
                        'No currency has been set for this module.' => '',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getValuesForLoadCatalogueFromDatabase
     */
    public function testItLoadsCustomizedTranslationsFromDatabase(string $domain, array $expectedCatalogue): void
    {
        $databaseContent = [
            [
                'lang' => 'fr-FR',
                'key' => 'Uninstall',
                'translation' => 'Traduction customisée',
                'domain' => 'AdminActions',
                'theme' => null,
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Some made up text',
                'translation' => 'Un texte inventé',
                'domain' => 'ShopActions',
                'theme' => null,
            ],
        ];

        // load catalogue from database translations
        $catalogue = $this->getUserTranslatedCatalogue($domain, 'fr-FR', $databaseContent);

        // verify all catalogues are loaded
        $this->assertResultIsAsExpected($expectedCatalogue, $catalogue);
    }

    public function getValuesForLoadCatalogueFromDatabase(): Generator
    {
        yield [
            // domain
            'AdminActions',
            // expectedCatalogue
            [
                'AdminActions' => [
                    'count' => 1,
                    'translations' => [
                        'Save and stay' => 'Save and stay',
                        'Uninstall' => 'Traduction customisée',
                    ],
                ],
            ],
        ];

        yield [
            // domain
            'ShopActions',
            // expectedCatalogue
            [
                'ShopActions' => [
                    'count' => 1,
                    'translations' => [
                        'Some made up text' => 'Un texte inventé',
                        'Uninstall' => 'Uninstall',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test it merges the label/description wordings declared in the extra property registry into the
     * default catalogue, keeping only the domains that belong to this core type.
     */
    public function testItMergesExtraPropertyWordingsIntoDefaultCatalogue(): void
    {
        $extraPropertyTranslationExtractor = $this->createMock(ExtraPropertyTranslationExtractor::class);
        $extraPropertyTranslationExtractor->method('extract')
            ->willReturnCallback(function (string $locale) {
                $catalogue = new MessageCatalogue($locale);
                // belongs to this core domain: must be kept (normalized to AdminActions)
                $catalogue->set('A core registry label', 'A core registry label', 'Admin.Actions');
                // belongs to another domain: must be filtered out
                $catalogue->set('A foreign label', 'A foreign label', 'Admin.Catalog');

                return $catalogue;
            });

        $catalogue = $this->getProvider('AdminActions', [], $extraPropertyTranslationExtractor)->getDefaultCatalogue('fr-FR');

        // the core registry wording is present under the normalized domain, with key == value
        $this->assertSame('A core registry label', $catalogue->get('A core registry label', 'AdminActions'));

        // a wording belonging to another domain is filtered out
        $this->assertNotContains('AdminCatalog', $catalogue->getDomains());
    }

    /**
     * A core domain can exist only through the extra property registry, with no XLF file shipped for
     * it. Opening such a domain must not fail: the default catalogue serves the registry wording and
     * the file-translated catalogue is simply empty (regression test for the per-domain editor 400).
     */
    public function testItServesARegistryOnlyCoreDomainWithoutAnXlfFile(): void
    {
        $extractor = $this->createMock(ExtraPropertyTranslationExtractor::class);
        $extractor->method('extract')
            ->willReturnCallback(function (string $locale) {
                $catalogue = new MessageCatalogue($locale);
                $catalogue->set('Core only label', 'Core only label', 'Admin.Coreextrafield.Test');

                return $catalogue;
            });

        $provider = $this->getProvider('AdminCoreextrafieldTest', [], $extractor);

        $defaultCatalogue = $provider->getDefaultCatalogue('fr-FR');
        $this->assertSame('Core only label', $defaultCatalogue->get('Core only label', 'AdminCoreextrafieldTest'));

        // no XLF file exists for this domain: the file-translated catalogue must be empty, not throw
        $this->assertSame([], $provider->getFileTranslatedCatalogue('fr-FR')->getDomains());
    }

    private function getDefaultCatalogue(string $domain, string $locale): MessageCatalogue
    {
        return $this->getProvider($domain)->getDefaultCatalogue($locale);
    }

    private function getFileTranslatedCatalogue(string $domain, string $locale): MessageCatalogue
    {
        return $this->getProvider($domain)->getFileTranslatedCatalogue($locale);
    }

    private function getUserTranslatedCatalogue(string $domain, string $locale, array $databaseContent = []): MessageCatalogue
    {
        return $this->getProvider($domain, $databaseContent)->getUserTranslatedCatalogue($locale);
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

    /**
     * @param string $domain
     * @param array $databaseContent
     *
     * @return CoreCatalogueLayersProvider
     */
    private function getProvider(string $domain, array $databaseContent = [], ?ExtraPropertyTranslationExtractor $extraPropertyTranslationExtractor = null): CoreCatalogueLayersProvider
    {
        $providerDefinition = new CoreDomainProviderDefinition($domain);

        return new CoreCatalogueLayersProvider(
            new MockDatabaseTranslationLoader(
                $databaseContent,
                $this->createMock(LanguageRepositoryInterface::class),
                $this->createMock(TranslationRepositoryInterface::class)
            ),
            $this->translationsDir,
            $providerDefinition->getFilenameFilters(),
            $providerDefinition->getTranslationDomains(),
            $extraPropertyTranslationExtractor ?? $this->createEmptyExtraPropertyTranslationExtractor()
        );
    }

    /**
     * Builds an extra property extractor that contributes nothing, so the catalogue is unchanged.
     */
    private function createEmptyExtraPropertyTranslationExtractor(): ExtraPropertyTranslationExtractor
    {
        $extractor = $this->createMock(ExtraPropertyTranslationExtractor::class);
        $extractor->method('extract')
            ->willReturnCallback(function (string $locale) {
                return new MessageCatalogue($locale);
            });

        return $extractor;
    }
}
