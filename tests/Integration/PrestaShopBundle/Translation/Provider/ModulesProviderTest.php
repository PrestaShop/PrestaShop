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

namespace Tests\Integration\PrestaShopBundle\Translation\Provider;

use PrestaShop\TranslationToolsBundle\Translation\Helper\DomainHelper;
use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractor;
use PrestaShopBundle\Translation\Provider\ModulesProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Tests\Integration\PrestaShopBundle\Translation\CatalogueVerifier;

/**
 * @doc ./vendor/bin/phpunit -c tests/Integration/phpunit.xml --filter="ModulesProviderTest"
 */
class ModulesProviderTest extends KernelTestCase
{
    const MODULE_NAME = 'translationtest';

    /**
     * @var ModulesProvider
     */
    private $provider;

    /**
     * @var CatalogueVerifier
     */
    private $catalogueVerifier;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->catalogueVerifier = new CatalogueVerifier($this);
    }

    protected function setUp()
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $databaseContent = [
            [
                'lang' => 'fr-FR',
                'key' => 'Some text to translate',
                'translation' => 'Traduction customisée',
                'domain' => 'ModulesTranslationtest',
                'theme' => null,
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Some made up text',
                'translation' => 'Un texte inventé',
                'domain' => 'ModulesTranslationtest',
                'theme' => 'classic',
            ],
        ];
        $legacyFileLoader = $container->get('prestashop.translation.loader.legacy_file');
        $phpExtractor = $container->get('prestashop.translation.extractor.php');
        $smartyExtractor = $container->get('prestashop.translation.extractor.smarty.legacy');
        $twigExtractor = $container->get('prestashop.translation.extractor.twig');
        $modulesDirectory = $this->getBuiltInModuleDirectory();

        $extractor = new LegacyModuleExtractor(
            $phpExtractor,
            $smartyExtractor,
            $twigExtractor,
            $modulesDirectory
        );

        $this->provider = new ModulesProvider(
            new MockDatabaseTranslationReader($databaseContent),
            $modulesDirectory,
            $this->getDefaultModuleDirectory(),
            $legacyFileLoader,
            $extractor,
            self::MODULE_NAME
        );
    }

    /**
     * @param string $locale
     * @param array $expected
     *
     * @dataProvider provideTestCases
     */
    public function testTranslationsCatalogueIsBuiltFromKeysFoundInSourceAndTranslationsInLegacyFiles(
        $locale,
        array $expected
    ) {
        $legacyCatalogue = $this->provider->getFileTranslatedCatalogue($locale);

        $this->assertInstanceOf(MessageCatalogueInterface::class, $legacyCatalogue);

        $this->catalogueVerifier->assertCataloguesMatch($legacyCatalogue, $expected);
    }

    public function provideTestCases()
    {
        return [
            'French' => [
                'fr-FR',
                [
                    'ModulesTranslationtestAdmin' => [
                        'Modern controller' => 'Contrôleur moderne',
                    ],
                    'ModulesTranslationtestTranslationtest' => [
                        'Hello World' => 'Bonjour le monde',
                    ],
                    'ModulesTranslationtestSomefile.with-things' => [
                        'Smarty template' => 'Le template Smarty',
                    ],
                ],
            ],
            'Spanish' => [
                'es-ES',
                [
                    'ModulesTranslationtestTranslationtest' => [
                        'Hello World' => 'Hola mundo',
                    ],
                ],
            ],
            'Italian' => [
                'it-IT',
                [
                    'ModulesTranslationtestTranslationtest' => [
                        'Hello World' => 'Ciao mondo',
                    ],
                ],
            ],
        ];
    }

    public function testItLoadsCustomizedTranslationsFromDatabase()
    {
        // load catalogue from database translations
        $catalogue = $this->provider->getUserTranslatedCatalogue('fr-FR');

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $domains = $catalogue->getDomains();
        sort($domains);

        // verify all catalogues are loaded
        $this->assertSame([
            'ModulesTranslationtest',
        ], $domains);

        // verify that the catalogues are complete
        $this->assertCount(1, $messages['ModulesTranslationtest']);

        // verify translations
        $this->assertSame('Traduction customisée', $catalogue->get('Some text to translate', 'ModulesTranslationtest'));
    }

    /**
     * @return string
     */
    private function getBuiltInModuleDirectory()
    {
        return __DIR__ . '/../../../../Resources/modules';
    }

    /**
     * @return string
     */
    private function getDefaultModuleDirectory()
    {
        return __DIR__ . '/../../../../Resources';
    }
}
