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
use PrestaShopBundle\Translation\Provider\ModulesProvider;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Test the provider for all the modules' translations
 */
class ModulesProviderTest extends TestCase
{
    /**
     * @var ModulesProvider
     */
    private $provider;

    protected function setUp()
    {
        $loader = $this->getMockBuilder(LoaderInterface::class)
            ->getMock();

        $resourcesDir = __DIR__ . '/../../../../Resources/translations';

        $this->provider = new ModulesProvider($loader, $resourcesDir);
        $this->provider->setLocale('fr-FR');
    }

    public function testItExtractsCatalogueFromXliffFiles()
    {
        $catalogue = $this->provider->getMessageCatalogue();
        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $this->assertArrayHasKey('ModulesWirepaymentAdmin', $messages);
        $this->assertArrayHasKey('ModulesWirepaymentShop', $messages);

        $this->assertCount(15, $catalogue->all('ModulesCheckpaymentAdmin'));
        $this->assertSame('Chèque', $catalogue->get('Payments by check', 'ModulesCheckpaymentAdmin'));

        $this->assertCount(19, $catalogue->all('ModulesCheckpaymentShop'));
        $this->assertSame('Payer par chèque', $catalogue->get('Pay by check', 'ModulesCheckpaymentShop'));
    }
}
