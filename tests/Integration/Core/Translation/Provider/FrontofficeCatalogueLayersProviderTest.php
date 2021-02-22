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
        $providerDefinition = new FrontofficeProviderDefinition();
        $provider = new CoreCatalogueLayersProvider(
            new MockDatabaseTranslationLoader([], $this->createMock(EntityManagerInterface::class)),
            $this->translationsDir,
            $providerDefinition->getFilenameFilters(),
            $providerDefinition->getTranslationDomains()
        );

        // load catalogue from translations/fr-FR
        $catalogue = $provider->getFileTranslatedCatalogue('fr-FR');

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $domains = $catalogue->getDomains();
        sort($domains);

        // verify all catalogues are loaded
        $this->assertSame(['ModulesCheckpaymentShop', 'ModulesWirepaymentShop', 'ShopNotificationsWarning'], $domains);

        // verify that the catalogues are complete
        $this->assertCount(19, $messages['ModulesCheckpaymentShop']);
        $this->assertCount(15, $messages['ModulesWirepaymentShop']);
        $this->assertCount(8, $messages['ShopNotificationsWarning']);

        $this->assertSame('Vous ne possédez pas de bon de réduction.', $catalogue->get('You do not have any vouchers.', 'ShopNotificationsWarning'));
        $this->assertSame('Vous ne pouvez pas créer de nouvelle commande depuis votre pays (%s).', $catalogue->get('You cannot place a new order from your country (%s).', 'ShopNotificationsWarning'));
        $this->assertSame('(le traitement de la commande sera plus long)', $catalogue->get('(order processing will be longer)', 'ModulesWirepaymentShop'));
    }

    /**
     * Test it loads a default catalogue from the `translations` default directory
     */
    public function testItExtractsDefaultCatalogueFromTranslationsDefaultFiles(): void
    {
        $providerDefinition = new FrontofficeProviderDefinition();
        $provider = new CoreCatalogueLayersProvider(
            new MockDatabaseTranslationLoader([], $this->createMock(EntityManagerInterface::class)),
            $this->translationsDir,
            $providerDefinition->getFilenameFilters(),
            $providerDefinition->getTranslationDomains()
        );

        // load catalogue from translations/default
        $catalogue = $provider->getDefaultCatalogue('fr-FR');

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $domains = $catalogue->getDomains();
        sort($domains);

        // verify all catalogues are loaded
        $this->assertSame(['ModulesCheckpaymentShop', 'ModulesWirepaymentShop', 'ShopNotificationsWarning'], $domains);

        // verify that the catalogues are complete
        $this->assertCount(19, $messages['ModulesCheckpaymentShop']);
        $this->assertCount(20, $messages['ModulesWirepaymentShop']);
        $this->assertCount(8, $messages['ShopNotificationsWarning']);

        $this->assertSame('', $catalogue->get('You do not have any vouchers.', 'ShopNotificationsWarning'));
        $this->assertSame('', $catalogue->get('You cannot place a new order from your country (%s).', 'ShopNotificationsWarning'));
        $this->assertSame('', $catalogue->get('(order processing will be longer)', 'ModulesWirepaymentShop'));
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

        $providerDefinition = new FrontofficeProviderDefinition();
        $provider = new CoreCatalogueLayersProvider(
            new MockDatabaseTranslationLoader($databaseContent, $this->createMock(EntityManagerInterface::class)),
            $this->translationsDir,
            $providerDefinition->getFilenameFilters(),
            $providerDefinition->getTranslationDomains()
        );

        // load catalogue from database translations
        $catalogue = $provider->getUserTranslatedCatalogue('fr-FR');

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame(['ModulesWirepaymentShop', 'ShopNotificationsWarning'], $domains);

        // verify that the catalogues are complete
        $this->assertCount(1, $messages['ModulesWirepaymentShop']);
        $this->assertCount(1, $messages['ShopNotificationsWarning']);

        $this->assertSame('You do not have any vouchers.', $catalogue->get('You do not have any vouchers.', 'ShopNotificationsWarning'));
        $this->assertSame('Uninstall Traduction customisée', $catalogue->get('Uninstall', 'ShopNotificationsWarning'));
        $this->assertSame('Install Traduction customisée', $catalogue->get('Install', 'ModulesWirepaymentShop'));
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

        $providerDefinition = new FrontofficeProviderDefinition();
        $provider = new CoreCatalogueLayersProvider(
            new MockDatabaseTranslationLoader($databaseContent, $this->createMock(EntityManagerInterface::class)),
            $this->translationsDir,
            $providerDefinition->getFilenameFilters(),
            $providerDefinition->getTranslationDomains()
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
}
