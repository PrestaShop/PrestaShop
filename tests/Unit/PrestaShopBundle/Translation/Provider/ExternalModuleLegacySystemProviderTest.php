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

namespace Tests\Unit\PrestaShopBundle\Translation\Provider;

use PrestaShop\TranslationToolsBundle\Translation\Extractor\PhpExtractor;
use PrestaShopBundle\Translation\Exception\UnsupportedLocaleException;
use PrestaShopBundle\Translation\Loader\LegacyFileLoader;
use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractor;
use PrestaShopBundle\Translation\Provider\ExternalModuleLegacySystemProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * @doc ./vendor/bin/phpunit -c tests/Unit/phpunit.xml --filter="ExternalModuleLegacySystemProviderTest"
 */
class ExternalModuleLegacySystemProviderTest extends KernelTestCase
{
    /**
     * @var ExternalModuleLegacySystemProvider
     */
    private $provider;

    protected function setUp()
    {
        $loaderMock = $this->createMock(LoaderInterface::class);
        $legacyFileLoader = new LegacyFileLoader();
        $phpExtractor = new PhpExtractor();
        self::bootKernel();
        $smartyExtractor = self::$kernel->getContainer()->get('prestashop.translation.extractor.smarty');
        $extractor = new LegacyModuleExtractor($phpExtractor, $smartyExtractor, $this->getModuleDirectory());

        $this->provider = new ExternalModuleLegacySystemProvider(
            $loaderMock,
            $this->getModuleDirectory(),
            $legacyFileLoader,
            $extractor
        );

        $this->provider->setModuleName('some_module');

        parent::setUp();
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

    public function testGetLegacyCatalogueWithUndefinedLocaleThrowsAnException()
    {
        $this->expectException(UnsupportedLocaleException::class);
        $this->expectExceptionMessageRegExp('/The locale "en-US" is not supported, because we can\'t find the related file in the module/');
        $this->provider->getLegacyCatalogue();
    }

    public function testGetLegacyCatalogueWithDefinedLocale()
    {
        $this->provider->setLocale('fr-FR');
        $this->assertInstanceOf(MessageCatalogueInterface::class, $this->provider->getLegacyCatalogue());
        $legacyCatalogue = $this->provider->getLegacyCatalogue();

        dump($legacyCatalogue->all());
        $this->assertCount(5, $legacyCatalogue->all());
    }

    /**
     * @return string
     */
    private function getModuleDirectory()
    {
        return __DIR__ . '/../../../../resources';
    }
}
