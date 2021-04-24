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

declare(strict_types=1);

namespace Tests\Integration\Adapter\Module\PrestaTrust;

use Currency;
use Doctrine\Common\Cache\ArrayCache;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Adapter\Module\PrestaTrust\PrestaTrustChecker;
use PrestaShop\PrestaShop\Adapter\Presenter\Module\ModulePresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShopBundle\Service\DataProvider\Marketplace\ApiClient;
use Symfony\Component\Translation\Translator;

class PrestaTrustCheckerTest extends TestCase
{
    /**
     * @var PrestaTrustChecker
     */
    protected $prestatrustChecker;
    /**
     * @var array
     */
    protected $modules = [];

    /**
     * @var ModulePresenter
     */
    protected $modulePresenter;

    protected function setUp(): void
    {
        parent::setup();

        $this->modules = [
            // Module under dev, not concerned by PrestaTrust checks
            'module-under-dev' => new Module([
                'name' => 'module-under-dev',
            ]),
            // Module with Pico from Addons
            'module-verified-from-addons-api' => new Module([
                'name' => 'module-verified-from-addons-api',
                'prestatrust' => (object) [
                    'pico' => 'https://www.addons.prestashop.com/random-url.jpg',
                ],
            ]),
            // Module with PrestaTrust content
            'module-prestatrust-checked' => new Module([
                'name' => 'module-verified-from-addons-api',
                'author_address' => '0x809A29F600000000000000000000000000000911',
                'prestatrust' => (object) [
                    'pico' => 'https://www.addons.prestashop.com/random-url.jpg',
                ],
            ], [
                'path' => __DIR__ . '/../../../../Resources/modules/ganalytics/',
            ]),
        ];

        $apiClient = $this->getMockBuilder(ApiClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $apiClient
            ->method('setShopUrl')
            ->will($this->returnValue($apiClient));
        $apiClient
            ->method('getPrestaTrustCheck')
            ->will($this->returnValue((object) [
                'hash_trusted' => true,
                'property_trusted' => true,
            ]));

        $translator = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $translator
            ->method('trans')
            ->will($this->returnArgument(0));

        $cache = new ArrayCache();
        $cache->save(
            'module-verified-from-addons-api',
            (object) ['hash' => '366d25acf8172ef93c7086c3ee78f9a2f3e7870356df498d34bda30fb294ae3b']
        );

        $this->prestatrustChecker = new PrestaTrustChecker(
            $cache,
            $apiClient,
            $translator
        );

        $this->modulePresenter = new ModulePresenter(
            $this->createMock(Currency::class),
            $this->createMock(PriceFormatter::class)
        );
    }

    /**
     * This test is about a module not concerned by PrestaTrust.
     * This means its property "author_address" (= Ethereum address) does not exist or is invalid.
     * In that case, we do not expect the class PrestaTrustChecker to modify it.
     *
     * module-under-dev could be a module name which is actually under development, or not available on the
     * marketplace.
     *
     * To make sure the test is complete, we just have to check the dedicated attribute does not exist.
     */
    public function testNotConcernedModuleIsNotModified(): void
    {
        $testedModule = clone $this->modules['module-under-dev'];
        $this->prestatrustChecker->loadDetailsIntoModule($testedModule);

        $this->assertFalse($testedModule->attributes->has('prestatrust'));
    }

    /**
     * Pico = Small badge to display near the module name on the module catalog.
     *
     * When we receive data from the Addons Marketplace API, it seems these picos will be grouped by feature
     * and not in a single array. As we want a generic way to display picos on the module page, we implemented
     * on the module presenter a function to gather all possible picos.
     *
     * In consequence, if the API returns data about PrestaTrust (with a pico inside), we must find it in the "picos"
     * attribute once presented.
     */
    public function testModuleHasPico(): void
    {
        $testedModule = $this->modules['module-verified-from-addons-api'];
        $presentedModule = $this->modulePresenter->present($testedModule);

        $this->assertArrayHasKey('picos', $presentedModule['attributes']);
        $this->assertNotEmpty($presentedModule['attributes']['picos']);
    }

    /**
     * This test is the opposite of the previous one.
     * Until another potential picos sent by the Addons Marketplace API, we can be sure that the pico list will be
     * empty, although existing.
     *
     * As this information is gotten by the API, there is no way for a module developper to add another one.
     */
    public function testModuleHasNotPico(): void
    {
        $testedModule = $this->modules['module-under-dev'];
        $presentedModule = $this->modulePresenter->present($testedModule);

        $this->assertArrayHasKey('picos', $presentedModule['attributes']);
        $this->assertEmpty($presentedModule['attributes']['picos']);
    }

    /**
     * For this test, we use the module "ganalytics" available in the folder resources/modules of our tests.
     *
     * We are faking the PrestaTrust compliancy with a author_adress property which fits the checks (length + 0x).
     * The Addons Marketplace API has been mocked to return a specific response: All checks are OK!
     *
     * This function tests we have all the needed information to display a modal on the module catalog.
     */
    public function testModuleHasCompletePrestaTrustData(): void
    {
        $testedModule = clone $this->modules['module-prestatrust-checked'];
        $this->prestatrustChecker->loadDetailsIntoModule($testedModule);

        $presentedModule = $this->modulePresenter->present($testedModule);

        $this->assertEquals(
            (object) [
                'hash' => '366d25acf8172ef93c7086c3ee78f9a2f3e7870356df498d34bda30fb294ae3b',
                'check_list' => [
                    'integrity' => true,
                    'property' => true,
                ],
                'status' => true,
                'message' => 'Module is authenticated.',
                'pico' => 'https://www.addons.prestashop.com/random-url.jpg',
            ],
            $presentedModule['attributes']['prestatrust']
        );
    }
}
