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

namespace Tests\Integration\PrestaShopBundle\Translation\Loader;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Tests\Integration\PrestaShopBundle\Translation\CatalogueVerifier;

/**
 * Tests extract of translations from legacy translation files
 *
 * @doc ./vendor/bin/phpunit -c tests/Integration/phpunit.xml --filter="LegacyFileLoaderTest"
 */
class LegacyFileLoaderTest extends KernelTestCase
{
    /**
     * @var string
     */
    private $translationsFolder = __DIR__ . '/../../../../Resources/modules/translationtest/translations/';

    /**
     * @var CatalogueVerifier
     */
    private $catalogueVerifier;

    /**
     * {@inheritdoc}
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->catalogueVerifier = new CatalogueVerifier($this);
    }

    /**
     * @param string $locale
     * @param array[] $expected
     *
     * @dataProvider provideTestCases
     */
    public function testItExtractsTranslationsFromLegacyFiles($locale, $expected)
    {
        self::bootKernel();
        $extractor = self::$kernel->getContainer()->get('prestashop.translation.legacy_file_loader');

        $catalogue = $extractor->load($this->getTranslationsFolder(), $locale);

        $this->assertInstanceOf(MessageCatalogueInterface::class, $catalogue);

        $this->catalogueVerifier->assertCataloguesMatch($catalogue, $expected);
    }

    /**
     * @return string
     */
    private function getTranslationsFolder()
    {
        return $this->translationsFolder;
    }

    public function provideTestCases()
    {
        return [
            'French' => [
                'fr-FR',
                [
                    'ModulesTranslationtestAdmin' => [
                        '9e8be49b9cfd2252504e0a48ddb1c9df' => 'Contrôleur moderne',
                    ],
                    'ModulesTranslationtestTranslationtest' => [
                        'b10a8db164e0754105b7a99be72e3fe5' => 'Bonjour le monde',
                    ],
                    'ModulesTranslationtestSomefile.with-things' => [
                        '9e5c5556b32cabcca238e5d30f6e10c4' => 'Le template Smarty',
                    ],
                ],
            ],
            'Spanish' => [
                'es-ES',
                [
                    'ModulesTranslationtestTranslationtest' => [
                        'b10a8db164e0754105b7a99be72e3fe5' => 'Hola mundo',
                    ],
                ],
            ],
            'Italian' => [
                'it-IT',
                [
                    'ModulesTranslationtestTranslationtest' => [
                        'b10a8db164e0754105b7a99be72e3fe5' => 'Ciao mondo',
                    ],
                ],
            ],
        ];
    }
}
