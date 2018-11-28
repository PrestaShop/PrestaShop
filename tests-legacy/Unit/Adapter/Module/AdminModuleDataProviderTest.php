<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace Tests\Unit\Adapter\Module;

use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use Tests\TestCase\UnitTestCase;
use Phake;

class AdminModuleDataProviderTest extends UnitTestCase
{
    const NOTICE = '[AdminModuleDataProvider] ';
    private $httpHostNotFound = false;
    private $languageISOCode;
    private $legacyContext;
    private $addonsDataProviderS;
    private $categoriesProviderS;
    private $adminModuleDataProvider;
    private $moduleDataProviderS;

    public function setUp()
    {
        parent::setUp();

        $this->legacyContext = Phake::partialMock('PrestaShop\\PrestaShop\\Adapter\\LegacyContext');
        Phake::when($this->legacyContext)->getAdminBaseUrl()->thenReturn('admin_fake_base');

        if (!isset($_SERVER['HTTP_HOST'])) {
            $this->httpHostNotFound = true;
            $_SERVER['HTTP_HOST'] = 'localhost';
        }

        $this->setupSfKernel();
        $this->translator = $this->sfKernel->getContainer()->get('translator');
        list($this->languageISOCode) = explode('-', $this->translator->getLocale());
        $this->logger = $this->sfKernel->getContainer()->get('logger');

        $this->addonsDataProviderS = $this->getMockBuilder('PrestaShop\PrestaShop\Adapter\Addons\AddonsDataProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $this->categoriesProviderS = $this->getMockBuilder('PrestaShopBundle\Service\DataProvider\Admin\CategoriesProvider')
            ->disableOriginalConstructor()
            ->getmock();

        $this->moduleDataProviderS = $this->getMockBuilder('PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider')
            ->disableOriginalConstructor()
            ->getMock();

        /* The module catalog will contains only 5 modules for theses tests */
        $fakeModules = array(
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
        );

        $this->cacheProviderS = Phake::partialMock('Doctrine\Common\Cache\CacheProvider');
        Phake::when($this->cacheProviderS)->contains($this->languageISOCode.'_addons_modules')->thenReturn(true);
        Phake::when($this->cacheProviderS)->fetch($this->languageISOCode.'_addons_modules')->thenReturn($fakeModules);

        $this->adminModuleDataProvider = new AdminModuleDataProvider(
            $this->translator,
            $this->logger,
            $this->addonsDataProviderS,
            $this->categoriesProviderS,
            $this->moduleDataProviderS,
            $this->cacheProviderS
        );
    }

    public function testGetListOfModulesOk()
    {
        $modules = $this->adminModuleDataProvider->getCatalogModules();

        $this->assertGreaterThan(0, count($modules), sprintf('%s expected a list of modules, received none.', self::NOTICE));
    }

    public function testSearchCanResultNoResultsOk()
    {
        $filters = array('search' => 'doge');
        $modules = $this->adminModuleDataProvider->getCatalogModules($filters);

        $this->assertCount(0, $modules, sprintf('%s expected 0 modules, received %s.', self::NOTICE, count($modules)));
    }

    public function testSearchWithUnknownFilterCriteriaReturnAllOk()
    {
        $filters = array('random_filter' => 'doge');
        $modulesWithFilter = $this->adminModuleDataProvider->getCatalogModules($filters);

        $modules = $this->adminModuleDataProvider->getCatalogModules();

        $this->assertSame($modulesWithFilter, $modules, sprintf('%s expected undefined filter have no effect on search.', self::NOTICE));
    }

    public function testSearchForASpecificModuleOk()
    {
        $filters = array('search' => 'advancedpack');
        $modules = $this->adminModuleDataProvider->getCatalogModules($filters);

        $this->assertCount(1, $modules);
    }

    public function testSearchForASpecificModuleHaveMultipleResultsOk()
    {
        $filters = array('search' => 'payment advanced');
        $modules = $this->adminModuleDataProvider->getCatalogModules($filters);

        $this->assertCount(3, $modules);
    }

    public function testCallToAddonsShouldReturnSameResultOk()
    {
        $mock = $this->getMockBuilder('PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider')
            ->setConstructorArgs(array(
                'languageISO' => $this->translator,
                'logger' => $this->logger,
                'addonsDataProvider' => $this->addonsDataProviderS,
                'categoriesProvider' => $this->categoriesProviderS,
                'moduleDataProvider' => $this->moduleDataProviderS,
                'cacheProvider' => $this->cacheProviderS,
            ))
            ->setMethods(array('convertJsonForNewCatalog'))
            ->getMock();

        $mock->clearCatalogCache();

        $modules = $mock->getCatalogModules();
        $modules2 = $mock->getCatalogModules();

        $this->assertEquals($modules2, $modules);
    }

    public function teardown()
    {
        parent::teardown();

        if ($this->httpHostNotFound) {
            unset($_SERVER['HTTP_HOST']);
        }
    }

    private function fakeModule($id, $name, $displayName, $categoryName, $description)
    {
        $fakeModule = new \stdClass();
        $fakeModule->id = $id;
        $fakeModule->name = $name;
        $fakeModule->displayName = $displayName;
        $fakeModule->categoryName = $categoryName;
        $fakeModule->description = $description;

        return $fakeModule;
    }
}
