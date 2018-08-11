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
namespace Tests\Unit\Adapter\Module\Tab;

use PrestaShop\PrestaShop\Adapter\Module\Tab\ModuleTabRegister;
use Tests\TestCase\UnitTestCase;
use Symfony\Component\HttpFoundation\ParameterBag;

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
                'exception' => 'Class "AdminMissingController" not found in controllers/admin',
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
    );

    protected  $languages = array(
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
        )
    );

    /**
     * @var ModuleTabRegister
     */
    protected $tabRegister;

    public function setUp()
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
                $this->sfKernel->getContainer()->get('filesystem'),
                $this->languages,
            ))
            ->getMock()
        ;
        $this->tabRegister
            ->method('getModuleAdminControllersFilename')
            ->will($this->returnValueMap($this->moduleAdminControllers));
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
                        'Module '.$moduleName.' should not register '.$tab['class_name']);
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
