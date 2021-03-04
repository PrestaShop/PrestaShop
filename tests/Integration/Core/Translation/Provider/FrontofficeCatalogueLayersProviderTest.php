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
use PrestaShop\PrestaShop\Core\Translation\Provider\CoreCatalogueLayersProvider;
use PrestaShop\PrestaShop\Core\Translation\Provider\Definition\FrontofficeProviderDefinition;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Test the provider of frontOffice translations
 */
class FrontofficeCatalogueLayersProviderTest extends KernelTestCase
{
    /**
     * @var string
     */
    private $translationsDir;

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
    }

    /**
     * Test it loads a XLIFF catalogue from the locale's `translations` directory
     */
    public function testItLoadsCatalogueFromXliffFilesInLocaleDirectory(): void
    {
        // load catalogue from translations/fr-FR
        $catalogue = $this->getProvider()->getFileTranslatedCatalogue('fr-FR');

        $expected = [
            'ModulesCheckpaymentShop' => [
                'count' => 19,
                'translations' => [],
            ],
            'ModulesWirepaymentShop' => [
                'count' => 15,
                'translations' => [
                    '(order processing will be longer)' => '(le traitement de la commande sera plus long)',
                ],
            ],
            'ShopNotificationsWarning' => [
                'count' => 8,
                'translations' => [
                    'You do not have any vouchers.' => 'Vous ne possédez pas de bon de réduction.',
                    'You cannot place a new order from your country (%s).' => 'Vous ne pouvez pas créer de nouvelle commande depuis votre pays (%s).',
                ],
            ],
        ];

        $this->assertResultIsAsExpected($expected, $catalogue);
    }

    /**
     * Test it loads a default catalogue from the `translations` default directory
     */
    public function testItExtractsDefaultCatalogueFromTranslationsDefaultFiles(): void
    {
        // load catalogue from translations/default
        $catalogue = $this->getProvider()->getDefaultCatalogue('fr-FR');

        $expected = [
            'ModulesCheckpaymentShop' => [
                'count' => 19,
                'translations' => [],
            ],
            'ModulesWirepaymentShop' => [
                'count' => 20,
                'translations' => [
                    '(order processing will be longer)' => '',
                ],
            ],
            'ShopNotificationsWarning' => [
                'count' => 8,
                'translations' => [
                    'You do not have any vouchers.' => '',
                    'You cannot place a new order from your country (%s).' => '',
                ],
            ],
        ];

        $this->assertResultIsAsExpected($expected, $catalogue);
    }

    public function testItLoadsCustomizedTranslationsWithNoThemeFromDatabase(): void
    {
        $databaseContent = [
            [
                'lang' => 'fr-FR',
                'key' => 'Uninstall',
                'translation' => 'Uninstall Traduction customisée',
                'domain' => 'ShopNotificationsWarning',
                'theme' => null,
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Install',
                'translation' => 'Install Traduction customisée',
                'domain' => 'ModulesWirepaymentShop',
                'theme' => null,
            ],
        ];

        // load catalogue from database translations
        $catalogue = $this->getProvider($databaseContent)->getUserTranslatedCatalogue('fr-FR');

        $expected = [
            'ModulesWirepaymentShop' => [
                'count' => 1,
                'translations' => [
                    'Install' => 'Install Traduction customisée',
                ],
            ],
            'ShopNotificationsWarning' => [
                'count' => 1,
                'translations' => [
                    'You do not have any vouchers.' => 'You do not have any vouchers.',
                    'Uninstall' => 'Uninstall Traduction customisée',
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
                'domain' => 'ShopNotificationsWarning',
                'theme' => 'classic',
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Install',
                'translation' => 'Install Traduction customisée',
                'domain' => 'ModulesWirepaymentShop',
                'theme' => 'classic',
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
                'theme' => 'otherTheme',
            ],
        ];

        // load catalogue from database translations
        $catalogue = $this->getProvider($databaseContent)->getUserTranslatedCatalogue('fr-FR');

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $domains = $catalogue->getDomains();
        sort($domains);

        // If the theme name is null, the translations which have theme = 'classic' are taken
        $this->assertEmpty($domains);
        $this->assertEmpty($messages);
    }

    /**
     * @param array $databaseContent
     */
    private function getProvider(array $databaseContent = []): CoreCatalogueLayersProvider
    {
        $providerDefinition = new FrontofficeProviderDefinition();

        return new CoreCatalogueLayersProvider(
            new MockDatabaseTranslationLoader($databaseContent, $this->createMock(EntityManagerInterface::class)),
            $this->translationsDir,
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
