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

namespace Tests\Integration\PrestaShopBundle\Test;

use PrestaShop\PrestaShop\Adapter\Module\Module;
use Psr\Log\NullLogger;
use Tests\TestCase\Module as ModuleHelper;
use Tests\PrestaShopBundle\Utils\DatabaseCreator as Database;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as TestCase;

class WebTestCase extends TestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    /**
     * @var \Symfony\Component\Routing\Router
     */
    protected $router;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    public static function setUpBeforeClass()
    {
        Database::restoreTestDB();
    }

    protected function setUp()
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->router = self::$kernel->getContainer()->get('router');
        $this->translator = self::$kernel->getContainer()->get('translator');

        $employeeMock = $this->getMockBuilder('\Employee')
            ->getMock();
        $employeeMock->id_profile = 1;

        $contextMock = $this->getMockBuilder('\Context')
            ->setMethods(array('getTranslator', 'getBaseURL'))
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock->method('getTranslator')
            ->will($this->returnValue($this->translator));

        $contextMock->employee = $employeeMock;

        $shopMock = $this->getMockBuilder('\Shop')
            ->setMethods(array('getBaseURL'))
            ->getMock();
        $shopMock->id = 1;
        $shopMock->method('getBaseURL')
            ->willReturn('my-awesome-url.com');

        $contextMock->shop = $shopMock;

        $themeMock = $this->getMockBuilder('\Theme')
            ->setMethods(array('getName'))
            ->disableOriginalConstructor()
            ->getMock();

        $themeMock->method('getName')
            ->willReturn('classic');

        $contextMock->shop->theme = $themeMock;

        $languageMock = $this->getMockBuilder('\Language')
            ->disableAutoload()
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->language = $languageMock;

        $currencyMock = $this->getMockBuilder('\Currency')
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock->currency = $currencyMock;

        $legacyContextMock = $this->getMockBuilder('\PrestaShop\PrestaShop\Adapter\LegacyContext')
            ->setMethods([
                'getContext',
                'getEmployeeLanguageIso',
                'getEmployeeCurrency',
                'getRootUrl',
                'getLanguages'
            ])
            ->disableAutoload()
            ->disableOriginalConstructor()
            ->getMock();

        $legacyContextMock->method('getContext')
            ->will($this->returnValue($contextMock));

        $legacyContextMock->method('getLanguages')
            ->will(
                self::returnValue(
                    [
                        [
                            'id_lang' => '1',
                            'name' => 'English (English)',
                            'iso_code' => 'en',
                            'language_code' => 'en-us',
                            'locale' => 'en-US',
                        ],
                        [
                            'id_lang' => '2',
                            'name' => 'Français (French)',
                            'iso_code' => 'fr',
                            'language_code' => 'fr',
                            'locale' => 'fr-FR'
                        ]
                    ]
                )
            );

        $this->container = self::$kernel->getContainer();

        $this->container->set('prestashop.adapter.legacy.context', $legacyContextMock);
        $this->container->set('logger', new NullLogger());
    }

    protected function enableDemoMode()
    {
        $configurationMock = $this->getMockBuilder('\PrestaShop\PrestaShop\Adapter\Configuration')
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->disableAutoload()
            ->getMock();

        $values = array(
            array('_PS_MODE_DEMO_', null, true),
            array('_PS_MODULE_DIR_', null, __DIR__.'/../../../resources/modules/'),
        );

        $configurationMock->method('get')
            ->will($this->returnValueMap($values));

        $this->container->set('prestashop.adapter.legacy.configuration', $configurationMock);
    }

    /**
     * Little helper to retrieve service from Container.
     *
     * @param $serviceName
     *
     * @return \StdClass
     * @throws \Exception
     */
    protected function get($serviceName)
    {
        return $this->container->get($serviceName);
    }

    /**
     * Retrieves an instance of Module by module name
     * @param $moduleName
     *
     * @return Module
     * @throws \Exception
     */
    protected function getModule($moduleName)
    {
        return $this->get('prestashop.core.admin.module.repository')->getModule($moduleName);
    }

    /**
     * Copy a module from "module" folder in "tests" folder to the right one.
     * Also install the module.
     *
     * @param string $moduleName
     * @return bool
     * @throws \Exception
     */
    protected function installModule($moduleName)
    {
        if (ModuleHelper::addModuleInRealFolder($moduleName)) {
            $this->getModule($moduleName)->onInstall();
            $this->clearCache();

            return true;
        }

        return false;
    }

    /**
     * Remove a module from "modules" folder.
     * Also uninstall the module.
     *
     * @param string $moduleName
     * @return bool
     * @throws \Exception
     */
    protected function uninstallModule($moduleName)
    {
        if (ModuleHelper::removeModuleFromRealFolder($moduleName)) {
            $this->getModule($moduleName)->onUninstall();
            $this->clearCache();

            return true;
        }

        return false;
    }

    /**
     * Allow to clear the cache.
     */
    private function clearCache()
    {
        $this->get('prestashop.adapter.cache_clearer')->clearAllCaches();
    }
}
