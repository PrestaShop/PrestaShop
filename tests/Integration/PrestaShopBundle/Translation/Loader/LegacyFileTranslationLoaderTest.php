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

namespace Tests\Integration\PrestaShopBundle\Translation\Loader;

use PrestaShopBundle\Translation\Exception\UnsupportedModuleException;
use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractor;
use PrestaShopBundle\Translation\Loader\LegacyFileTranslationLoader;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @doc ./vendor/bin/phpunit -c tests/Integration/phpunit.xml --filter="LegacyFileTranslationLoaderTest"
 */
class LegacyFileTranslationLoaderTest extends KernelTestCase
{
    const DOMAIN_MODULES = 'ModulesSomeModule';

    public function testExtract()
    {
        $localeConverter = self::$kernel->getContainer()->get('prestashop.core.translation.locale.converter');
        $moduleExtractor = $this->getModuleExtractor();
        $modulesList = ['some_module'];
        $extractor = new LegacyFileTranslationLoader($localeConverter, $moduleExtractor, $modulesList);
        $catalogue = $extractor->load($this->getTranslationsFolder() . 'fr.php', 'fr-FR', self::DOMAIN_MODULES);

        $this->assertInstanceOf(MessageCatalogueInterface::class, $catalogue);
        $this->assertCount(5, $catalogue->all(self::DOMAIN_MODULES));
        $this->assertCount(0, $catalogue->all('messages'));
        $someId = 'An error occured, please check your zip file';
        $this->assertTrue($catalogue->has($someId, self::DOMAIN_MODULES));
        $this->assertSame(
            'Une erreur est survenue, veuillez vérifier votre fichier zip',
            $catalogue->get($someId, self::DOMAIN_MODULES)
        );
    }

    public function testExtractionWithAnInvalidDomain()
    {
        $this->expectException(UnsupportedModuleException::class);
        $this->expectExceptionMessage('No module was retrieved based on the domain "SomeModule". Maybe the module is not installed or disabled?');

        $localeConverter = self::$kernel->getContainer()->get('prestashop.core.translation.locale.converter');
        $moduleExtractor = $this->getModuleExtractor();
        $modulesList = ['some_module'];
        $extractor = new LegacyFileTranslationLoader($localeConverter, $moduleExtractor, $modulesList);

        $extractor->load($this->getTranslationsFolder() . 'fr.php', 'fr-FR', 'SomeModule');
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        self::bootKernel();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        self::$kernel->shutdown();
    }

    /**
     * @return string
     */
    private function getFakeModulesFolder()
    {
        return __DIR__ . '/../../../../resources/';
    }

    /**
     * @return string
     */
    private function getTranslationsFolder()
    {
        return __DIR__ . '/../../../../resources/some_module/translations/';
    }

    /**
     * We have updated the path to the module to point to the "some_module" modules folder.
     *
     * @return LegacyModuleExtractor
     */
    private function getModuleExtractor()
    {
        $phpExtractor = self::$kernel->getContainer()->get('prestashop.translation.extractor.php');
        $smartyExtractor = self::$kernel->getContainer()->get('prestashop.translation.extractor.smarty');

        return new LegacyModuleExtractor($phpExtractor, $smartyExtractor, $this->getFakeModulesFolder());
    }
}
