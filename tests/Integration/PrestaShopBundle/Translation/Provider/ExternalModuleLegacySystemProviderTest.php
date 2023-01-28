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

use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractor;
use PrestaShopBundle\Translation\Provider\ExternalModuleLegacySystemProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Tests\Integration\PrestaShopBundle\Translation\CatalogueVerifier;

/**
 * ./vendor/bin/phpunit -c tests/Integration/phpunit.xml --filter="ExternalModuleLegacySystemProviderTest"
 */
class ExternalModuleLegacySystemProviderTest extends KernelTestCase
{
    public const MODULE_NAME = 'translationtest';

    /**
     * @var ExternalModuleLegacySystemProvider
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

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $databaseLoader = $container->get('prestashop.translation.database_loader');
        $legacyFileLoader = $container->get('prestashop.translation.legacy_file_loader');
        $phpExtractor = $container->get('prestashop.translation.extractor.php');
        $moduleProvider = $container->get('prestashop.translation.module_provider');
        $smartyExtractor = $container->get('prestashop.translation.extractor.smarty.legacy');
        $twigExtractor = $container->get('prestashop.translation.extractor.twig');

        $extractor = new LegacyModuleExtractor(
            $phpExtractor,
            $smartyExtractor,
            $twigExtractor,
            $this->getModuleDirectory()
        );

        $this->provider = new ExternalModuleLegacySystemProvider(
            $databaseLoader,
            $this->getModuleDirectory(),
            $legacyFileLoader,
            $extractor,
            $moduleProvider
        );

        $this->provider
            ->setModuleName(self::MODULE_NAME)
        ;
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
        $this->provider->setLocale($locale);
        $legacyCatalogue = $this->provider->getXliffCatalogue();

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

    /**
     * @return string
     */
    private function getModuleDirectory()
    {
        return __DIR__ . '/../../../../Resources/modules';
    }
}
