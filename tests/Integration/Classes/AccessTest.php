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

namespace Tests\Integration\Classes;

use Access;
use Db;
use Language;
use PHPUnit\Framework\TestCase;
use Tab;

class AccessTest extends TestCase
{
    /** @var Tab|null */
    private $classNameTab;

    /** @var Tab|null */
    private $classNameTabChild;

    /** @var Tab|null */
    private $routeNameTab;

    /** @var Tab|null */
    private $routeNameTabChild;

    protected function setUp(): void
    {
        parent::setUp();

        // Remove tabs
        $result = Db::getInstance((bool) _PS_USE_SQL_SLAVE_)->executeS(
            'SELECT id_tab, class_name, route_name FROM `' . _DB_PREFIX_ . 'tab` WHERE `module` = "module_test_tab"',
            true,
            false
        );
        foreach ($result as $row) {
            $tab = new Tab($row['id_tab']);
            $tab->delete();
        }

        $this->classNameTab = $this->routeNameTab = $this->classNameTabChild = $this->routeNameTabChild = null;
    }

    public function testFindSlugByIdTab(): void
    {
        $this->createTestTabs();

        $this->assertEquals('ROLE_MOD_TAB_ADMINCLASSNAMETEST_', Access::findSlugByIdTab($this->classNameTab->id));
        $this->assertEquals('ROLE_MOD_TAB_ADMINCLASSNAMETEST_', Access::findSlugByIdTab($this->routeNameTab->id));
    }

    public function testFindSlugByIdParentTab(): void
    {
        $this->createTestTabs();
        $this->createTestTabsChildren();

        $classSlugs = Access::findSlugByIdParentTab($this->classNameTab->id);
        $this->assertNotEmpty($classSlugs);
        $this->assertEquals('AdminClassNameTestChild', $classSlugs[0]['class_name']);

        $routeSlugs = Access::findSlugByIdParentTab($this->routeNameTab->id);
        $this->assertNotEmpty($routeSlugs);
        $this->assertEquals('AdminClassNameTestChild', $routeSlugs[0]['class_name']);
    }

    /**
     * @dataProvider providerSluggifyTab
     */
    public function testSluggifyTab(string $expected, array $tab, string $authorization): void
    {
        $this->assertEquals(
            $expected,
            Access::sluggifyTab($tab, $authorization)
        );
    }

    public function providerSluggifyTab(): iterable
    {
        yield [
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_',
            ['class_name' => 'AdminClassNameTest'],
            '',
        ];
        yield [
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_READ',
            ['class_name' => 'AdminClassNameTest'],
            'READ',
        ];
        yield [
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_',
            ['class_name' => 'AdminClassNameTest', 'route_name' => null],
            '',
        ];
        yield [
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_read',
            ['class_name' => 'AdminClassNameTest', 'route_name' => null],
            'read',
        ];
        yield [
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_',
            ['class_name' => 'AdminClassNameTest', 'route_name' => 'admin_route_name_test'],
            '',
        ];
        yield [
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_READ',
            ['class_name' => 'AdminClassNameTest', 'route_name' => 'admin_route_name_test'],
            'READ',
        ];
    }

    public function testFindIdTabByAuthSlug(): void
    {
        $this->createTestTabs();

        $this->assertEquals($this->classNameTab->id, Access::findIdTabByAuthSlug('ROLE_MOD_TAB_ADMINCLASSNAMETEST_READ'));
    }

    private function createTestTabs(): void
    {
        $this->classNameTab = new Tab();
        $this->classNameTab->active = true;
        $this->classNameTab->class_name = 'AdminClassNameTest';
        $this->classNameTab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $this->classNameTab->name[$lang['id_lang']] = 'Class name tab';
        }
        $this->classNameTab->id_parent = (int) Tab::getIdFromClassName('AdminParentThemes');
        $this->classNameTab->module = 'module_test_tab';

        $this->routeNameTab = clone $this->classNameTab;
        $this->routeNameTab->route_name = 'admin_route_name_test';
        $this->routeNameTab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $this->routeNameTab->name[$lang['id_lang']] = 'Route name tab';
        }

        // Add in DB
        $this->classNameTab->add(true, true);
        $this->routeNameTab->add(true, true);
    }

    private function createTestTabsChildren(): void
    {
        $this->classNameTabChild = new Tab();
        $this->classNameTabChild->active = true;
        $this->classNameTabChild->class_name = 'AdminClassNameTestChild';
        $this->classNameTabChild->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $this->classNameTabChild->name[$lang['id_lang']] = 'Class name tab child';
        }
        $this->classNameTabChild->id_parent = $this->classNameTab->id;
        $this->classNameTabChild->module = 'module_test_tab';

        $this->routeNameTabChild = clone $this->classNameTabChild;
        $this->routeNameTabChild->route_name = 'admin_route_name_test_child';
        $this->routeNameTabChild->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $this->routeNameTabChild->name[$lang['id_lang']] = 'Route name tab child';
        }
        $this->routeNameTabChild->id_parent = $this->routeNameTab->id;

        // Add in DB
        $this->classNameTabChild->add(true, true);
        $this->routeNameTabChild->add(true, true);
    }
}
