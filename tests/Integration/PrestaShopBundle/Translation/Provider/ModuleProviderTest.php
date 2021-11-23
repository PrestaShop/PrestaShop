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

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Provider\ModuleProvider;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Test the provider of specific module translations
 */
class ModuleProviderTest extends TestCase
{
    /**
     * @var ModuleProvider
     */
    private $provider;

    protected function setUp(): void
    {
        $loader = $this->getMockBuilder(LoaderInterface::class)
            ->getMock();

        $resourcesDir = __DIR__ . '/../../../../Resources/translations';

        $this->provider = new ModuleProvider($loader, $resourcesDir);
        $this->provider->setLocale('fr-FR');
        $this->provider->setModuleName('ps_wirepayment');
    }

    public function testItExtractsCatalogueFromXliffFiles()
    {
        $catalogue = $this->provider->getMessageCatalogue();

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // check only the specific module translations have been loaded
        $domains = $catalogue->getDomains();
        // for some reason domains may not be in the same order as the test
        sort($domains);
        $this->assertSame(['ModulesWirepaymentAdmin', 'ModulesWirepaymentShop'], $domains);

        // Check integrity of translations
        $this->assertCount(21, $catalogue->all('ModulesWirepaymentAdmin'));

        $this->assertSame('Transfert bancaire', $catalogue->get('Wire payment', 'ModulesWirepaymentAdmin'));

        $this->assertCount(20, $catalogue->all('ModulesWirepaymentShop'));
        $this->assertSame('Payer par virement bancaire', $catalogue->get('Pay by bank wire', 'ModulesWirepaymentShop'));
    }
}
