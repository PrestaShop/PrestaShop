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
use PrestaShop\PrestaShop\Core\Translation\Provider\Definition\ModuleProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Provider\ModuleCatalogueLayersProvider;
use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractorInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
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
    private $legacyModuleExtractor;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|LoaderInterface
     */
    private $legacyFileLoader;
    /**
     * @var mixed
     */
    private $modulesDir;

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

        $this->legacyModuleExtractor = $this->createMock(LegacyModuleExtractorInterface::class);
        $this->legacyFileLoader = $this->createMock(LoaderInterface::class);
    }

    /**
     * Test it loads a XLIFF catalogue from the locale's `translations` directory
     */
    public function testItLoadsCatalogueFromXliffFilesInLocaleDirectory()
    {
        $providerDefinition = new ModuleProviderDefinition('Checkpayment');
        $provider = new ModuleCatalogueLayersProvider(
            new MockDatabaseTranslationLoader([], $this->createMock(EntityManagerInterface::class)),
            $this->legacyModuleExtractor,
            $this->legacyFileLoader,
            $this->modulesDir,
            $this->translationsDir,
            $this->modulesDir,
            $providerDefinition->getModuleName(),
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
        $this->assertSame(['ModulesCheckpaymentAdmin', 'ModulesCheckpaymentShop'], $domains);

        // verify that the catalogues are complete
        $this->assertCount(15, $messages['ModulesCheckpaymentAdmin']);
        $this->assertCount(19, $messages['ModulesCheckpaymentShop']);

        $this->assertSame('Aucune devise disponible pour ce module', $catalogue->get('No currency has been set for this module.', 'ModulesCheckpaymentAdmin'));
        $this->assertSame('Envoyez votre chèque à cette adresse', $catalogue->get('Send your check to this address', 'ModulesCheckpaymentShop'));
    }

    /**
     * Test it loads a default catalogue from the `translations` default directory
     */
    public function testItExtractsDefaultCatalogueFromTranslationsDefaultFiles()
    {
        $providerDefinition = new ModuleProviderDefinition('Checkpayment');
        $provider = new ModuleCatalogueLayersProvider(
            new MockDatabaseTranslationLoader([], $this->createMock(EntityManagerInterface::class)),
            $this->legacyModuleExtractor,
            $this->legacyFileLoader,
            $this->modulesDir,
            $this->translationsDir,
            $this->modulesDir,
            $providerDefinition->getModuleName(),
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
        $this->assertSame(['ModulesCheckpaymentAdmin', 'ModulesCheckpaymentShop'], $domains);

        // verify that the catalogues are complete
        $this->assertCount(15, $messages['ModulesCheckpaymentAdmin']);
        $this->assertCount(19, $messages['ModulesCheckpaymentShop']);

        $this->assertSame('', $catalogue->get('No currency has been set for this module.', 'ModulesCheckpaymentAdmin'));
    }

    public function testItDoesntLoadsCustomizedTranslationsWithThemeDefinedFromDatabase()
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

        $providerDefinition = new ModuleProviderDefinition('Checkpayment');
        $provider = new ModuleCatalogueLayersProvider(
            new MockDatabaseTranslationLoader($databaseContent, $this->createMock(EntityManagerInterface::class)),
            $this->legacyModuleExtractor,
            $this->legacyFileLoader,
            $this->modulesDir,
            $this->translationsDir,
            $this->modulesDir,
            $providerDefinition->getModuleName(),
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

    public function testItLoadsCustomizedTranslationsWithNoThemeFromDatabase()
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

        $providerDefinition = new ModuleProviderDefinition('Checkpayment');
        $provider = new ModuleCatalogueLayersProvider(
            new MockDatabaseTranslationLoader($databaseContent, $this->createMock(EntityManagerInterface::class)),
            $this->legacyModuleExtractor,
            $this->legacyFileLoader,
            $this->modulesDir,
            $this->translationsDir,
            $this->modulesDir,
            $providerDefinition->getModuleName(),
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

        $this->assertSame(['ModulesCheckpaymentAdmin', 'ModulesCheckpaymentShop'], $domains);

        // verify that the catalogues are complete
        $this->assertCount(1, $messages['ModulesCheckpaymentAdmin']);
        $this->assertCount(1, $messages['ModulesCheckpaymentShop']);

        $this->assertSame('Uninstall Traduction customisée', $catalogue->get('Uninstall', 'ModulesCheckpaymentAdmin'));
        $this->assertSame('Install Traduction customisée', $catalogue->get('Install', 'ModulesCheckpaymentShop'));
    }

    /*
     * @TODO: Test fallbacks to load legacy translations
     */
}
