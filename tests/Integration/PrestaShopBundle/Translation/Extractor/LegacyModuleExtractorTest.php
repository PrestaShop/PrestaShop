<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\PrestaShopBundle\Translation\Extractor;

use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Tests\Integration\PrestaShopBundle\Translation\CatalogueVerifier;

/**
 * Tests the extraction of wordings from a module using static code analysis
 *
 * ./vendor/bin/phpunit -c tests/Integration/phpunit.xml --filter="LegacyModuleExtractorTest"
 */
class LegacyModuleExtractorTest extends KernelTestCase
{
    /**
     * @var string Domain name of the modules translations
     */
    public const DOMAIN_NAME = 'ModulesTranslationtest';
    public const MODULE_NAME = 'translationtest';

    /**
     * @var CatalogueVerifier
     */
    private $catalogueVerifier;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->catalogueVerifier = new CatalogueVerifier($this);
    }

    /**
     * @param string $locale
     * @param array $expected
     *
     * @dataProvider provideTestCases
     */
    public function testExtractedCatalogueContainsTheExpectedWordings($locale, $expected)
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();

        $phpExtractor = $container->get('prestashop.translation.extractor.php');
        $smartyExtractor = $container->get('prestashop.translation.extractor.smarty.legacy');
        $twigExtractor = $container->get('prestashop.translation.extractor.twig');

        $extractor = new LegacyModuleExtractor(
            $phpExtractor,
            $smartyExtractor,
            $twigExtractor,
            $this->getModuleFolder()
        );

        $catalogue = $extractor->extract(self::MODULE_NAME, $locale);

        $this->assertInstanceOf(MessageCatalogueInterface::class, $catalogue);

        $this->catalogueVerifier->assertCataloguesMatch($catalogue, $expected);
    }

    /**
     * @return string
     */
    private function getModuleFolder()
    {
        return __DIR__ . '/../../../../Resources/modules';
    }

    public function provideTestCases()
    {
        return [
            'French' => [
                'fr-FR',
                [
                    'Modules.Translationtest.Admin' => [
                        'Modern controller' => 'Modern controller',
                    ],
                    'Modules.Translationtest.Translationtest' => [
                        'Hello World' => 'Hello World',
                        'An error occured, please check your zip file' => 'An error occured, please check your zip file',
                    ],
                    'Modules.Translationtest.Somefile.with-things' => [
                        'Smarty template' => 'Smarty template',
                    ],
                ],
            ],
            // the locale has no impact on wordings because they are only extracted,
            // not translated
            'Spanish' => [
                'es-ES',
                [
                    'Modules.Translationtest.Admin' => [
                        'Modern controller' => 'Modern controller',
                    ],
                    'Modules.Translationtest.Translationtest' => [
                        'Hello World' => 'Hello World',
                        'An error occured, please check your zip file' => 'An error occured, please check your zip file',
                    ],
                    'Modules.Translationtest.Somefile.with-things' => [
                        'Smarty template' => 'Smarty template',
                    ],
                ],
            ],
        ];
    }
}
