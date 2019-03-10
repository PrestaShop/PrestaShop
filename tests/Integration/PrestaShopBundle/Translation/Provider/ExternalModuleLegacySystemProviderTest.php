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

namespace Tests\Integration\PrestaShopBundle\Translation\Provider;

use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractor;
use PrestaShopBundle\Translation\Provider\ExternalModuleLegacySystemProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * @doc ./vendor/bin/phpunit -c tests/Integration/phpunit.xml --filter="ExternalModuleLegacySystemProviderTest"
 */
class ExternalModuleLegacySystemProviderTest extends KernelTestCase
{
    /**
     * @var ExternalModuleLegacySystemProvider
     */
    private $provider;

    protected function setUp()
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $databaseLoader = $container->get('prestashop.translation.database_loader');
        $legacyFileLoader = $container->get('prestashop.translation.legacy_file_loader');
        $phpExtractor = $container->get('prestashop.translation.extractor.php');
        $moduleProvider = $container->get('prestashop.translation.module_provider');
        $smartyExtractor = $container->get('prestashop.translation.extractor.smarty');
        $extractor = new LegacyModuleExtractor($phpExtractor, $smartyExtractor, $this->getModuleDirectory());

        $this->provider = new ExternalModuleLegacySystemProvider(
            $databaseLoader,
            $this->getModuleDirectory(),
            $legacyFileLoader,
            $extractor,
            $moduleProvider
        );

        $this->provider
            ->setModuleName('some_module')
            ->setDomain('ModulesSomeModule')
        ;
    }

    protected function tearDown()
    {
        self::$kernel->shutdown();
    }

    public function testGetTranslationDomains()
    {
        $this->assertSame(['^ModulesSomeModule*'], $this->provider->getTranslationDomains());
    }

    public function testGetFilters()
    {
        $this->assertSame([], $this->provider->getFilters());
    }

    public function testGetIdentifier()
    {
        $this->assertSame('external_legacy_module', $this->provider->getIdentifier());
    }

    /**
     * In the module same_module, you can find:
     * 1 translation in the controller
     * 4 translations in Smarty files
     * and 88 unique translations in the translations folder for the locale fr-FR
     */
    public function testGetLegacyCatalogueWithDefinedLocale()
    {
        $this->provider->setLocale('fr-FR');
        $legacyCatalogue = $this->provider->getLegacyCatalogue();
        $this->assertInstanceOf(MessageCatalogueInterface::class, $legacyCatalogue);

        $this->assertCount(5, $legacyCatalogue->all('ModulesSomeModule'));
    }

    /**
     * @return string
     */
    private function getModuleDirectory()
    {
        return __DIR__ . '/../../../../resources';
    }
}
