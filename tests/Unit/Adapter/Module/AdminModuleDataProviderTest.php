<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\tests\Unit\Adapter\Module;

use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use Phake;

class AdminModuleDataProviderTest extends UnitTestCase
{
    const NOTICE = '[AdminModuleDataProvider] ';
    private $httpHostNotFound = false;
    private $languageISOCode;
    private $legacyContext;
    private $addonsDataProviderS;
    private $adminModuleDataProvider;

    public function setUp()
    {
        parent::setup();

        $this->languageISOCode = 'en';
        $this->legacyContext = Phake::partialMock('PrestaShop\\PrestaShop\\Adapter\\LegacyContext');
        Phake::when($this->legacyContext)->getAdminBaseUrl()->thenReturn('admin_fake_base');

        if (!isset($_SERVER['HTTP_HOST'])) {
            $this->httpHostNotFound = true;
            $_SERVER['HTTP_HOST'] = 'localhost';
        }

        $this->setupSfKernel();
        $this->sfRouter = $this->sfKernel->getContainer()->get('router');

        $this->addonsDataProviderS = $this->getMockBuilder('PrestaShop\PrestaShop\Adapter\Addons\AddonsDataProvider')
            ->getMock();

        /* The module catalog will contains only 5 modules for theses tests */
        $fakeModules =  [
            $this->fakeModule(1,
                'pm_advancedpack',
                'Advanced Pack 5 - Create ​​bundles of products',
                'Cross-selling & Product Bundles',
                'Allows the sale batch using any stocks actually available products composing your packs, and offers the opportunity to apply business operations'
            ),
            $this->fakeModule(2,
                'cmcicpaiement',
                'CM-CIC / Monetico Payment in one instalment',
                'Payment by Card or Wallet',
                'Accept bank card payments in your online shop with the CM-CIC / Monetico p@yment&nbsp;module!  This very popular means of secure payment reassures your customers when they make their purchases in your'
            ),
            $this->fakeModule(3,
                'bitcoinpayment',
                'Coinbase Payment (Bitcoin)',
                'Other Payment Methods',
                'Use the Coinbase payment module to give your customers the possibility of paying for their purchases in your store with Bitcoin!  This module uses the API from Coinbase, a globally recognized Bitcoin'
            ),
            $this->fakeModule(4,
                'fake_module',
                'Fake module 1',
                'PHPUnit Fakes',
                ''
            ),
            $this->fakeModule(5,
                'fake_module_2',
                'Fake module 2',
                'PHPUnit Fakes',
                ''
            ),
        ];

        /* we need to fake cache wih fake catalog */
        $this->clearModuleCache();
        file_put_contents(_PS_CACHE_DIR_.'en_catalog_modules.json', json_encode($fakeModules, true));

        $this->adminModuleDataProvider = new AdminModuleDataProvider($this->languageISOCode, $this->sfRouter, $this->addonsDataProviderS);
    }

    public function testGetListOfModulesOk()
    {
        $modules = $this->adminModuleDataProvider->getCatalogModules();

        $this->assertGreaterThan(0, count($modules), sprintf('%s expected a list of modules, received none.', self::NOTICE));
    }

    public function testSearchCanResultNoResultsOk()
    {
        $filters = ['search' => 'doge'];
        $modules = $this->adminModuleDataProvider->getCatalogModules($filters);

        $this->assertCount(0, $modules, sprintf('%s expected 0 modules, received %s.', self::NOTICE, count($modules)));
    }

    public function testSearchWithUnknownFilterCriteriaReturnAllOk()
    {
        $filters = ['random_filter' => 'doge'];
        $modulesWithFilter = $this->adminModuleDataProvider->getCatalogModules($filters);

        $modules = $this->adminModuleDataProvider->getCatalogModules();

        $this->assertSame($modulesWithFilter, $modules, sprintf('%s expected undefined filter have no effect on search.', self::NOTICE));
    }

    public function testSearchForASpecificModuleOk()
    {
        $filters = ['search' => 'advancedpack'];
        $modules = $this->adminModuleDataProvider->getCatalogModules($filters);

        $this->assertCount(1, $modules);
    }

    public function testSearchForASpecificModuleHaveMultipleResultsOk()
    {
        $filters = ['search' => 'payment advanced'];
        $modules = $this->adminModuleDataProvider->getCatalogModules($filters);

        $this->assertCount(3, $modules);
    }

    public function testCallToAddonsShouldReturnSameResultOk()
    {
        $mock = $this->getMock('PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider',
            ['convertJsonForNewCatalog'],
            [
                'languageISO' => $this->languageISOCode,
                'router' => $this->sfRouter,
                'addonsDataProvider' => $this->addonsDataProviderS
            ]
        );
        $mock->expects($this->once())->method('convertJsonForNewCatalog')->will($this->returnValue($this->adminModuleDataProvider->getCatalogModules()));

        $mock->clearCatalogCache();

        $modules = $mock->getCatalogModules();
        $modules2 = $mock->getCatalogModules();

        $this->assertEquals($modules2, $modules);
    }

    public function testProductTypeShouldBeCorrectOk()
    {
        $this->clearModuleCache();
        $modules = $this->adminModuleDataProvider->getCatalogModules();
        $possible_values = ['module', 'service', 'theme'];
        foreach ($modules as $module) {
            $this->assertTrue(in_array($module->productType, $possible_values));
        }
    }

    public function teardown()
    {
        parent::teardown();

        if ($this->httpHostNotFound) {
            unset($_SERVER['HTTP_HOST']);
        }

        $this->clearModuleCache();
    }

    private function fakeModule($id, $name, $displayName, $categoryName, $description) {
        $fakeModule = new \stdClass();
        $fakeModule->id = $id;
        $fakeModule->name = $name;
        $fakeModule->displayName = $displayName;
        $fakeModule->categoryName = $categoryName;
        $fakeModule->description = $description;

        return $fakeModule;
    }

    private function clearModuleCache()
    {
        if(file_exists(_PS_CACHE_DIR_.'en_catalog_modules.json')) {
            unlink(_PS_CACHE_DIR_.'en_catalog_modules.json');
        }
    }
}
