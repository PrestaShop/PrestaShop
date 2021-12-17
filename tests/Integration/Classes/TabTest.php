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

namespace Tests\Integration\Classes;

use Db;
use Language;
use PHPUnit\Framework\TestCase;
use Tab;

class TabTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::removeTestTabs();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::removeTestTabs();
    }

    private static function removeTestTabs(): void
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_tab, class_name, route_name FROM `' . _DB_PREFIX_ . 'tab` WHERE `module` = "module_test_tab"', true, false);
        foreach ($result as $tabRow) {
            $tab = new Tab($tabRow['id_tab']);
            $tab->delete();
        }
    }

    public function testAddTabWithClassName(): void
    {
        $expectedRoles = [
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_CREATE',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_READ',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_UPDATE',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_DELETE',
        ];
        $this->assertUnexpectedRoles($expectedRoles);

        $classNameTab = new Tab();
        $classNameTab->active = 1;
        $classNameTab->class_name = 'AdminClassNameTest';
        $classNameTab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $classNameTab->name[$lang['id_lang']] = 'Class name tab';
        }
        $classNameTab->id_parent = (int) Tab::getIdFromClassName('AdminParentThemes');
        $classNameTab->module = 'module_test_tab';
        $classNameTab->add();

        $this->assertExpectedRoles($expectedRoles);

        // testDeleteTabWithClassName
        $unexpectedRoles = [
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_CREATE',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_READ',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_UPDATE',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_DELETE',
        ];
        $this->assertExpectedRoles($unexpectedRoles);

        $tab = new Tab(Tab::getIdFromClassName('AdminClassNameTest'));
        $this->assertNotFalse($tab->id);
        $this->assertEquals('AdminClassNameTest', $tab->class_name);
        $tab->delete();

        $this->assertUnexpectedRoles($unexpectedRoles);
    }

    public function testAddMultipleTabsWithClassName(): void
    {
        $this->removeTestTabs();
        $unexpectedRoles = [
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_CREATE',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_READ',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_UPDATE',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_DELETE',
        ];
        $this->assertUnexpectedRoles($unexpectedRoles);

        for ($i = 0; $i < 3; ++$i) {
            $classNameTab = new Tab();
            $classNameTab->active = 1;
            $classNameTab->class_name = 'AdminClassNameTest';
            $classNameTab->name = [];
            foreach (Language::getLanguages(true) as $lang) {
                $classNameTab->name[$lang['id_lang']] = 'Class name tab';
            }
            $classNameTab->id_parent = (int) Tab::getIdFromClassName('AdminParentThemes');
            $classNameTab->module = 'module_test_tab';
            $classNameTab->add();
        }

        $expectedRoles = [
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_CREATE',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_READ',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_UPDATE',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_DELETE',
        ];
        $this->assertExpectedRoles($expectedRoles);
    }

    public function testAddTabWithRouteName(): void
    {
        $this->removeTestTabs();
        $unexpectedRoles = [
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_CREATE',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_READ',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_UPDATE',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_DELETE',
        ];
        $this->assertUnexpectedRoles($unexpectedRoles);

        $routeNameTab = new Tab();
        $routeNameTab->active = 1;
        $routeNameTab->class_name = 'AdminClassNameTest';
        $routeNameTab->route_name = 'admin_route_name_test';
        $routeNameTab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $routeNameTab->name[$lang['id_lang']] = 'Route name tab';
        }
        $routeNameTab->id_parent = (int) Tab::getIdFromClassName('AdminParentThemes');
        $routeNameTab->module = 'module_test_tab';
        $routeNameTab->add();

        $expectedRoles = [
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_CREATE',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_READ',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_UPDATE',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_DELETE',
        ];
        $this->assertExpectedRoles($expectedRoles);

        // testDeleteTabWithRouteName
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT id_tab FROM `' . _DB_PREFIX_ . 'tab` WHERE `route_name` = "admin_route_name_test"',
            true,
            false
        );
        $this->assertNotEmpty($result);

        $unexpectedRoles = [
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_CREATE',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_READ',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_UPDATE',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_DELETE',
        ];
        $this->assertExpectedRoles($unexpectedRoles);

        $tab = new Tab($result[0]['id_tab']);
        $this->assertNotFalse($tab->id);
        $this->assertEquals('admin_route_name_test', $tab->route_name);
        $tab->delete();

        $this->assertUnexpectedRoles($unexpectedRoles);
    }

    public function testAddMultipleTabsWithRouteName(): void
    {
        for ($i = 0; $i < 3; ++$i) {
            $routeNameTab = new Tab();
            $routeNameTab->active = 1;
            $routeNameTab->class_name = 'AdminClassNameTest';
            $routeNameTab->route_name = 'admin_route_name_test';
            $routeNameTab->name = [];
            foreach (Language::getLanguages(true) as $lang) {
                $routeNameTab->name[$lang['id_lang']] = 'Route name tab';
            }
            $routeNameTab->id_parent = (int) Tab::getIdFromClassName('AdminParentThemes');
            $routeNameTab->module = 'module_test_tab';
            $routeNameTab->add();
        }

        $expectedRoles = [
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_CREATE',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_READ',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_UPDATE',
            'ROLE_MOD_TAB_ADMINCLASSNAMETEST_DELETE',
        ];
        $this->assertExpectedRoles($expectedRoles);
    }

    /**
     * @param array $expectedRoles
     *
     * @throws \PrestaShopDatabaseException
     */
    private function assertExpectedRoles(array $expectedRoles): void
    {
        foreach ($expectedRoles as $expectedRole) {
            $roles = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                'SELECT id_authorization_role, slug FROM `' . _DB_PREFIX_ . 'authorization_role` WHERE `slug` = "' . $expectedRole . '"',
                true,
                false
            );
            $this->assertNotEmpty($roles);
            $this->assertCount(1, $roles);
            $role = $roles[0];
            $this->assertEquals($expectedRole, $role['slug']);

            $accesses = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                'SELECT id_profile, id_authorization_role FROM `' . _DB_PREFIX_ . 'access` WHERE `id_authorization_role` = ' . $role['id_authorization_role'],
                true,
                false
            );
            $this->assertNotEmpty($accesses);
            $this->assertCount(1, $accesses);
            $access = $accesses[0];
            $this->assertEquals(1, $access['id_profile']);
        }
    }

    /**
     * @param array $unexpectedRoles
     *
     * @throws \PrestaShopDatabaseException
     */
    private function assertUnexpectedRoles(array $unexpectedRoles): void
    {
        foreach ($unexpectedRoles as $unexpectedRole) {
            $roles = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                'SELECT id_authorization_role, slug FROM `' . _DB_PREFIX_ . 'authorization_role` WHERE `slug` = "' . $unexpectedRole . '"',
                true,
                false
            );
            $this->assertEmpty($roles);
        }
    }
}
