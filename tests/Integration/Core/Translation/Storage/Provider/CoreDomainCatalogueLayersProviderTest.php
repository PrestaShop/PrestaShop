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

    protected function setUp()
    {
        self::bootKernel();
        $this->translationsDir = self::$kernel->getContainer()->getParameter('test_translations_dir');
    }

    /**
     * Test it loads a XLIFF catalogue from the locale's `translations` directory
     */
    public function testItLoadsCatalogueFromXliffFilesInLocaleDirectory(): void
    {
        // load catalogue from translations/fr-FR
        $catalogue = $this->getFileTranslatedCatalogue('AdminActions', 'fr-FR');

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

        // load catalogue from translations/fr-FR
        $catalogue = $this->getFileTranslatedCatalogue('ModulesCheckpaymentAdmin', 'fr-FR');

        $expected = [
            'ModulesCheckpaymentAdmin' => [
                'count' => 15,
                'translations' => [
                    'The "Payee" and "Address" fields must be configured before using this module.' => 'Les champs "Nom du bénéficiaire" et "Adresse" doivent être configurés avant d\'utiliser ce module.',
                    'No currency has been set for this module.' => 'Aucune devise disponible pour ce module',
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
        $catalogue = $this->getDefaultCatalogue('AdminActions', 'fr-FR');

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

        // load catalogue from translations/default
        $catalogue = $this->getDefaultCatalogue('ModulesCheckpaymentAdmin', 'fr-FR');

        $expected = [
            'ModulesCheckpaymentAdmin' => [
                'count' => 15,
                'translations' => [
                    'The "Payee" and "Address" fields must be configured before using this module.' => '',
                    'No currency has been set for this module.' => '',
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
                'theme' => null,
            ],
        ];

        // load catalogue from database translations
        $catalogue = $this->getUserTranslatedCatalogue('AdminActions', 'fr-FR', $databaseContent);

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

        // load catalogue from database translations
        $catalogue = $this->getUserTranslatedCatalogue('ShopActions', 'fr-FR', $databaseContent);

        $expected = [
            'ShopActions' => [
                'count' => 1,
                'translations' => [
                    'Some made up text' => 'Un texte inventé',
                    'Uninstall' => 'Uninstall',
                ],
            ],
        ];

        // verify all catalogues are loaded
        $this->assertResultIsAsExpected($expected, $catalogue);
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
    private function getProvider(string $domain, array $databaseContent = []): CoreCatalogueLayersProvider
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
            $providerDefinition->getTranslationDomains()
        );
    }
}
