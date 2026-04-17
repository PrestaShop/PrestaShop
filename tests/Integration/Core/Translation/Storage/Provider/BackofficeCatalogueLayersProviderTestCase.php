<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Core\Translation\Storage\Provider;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\CoreCatalogueLayersProvider;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\BackofficeProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\TranslationRepositoryInterface;

/**
 * Test the provider of backOffice translations
 */
class BackofficeCatalogueLayersProviderTestCase extends AbstractCatalogueLayersProviderTestCase
{
    /**
     * Test it loads a XLIFF catalogue from the locale's `translations` directory
     */
    public function testItLoadsCatalogueFromXliffFilesInLocaleDirectory(): void
    {
        // load catalogue from translations/fr-FR
        $catalogue = $this->getFileTranslatedCatalogue('fr-FR');

        $expected = [
            'AdminActions' => [
                'count' => 90,
                'translations' => [
                    'Save and stay' => 'Enregistrer et rester',
                    'Uninstall' => 'Désinstaller',
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
        $catalogue = $this->getDefaultCatalogue('fr-FR');

        $expected = [
            'AdminActions' => [
                'count' => 91,
                'translations' => [
                    'Save and stay' => '',
                    'Uninstall' => '',
                ],
            ],
        ];

        // verify all catalogues are loaded
        $this->assertResultIsAsExpected($expected, $catalogue);
    }

    public function testItLoadsCustomizedTranslationsFromDatabase(): void
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
                'theme' => Theme::getDefaultTheme(),
            ],
        ];

        // load catalogue from database translations
        $catalogue = $this->getUserTranslatedCatalogue('fr-FR', $databaseContent);

        $expected = [
            'AdminActions' => [
                'count' => 1,
                'translations' => [
                    'Save and stay' => 'Save and stay',
                    'Uninstall' => 'Traduction customisée',
                ],
            ],
        ];

        // verify all catalogues are loaded
        $this->assertResultIsAsExpected($expected, $catalogue);
    }

    /**
     * @param array $databaseContent
     *
     * @return CoreCatalogueLayersProvider
     */
    protected function getProvider(array $databaseContent = []): CoreCatalogueLayersProvider
    {
        $providerDefinition = new BackofficeProviderDefinition();

        return new CoreCatalogueLayersProvider(
            new MockDatabaseTranslationLoader(
                $databaseContent,
                $this->createMock(LanguageRepositoryInterface::class),
                $this->createMock(TranslationRepositoryInterface::class)
            ),
            $this->translationsDir,
            $providerDefinition->getFilenameFilters(),
            $providerDefinition->getTranslationDomains()
        );
    }
}
