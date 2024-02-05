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

use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\CoreCatalogueLayersProvider;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\OthersProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\TranslationRepositoryInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Test the provider of frontOffice translations
 */
class OthersCatalogueLayersProviderTest extends AbstractCatalogueLayersProviderTest
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
                'theme' => 'classic',
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Install',
                'translation' => 'Install Traduction customisée',
                'domain' => 'messages',
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
                'theme' => 'classic',
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Some made up text 2',
                'translation' => 'Un texte inventé 2',
                'domain' => 'ModuleWirepaymentShop',
                'theme' => 'classic',
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
