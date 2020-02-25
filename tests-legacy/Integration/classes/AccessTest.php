<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace LegacyTests\Integration\classes;

use LegacyTests\TestCase\IntegrationTestCase;
use Access;
use Db;
use Language;
use Tab;

class AccessTest extends IntegrationTestCase
{
    /** @var Tab */
    private $classNameTab;

    /** @var Tab */
    private $classNameTabChild;

    /** @var Tab */
    private $routeNameTab;

    /** @var Tab */
    private $routeNameTabChild;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->removeTestTabs();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->removeTestTabs();
    }

    public function testFindSlugByIdTab()
    {
        $this->createTestTabs();
        $classSlug = Access::findSlugByIdTab($this->classNameTab->id);
        $this->assertEquals('ROLE_MOD_TAB_ADMINCLASSNAMETEST_', $classSlug);

        $routeSlug = Access::findSlugByIdTab($this->routeNameTab->id);
        $this->assertEquals('ROLE_MOD_TAB_ADMINCLASSNAMETEST_', $routeSlug);
    }

    public function testFindSlugByIdParentTab()
    {
        $this->createTestTabs();
        $this->createTestTabsChildren();

        $classSlugs = Access::findSlugByIdParentTab($this->classNameTab->id);
        $this->assertNotEmpty($classSlugs);
        $this->assertEquals('AdminClassNameTestChild', $classSlugs[0]['class_name']);

        $routeSlugs = Access::findSlugByIdParentTab($this->routeNameTab->id);
        $this->assertNotEmpty($routeSlugs);
        $this->assertEquals('AdminClassNameTestChild', $classSlugs[0]['class_name']);
    }

    public function testSluggifyTab()
    {
        $this->assertEquals('ROLE_MOD_TAB_ADMINCLASSNAMETEST_', Access::sluggifyTab(['class_name' => 'AdminClassNameTest']));
        $this->assertEquals('ROLE_MOD_TAB_ADMINCLASSNAMETEST_READ', Access::sluggifyTab(['class_name' => 'AdminClassNameTest'], 'READ'));
        $this->assertEquals('ROLE_MOD_TAB_ADMINCLASSNAMETEST_', Access::sluggifyTab(['class_name' => 'AdminClassNameTest', 'route_name' => null]));
        $this->assertEquals('ROLE_MOD_TAB_ADMINCLASSNAMETEST_read', Access::sluggifyTab(['class_name' => 'AdminClassNameTest', 'route_name' => null], 'read'));

        $this->assertEquals('ROLE_MOD_TAB_ADMINCLASSNAMETEST_', Access::sluggifyTab(['class_name' => 'AdminClassNameTest', 'route_name' => 'admin_route_name_test']));
        $this->assertEquals('ROLE_MOD_TAB_ADMINCLASSNAMETEST_READ', Access::sluggifyTab(['class_name' => 'AdminClassNameTest', 'route_name' => 'admin_route_name_test'], 'READ'));
    }

    public function testFindIdTabByAuthSlug()
    {
        $this->createTestTabs();

        $idTab = Access::findIdTabByAuthSlug('ROLE_MOD_TAB_ADMINCLASSNAMETEST_READ');
        $this->assertEquals($this->classNameTab->id, $idTab);
    }

    private function createTestTabs()
    {
        $this->classNameTab = new Tab();
        $this->classNameTab->active = 1;
        $this->classNameTab->class_name = 'AdminClassNameTest';
        $this->classNameTab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $this->classNameTab->name[$lang['id_lang']] = 'Class name tab';
        }
        $this->classNameTab->id_parent = (int) Tab::getIdFromClassName('AdminParentThemes');
        $this->classNameTab->module = 'module_test_tab';
        $this->classNameTab->add(true, true);

        $this->routeNameTab = new Tab();
        $this->routeNameTab->active = 1;
        $this->routeNameTab->class_name = 'AdminClassNameTest';
        $this->routeNameTab->route_name = 'admin_route_name_test';
        $this->routeNameTab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $this->routeNameTab->name[$lang['id_lang']] = 'Route name tab';
        }
        $this->routeNameTab->id_parent = (int) Tab::getIdFromClassName('AdminParentThemes');
        $this->routeNameTab->module = 'module_test_tab';
        $this->routeNameTab->add(true, true);
    }

    private function createTestTabsChildren()
    {
        $this->classNameTabChild = new Tab();
        $this->classNameTabChild->active = 1;
        $this->classNameTabChild->class_name = 'AdminClassNameTestChild';
        $this->classNameTabChild->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $this->classNameTabChild->name[$lang['id_lang']] = 'Class name tab child';
        }
        $this->classNameTabChild->id_parent = $this->classNameTab->id;
        $this->classNameTabChild->module = 'module_test_tab';
        $this->classNameTabChild->add(true, true);

        $this->routeNameTabChild = new Tab();
        $this->routeNameTabChild->active = 1;
        $this->routeNameTabChild->class_name = 'AdminClassNameTestChild';
        $this->routeNameTabChild->route_name = 'admin_route_name_test_child';
        $this->routeNameTabChild->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $this->routeNameTabChild->name[$lang['id_lang']] = 'Route name tab child';
        }
        $this->routeNameTabChild->id_parent = $this->routeNameTab->id;
        $this->routeNameTabChild->module = 'module_test_tab';
        $this->routeNameTabChild->add(true, true);
    }

    private function removeTestTabs()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_tab, class_name, route_name FROM `' . _DB_PREFIX_ . 'tab` WHERE `module` = "module_test_tab"', true, false);
        foreach ($result as $tabRow) {
            $tab = new Tab($tabRow['id_tab']);
            $tab->delete();
        }
        $this->classNameTab = null;
        $this->routeNameTab = null;
        $this->classNameTabChild = null;
        $this->routeNameTabChild = null;
    }
}
