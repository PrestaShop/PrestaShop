<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\PrestaShopBundle\Translation\Extractor;

use PrestaShop\TranslationToolsBundle\Translation\Extractor\PhpExtractor;
use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * @doc ./vendor/bin/phpunit -c tests/Integration/phpunit.xml --filter="LegacyModuleExtractorTest"
 */
class LegacyModuleExtractorTest extends KernelTestCase
{
    /**
     * @var string Domain name of the modules translations
     */
    const DOMAIN_NAME = 'ModulesSomeModule';

    /**
     * @cover extract
     */
    public function testLegacyModuleExtractorShouldReturnACatalogue()
    {
        self::bootKernel();
        $phpExtractor = new PhpExtractor();
        $smartyExtractor = self::$kernel->getContainer()->get('prestashop.translation.extractor.smarty');
        $extractor = new LegacyModuleExtractor($phpExtractor, $smartyExtractor, $this->getModuleFolder());

        $catalogue = $extractor->extract('some_module', 'fr-FR');

        $this->assertInstanceOf(MessageCatalogueInterface::class, $catalogue);
    }

    /**
     * @depends testLegacyModuleExtractorShouldReturnACatalogue
     */
    public function testExtractedCatalogueContainsTheExpectedTranslations()
    {
        self::bootKernel();
        $phpExtractor = new PhpExtractor();
        $smartyExtractor = self::$kernel->getContainer()->get('prestashop.translation.extractor.smarty');
        $extractor = new LegacyModuleExtractor($phpExtractor, $smartyExtractor, $this->getModuleFolder());

        $catalogue = $extractor->extract('some_module', 'fr-FR');
        $this->assertCount(5, $catalogue->all(self::DOMAIN_NAME));
        $this->assertTrue($catalogue->has(
            'An error occured, please check your zip file',
            self::DOMAIN_NAME
        ));

        $this->assertSame(
            'The module %s has been disabled',
            $catalogue->get('The module %s has been disabled', self::DOMAIN_NAME)
        );
    }

    protected function tearDown()
    {
        self::$kernel->shutdown();
    }

    /**
     * @return string
     */
    private function getModuleFolder()
    {
        return __DIR__ . '/../../../../resources';
    }
}
