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

namespace Tests\Unit\Adapter\Module\Tab;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Module\Tab\ModuleTabRegister;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Entity\Repository\TabRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Translation\TranslatorInterface;

class ModuleTabRegisterTest extends TestCase
{
    /**
     * @var string[][]
     */
    protected $expectedTabsToAdd = [
        'gamification' => ['AdminGamification'],
        'doge' => ['Wololo', 'AdminMissing', 'AdminMy'],
        'symfony' => ['UnknownLegacyController'],
        'undeclared_symfony' => ['UndeclaredLegacyController'],
    ];

    /**
     * @var ModuleTabRegister
     */
    protected $tabRegister;

    protected function setUp(): void
    {
        parent::setUp();

        $moduleAdminControllers = [
            ['gamification', ['AdminGamificationController.php']],
            ['doge', ['WololoController.php', 'AdminMyController.php']],
            ['symfony', []],
            ['undeclared_symfony', []],
        ];
        $languages = [
            [
                'id_lang' => 1,
                'name' => 'Français (French)',
                'active' => '1',
                'iso_code' => 'fr',
                'language_code' => 'fr',
                'locale' => 'fr-FR',
                'date_format_lite' => 'd/m/Y',
                'date_format_full' => 'd/m/Y H:i:s',
                'is_rtl' => '0',
                'id_shop' => '1',
                'shops' => [],
            ],
            [
                'id_lang' => 2,
                'name' => 'English (English)',
                'active' => '1',
                'iso_code' => 'en',
                'language_code' => 'en-us',
                'locale' => 'en-US',
                'date_format_lite' => 'm/d/Y',
                'date_format_full' => 'm/d/Y H:i:s',
                'is_rtl' => '0',
                'id_shop' => '1',
                'shops' => [],
            ],
            [
                'id_lang' => 3,
                'name' => 'English (English)',
                'active' => '1',
                'iso_code' => 'en',
                'language_code' => 'en-us',
                'locale' => 'en-US',
                'date_format_lite' => 'm/d/Y',
                'date_format_full' => 'm/d/Y H:i:s',
                'is_rtl' => '0',
                'id_shop' => '1',
                'shops' => [],
            ],
            [
                'id_lang' => 3,
                'name' => 'Català (Catalan)',
                'active' => '1',
                'iso_code' => 'ca',
                'language_code' => 'ca-es',
                'locale' => 'ca-ES',
                'date_format_lite' => 'd/m/Y',
                'date_format_full' => 'Y-m-d H:i:s',
                'is_rtl' => '0',
                'id_shop' => '1',
                'shops' => [],
            ],
        ];

        $this->tabRegister = $this->getMockBuilder(ModuleTabRegister::class)
            ->setMethods(['getModuleAdminControllersFilename'])
            ->setConstructorArgs([
                $this->createMock(TabRepository::class),
                $this->createMock(LangRepository::class),
                $this->createMock(LoggerInterface::class),
                $this->createMock(TranslatorInterface::class),
                $this->buildFilesystemMock(),
                $languages,
                $this->buildRoutingConfigLoaderMock(),
            ])
            ->getMock();
        $this->tabRegister
            ->method('getModuleAdminControllersFilename')
            ->willReturnMap($moduleAdminControllers);
    }

    protected function buildFilesystemMock(): Filesystem
    {
        $filesystemMock = $this->getMockBuilder(Filesystem::class)
            ->setMethods(['exists'])
            ->disableOriginalConstructor()
            ->getMock()
        ;

        // We only need to override the behaviour for undeclared_symfony routing file, to simulate
        // that the file is present and parseable, then the YamlModuleLoader mock does the rest
        $service = $this->createMock(Filesystem::class);
        $filesystemMock
            ->method('exists')
            ->willReturnCallback(function ($filePath) use ($service) {
                if (false !== strpos($filePath, 'undeclared_symfony/config/routes.yml')) {
                    return true;
                }

                return $service->exists($filePath);
            });

        return $filesystemMock;
    }

