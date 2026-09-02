<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\PrestaShopBundle\Translation\Loader;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Tests\Integration\PrestaShopBundle\Translation\CatalogueVerifier;

/**
 * Tests extract of translations from legacy translation files
 *
 * ./vendor/bin/phpunit -c tests/Integration/phpunit.xml --filter="LegacyFileLoaderTest"
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
    public function __construct($name = null)
    {
        parent::__construct($name);

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
