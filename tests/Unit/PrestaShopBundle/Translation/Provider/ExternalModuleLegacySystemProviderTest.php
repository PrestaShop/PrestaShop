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

use PHPUnit\Framework\TestCase;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\PhpExtractor;
use PrestaShopBundle\Translation\Extractor\LegacyFileExtractor;
use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractor;
use PrestaShopBundle\Translation\Provider\ExternalModuleLegacySystemProvider;
use Symfony\Component\Translation\Loader\LoaderInterface;

/**
 * @doc ./vendor/bin/phpunit -c tests/Unit/phpunit.xml --filter="ExternalModuleLegacySystemProviderTest"
 */
class ExternalModuleLegacySystemProviderTest extends TestCase
{
    /**
     * @var ExternalModuleLegacySystemProvider
     */
    private $provider;

    protected function setUp()
    {
        $loaderMock = $this->createMock(LoaderInterface::class);
        $legacyFileExtractor = new LegacyFileExtractor();
        $phpExtractor = new PhpExtractor();
        $legacyModuleExtractor = new LegacyModuleExtractor($phpExtractor, $this->getModuleDirectory());

        $this->provider = new ExternalModuleLegacySystemProvider(
            $loaderMock,
            $this->getModuleDirectory(),
            $legacyFileExtractor,
            $legacyModuleExtractor
        );

        $this->provider->setModuleName('yo_lo_lo');

        parent::setUp();
    }

    public function testGetTranslationDomains()
    {
        $this->assertSame(['^ModulesYoLoLo*'], $this->provider->getTranslationDomains());
    }

    public function testGetFilters()
    {
        $this->assertSame([], $this->provider->getFilters());
    }

    /**
     * @return string
     */
    private function getModuleDirectory()
    {
        return __DIR__ . '/../../../../resources';
    }
}