    protected function buildRoutingConfigLoaderMock(): Loader
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
            ->willReturnCallback(function ($routingFile, $type) {
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

    /**
     * @dataProvider providerTabs
     */
    public function testWorkingTabsAreOk(string $moduleName, array $tabs): void
    {
        foreach ($tabs as $tab) {
            // If exception exception, do not test it here
            if (array_key_exists('exception', $tab)) {
                continue;
            }
            $data = new ParameterBag($tab);
            $this->assertTrue($this->invokeMethod($this->tabRegister, 'checkIsValid', [$moduleName, $data]));
        }
    }

    /**
     * @dataProvider providerTabs
     */
    public function testNonWorkingTabsThrowException(string $moduleName, array $tabs): void
    {
        foreach ($tabs as $tab) {
            // If an exception is expected, test it here
            if (!array_key_exists('exception', $tab)) {
                $this->assertTrue(!array_key_exists('exception', $tab));
                continue;
            }
            $data = new ParameterBag($tab);

            try {
                $this->invokeMethod($this->tabRegister, 'checkIsValid', [$moduleName, $data]);
            } catch (\Exception $e) {
                $this->assertEquals($e->getMessage(), $tab['exception']);

                continue;
            }
            $this->fail('Expected Exception "' . $tab['exception'] . '" has not been raised.');
        }
    }

    /**
     * @dataProvider providerTabs
     */
    public function testTabsListToRegister(string $moduleName, array $data): void
    {
        $tabs = $this->invokeMethod($this->tabRegister, 'addUndeclaredTabs', [$moduleName, $data]);

        // We test there is no unexpected tab to register
        // Be aware, it also include which can throw an exception later when being validated
        foreach ($tabs as $tab) {
            $this->assertTrue(
                in_array($tab['class_name'], $this->expectedTabsToAdd[$moduleName]),
                'Module ' . $moduleName . ' should not register ' . $tab['class_name']
            );
        }

        // In the opposite, we check no tab is missing
        foreach ($this->expectedTabsToAdd[$moduleName] as $moduleAdminController) {
            foreach ($tabs as $tab) {
                if ($tab['class_name'] === $moduleAdminController) {
                    continue 2;
                }
            }
            $this->fail('ModuleAdminController ' . $moduleAdminController . ' is expected but not found in the list to register!');
        }
    }

    public function testTabNameWithOnlyClassName(): void
    {
        $names = 'doge';
        $expectedResult = [1 => $names, 2 => $names, 3 => $names];
        $this->assertEquals($expectedResult, $this->invokeMethod($this->tabRegister, 'getTabNames', [$names]));
    }

    public function testTabNames(): void
    {
        $names = [
            'en' => 'random name',
            'fr' => 'nom généré',
            'de' => 'eine Name',
        ];
        $expectedResult = [1 => $names['fr'], 2 => $names['en'], 3 => $names['en']];
        $this->assertEquals($expectedResult, $this->invokeMethod($this->tabRegister, 'getTabNames', [$names]));
    }

    public function providerTabs(): iterable
    {
        yield [
            'gamification',
            [
                // Test given in PR
                [
                    'name' => 'Merchant Expertise',
                    'class_name' => 'AdminGamification',
                    'parent_class' => 'AdminAdmin',
                ],
            ],
        ];
        yield [
            'doge',
            [
                // minimum data, must work
                [
                    'class_name' => 'AdminMy',
                ],
                // Non-existing class file, must throw an exception
                [
                    'class_name' => 'AdminMissing',
                    'exception' => 'Class "AdminMissingController" not found in controllers/admin nor routing file',
                ],
            ],
        ];
        yield ['symfony',
            [
                // modules with routes are added regardless of the controller existing or not
                [
                    'class_name' => 'UnknownLegacyController',
                    'route_name' => 'some_fancy_symfony_route',
                ],
            ],
        ];
        // No tabs by default, the undeclared one comes from the routing parsing
        yield ['undeclared_symfony',
            [
                [
                    'class_name' => 'UndeclaredLegacyController',
                ],
            ],
        ];
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object Instantiated object that we will run method on
     * @param string $methodName Method name to call
     * @param array $parameters array of parameters to pass into method
     *
     * @return mixed method return
     *
     * @see https://jtreminio.com/2013/03/unit-testing-tutorial-part-3-testing-protected-private-methods-coverage-reports-and-crap/
     */
    protected function invokeMethod(&$object, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
