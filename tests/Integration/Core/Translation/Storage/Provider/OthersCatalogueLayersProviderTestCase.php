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
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\OthersProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\TranslationRepositoryInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Test the provider of frontOffice translations
 */
class OthersCatalogueLayersProviderTestCase extends AbstractCatalogueLayersProviderTestCase
{
    /**
     * Test it loads a XLIFF catalogue from the locale's `translations` directory
     */
    public function testItLoadsCatalogueFromXliffFilesInLocaleDirectory(): void
    {
        // load catalogue from translations/fr-FR
        $catalogue = $this->getFileTranslatedCatalogue('fr-FR');

        $expected = [
            'messages' => [
                'count' => 516,
                'translations' => [
                    'Attributes generator' => 'Générateur de déclinaisons de produits',
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
            'messages' => [
                'count' => 516,
                'translations' => [
                    'Attributes generator' => '',
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
                'domain' => 'messages',
                'theme' => Theme::getDefaultTheme(),
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Install',
                'translation' => 'Install Traduction customisée',
                'domain' => 'messages',
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

        // If the theme name is null, the translations which have theme = Theme::getDefaultTheme() are taken
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
                'domain' => 'messages',
                'theme' => null,
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Install',
                'translation' => 'Install Traduction customisée',
                'domain' => 'messages',
                'theme' => null,
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Some made up text 1',
                'translation' => 'Un texte inventé 1',
                'domain' => 'AdminActions',
                'theme' => Theme::getDefaultTheme(),
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Some made up text 2',
                'translation' => 'Un texte inventé 2',
                'domain' => 'ModuleWirepaymentShop',
                'theme' => Theme::getDefaultTheme(),
            ],
        ];

        // load catalogue from database translations
        $catalogue = $this->getUserTranslatedCatalogue('fr-FR', $databaseContent);

        $expected = [
            'messages' => [
                'count' => 2,
                'translations' => [
                    'Uninstall' => 'Uninstall Traduction customisée',
                    'Install' => 'Install Traduction customisée',
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
        $providerDefinition = new OthersProviderDefinition();

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
