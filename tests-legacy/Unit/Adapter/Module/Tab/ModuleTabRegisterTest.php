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

namespace LegacyTests\Unit\Adapter\Module\Tab;

use LegacyTests\TestCase\UnitTestCase;
use PrestaShop\PrestaShop\Adapter\Module\Tab\ModuleTabRegister;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ModuleTabRegisterTest extends UnitTestCase
{
    protected $tabsToTest = array(
        'gamification' => array(
            // Test given in PR
            array(
                'name' => 'Merchant Expertise',
                'class_name' => 'AdminGamification',
                'parent_class' => 'AdminAdmin',
            ),
        ),
        'doge' => array(
            // minimum data, must work
            array(
                'class_name' => 'AdminMy',
            ),
            // Non-existing class file, must throw an exception
            array(
                'class_name' => 'AdminMissing',
                'exception' => 'Class "AdminMissingController" not found in controllers/admin nor routing file',
            ),
        ),
        'symfony' => array(
            // modules with routes are added regardless of the controller existing or not
            array(
                'class_name' => 'UnknownLegacyController',
                'route_name' => 'some_fancy_symfony_route',
            ),
        ),
        // No tabs by default, the undeclared one comes from the routing parsing
        'undeclared_symfony' => array(
            array(
                'class_name' => 'UndeclaredLegacyController',
            ),
        ),
    );

    protected $moduleAdminControllers = array(
        array('gamification', array('AdminGamificationController.php')),
        array('doge', array('WololoController.php', 'AdminMyController.php')),
    );

    protected $expectedTabsToAdd = array(
        'gamification' => array('AdminGamification'),
        'doge' => array('Wololo', 'AdminMissing', 'AdminMy'),
        'symfony' => array('UnknownLegacyController'),
        'undeclared_symfony' => array('UndeclaredLegacyController'),
    );

    protected $languages = array(
        array(
            "id_lang" => 1,
            "name" => "Français (French)",
            "active" => "1",
            "iso_code" => "fr",
            "language_code" => "fr",
            "locale" => "fr-FR",
            "date_format_lite" => "d/m/Y",
            "date_format_full" => "d/m/Y H:i:s",
            "is_rtl" => "0",
            "id_shop" => "1",
            "shops" => array(),
        ),
        array(
            "id_lang" => 2,
            "name" => "English (English)",
            "active" => "1",
            "iso_code" => "en",
            "language_code" => "en-us",
            "locale" => "en-US",
            "date_format_lite" => "m/d/Y",
            "date_format_full" => "m/d/Y H:i:s",
            "is_rtl" => "0",
            "id_shop" => "1",
            "shops" => array(),
        ),
        array(
            "id_lang" => 3,
            "name" => "English (English)",
            "active" => "1",
            "iso_code" => "en",
            "language_code" => "en-us",
            "locale" => "en-US",
            "date_format_lite" => "m/d/Y",
            "date_format_full" => "m/d/Y H:i:s",
            "is_rtl" => "0",
            "id_shop" => "1",
            "shops" => array(),
        ),
        array (
            "id_lang" => 3,
            "name" => "Català (Catalan)",
            "active" => "1",
            "iso_code" => "ca",
            "language_code" => "ca-es",
            "locale" => "ca-ES",
            "date_format_lite" => "d/m/Y",
            "date_format_full" => "Y-m-d H:i:s",
            "is_rtl" => "0",
            "id_shop" => "1",
            "shops" => array(),
        ),
    );

    /**
     * @var ModuleTabRegister
     */
    protected $tabRegister;

    protected function setUp()
    {
        parent::setUp();

        $this->setupSfKernel();

        $this->tabRegister = $this->getMockBuilder('PrestaShop\\PrestaShop\\Adapter\\Module\\Tab\\ModuleTabRegister')
            ->setMethods(array('getModuleAdminControllersFilename'))
            ->setConstructorArgs(array(
                $this->sfKernel->getContainer()->get('prestashop.core.admin.tab.repository'),
                $this->sfKernel->getContainer()->get('prestashop.core.admin.lang.repository'),
                $this->sfKernel->getContainer()->get('logger'),
                $this->sfKernel->getContainer()->get('translator'),
                $this->buildFilesystemMock(),
                $this->languages,
                $this->buildRoutingConfigLoaderMock(),
            ))
            ->getMock();
        $this->tabRegister
            ->method('getModuleAdminControllersFilename')
            ->will($this->returnValueMap($this->moduleAdminControllers));
    }

    protected function buildFilesystemMock()
    {
        $filesystemMock = $this->getMockBuilder(Filesystem::class)
            ->setMethods(['exists'])
            ->disableOriginalConstructor()
            ->getMock()
        ;

        // We only need to override the behaviour for undeclared_symfony routing file, to simulate
        // that the file is present and parseable, then the YamlModuleLoader mock does the rest
        $service = $this->sfKernel->getContainer()->get('filesystem');
        $filesystemMock
            ->method('exists')
            ->willReturnCallback(function($filePath) use($service) {
                if (false !== strpos($filePath, 'undeclared_symfony/config/routes.yml')) {
                    return true;
                }

                return $service->exists($filePath);
            });

        return $filesystemMock;
    }

    protected function buildRoutingConfigLoaderMock()
    {
        $moduleRoutingLoader = $this->getMockBuilder(Loader::class)
            ->setMethods(['import', 'load', 'supports', 'getResolver', 'setResolver'])
            ->disableOriginalConstructor()
            ->getMock()
        ;

        // We only need to mock the import method, it returns a RouteCollection, by default the mock returns a useless
        // Route object, but ONLY for the undeclared_symfony module it returns an additional one with the _legacy_controller
        // option that will add an undeclared controller
        $moduleRoutingLoader
            ->method('import')
            ->willReturnCallback(function($routingFile, $type) {
                $routeCollection = new RouteCollection();
                $simpleRoute = new Route('/nowhere', [
                    '_controller' => 'PrestaShop\\Module\\Test\\SymfonyController::someAction',
                ]);
                $routeCollection->add('not_detected_route', $simpleRoute);

                if (false !== strpos($routingFile, 'symfony/config/routes.yml')) {
                    $route = new Route('/hidden-url', [
                        '_controller' => 'PrestaShop\\Module\\Test\\SecuredSymfonyController::securedAction',
                        '_legacy_controller' => 'UndeclaredLegacyController',
                    ]);
                    $routeCollection->add('route_to_be_detected', $route);
                }

                return $routeCollection;
            });

        return $moduleRoutingLoader;
    }

    public function testWorkingTabsAreOk()
    {
        foreach ($this->tabsToTest as $moduleName => $tabs) {
            foreach ($tabs as $tab) {
                // If exception exception, do not test it here
                if (array_key_exists('exception', $tab)) {
                    continue;
                }
                $data = new ParameterBag($tab);
                $this->assertTrue($this->invokeMethod($this->tabRegister, 'checkIsValid', array($moduleName, $data)));
            }
        }
    }

    public function testNonWorkingTabsThrowException()
    {
        foreach ($this->tabsToTest as $moduleName => $tabs) {
            foreach ($tabs as $tab) {
                // If an exception is expected, test it here
                if (!array_key_exists('exception', $tab)) {
                    continue;
                }
                $data = new ParameterBag($tab);

                try {
                    $this->invokeMethod($this->tabRegister, 'checkIsValid', array($moduleName, $data));
                } catch (\Exception $e) {
                    $this->assertEquals($e->getMessage(), $tab['exception']);

                    continue;
                }
                $this->fail('Expected Exception "'.$tab['exception'].'" has not been raised.');
            }
        }
    }

    public function testTabsListToRegister()
    {
        foreach ($this->tabsToTest as $moduleName => $data) {
            $tabs = $this->invokeMethod($this->tabRegister, 'addUndeclaredTabs', array($moduleName, $data));

            // We test there is no unexpected tab to register
            // Be aware, it also include which can throw an exception later when being validated
            foreach($tabs as $tab) {
                $this->assertTrue(
                        in_array($tab['class_name'], $this->expectedTabsToAdd[$moduleName]),
                        'Module '.$moduleName.' should not register '.$tab['class_name']
                );
            }

            // In the opposite, we check no tab is missing
            foreach ($this->expectedTabsToAdd[$moduleName] as $moduleAdminController) {
                foreach ($tabs as $tab) {
                    if ($tab['class_name'] === $moduleAdminController) {
                        continue 2;
                    }
                }
                $this->fail('ModuleAdminController '.$moduleAdminController.' is expected but not found in the list to register!');
            }
        }
    }

    public function testTabNameWithOnlyClassName()
    {
        $names = 'doge';
        $expectedResult = array(1 => $names, 2 => $names, 3 => $names);
        $this->assertEquals($expectedResult, $this->invokeMethod($this->tabRegister, 'getTabNames', array($names)));
    }

    public function testTabNames()
    {
        $names = array(
            'en' => 'random name',
            'fr' => 'nom généré',
            'de' => 'eine Name',
        );
        $expectedResult = array(1 => $names['fr'], 2 => $names['en'], 3 => $names['en']);
        $this->assertEquals($expectedResult, $this->invokeMethod($this->tabRegister, 'getTabNames', array($names)));
    }
}
