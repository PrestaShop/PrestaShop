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

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

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
