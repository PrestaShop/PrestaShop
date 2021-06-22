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

namespace Tests\Integration\Classes\db;

use Db;
use PHPUnit\Framework\TestCase;

class DbTest extends TestCase
{
    /**
     * @var Db
     */
    private $firstSlave;

    /**
     * @var Db
     */
    private $secondSlave;

    /**
     * @var Db
     */
    private $master;

    protected function tearDown(): void
    {
        Db::$_slave_servers_loaded = false;
        Db::$_servers = null;
        Db::$instance = [];
    }

    public function testGetInstanceShouldLoadSlavesCorrectlyEvenWhenMasterIsCalled(): void
    {
        $this->loadSlaves(2);

        $this->master = Db::getInstance();

        //When
        $this->firstSlave = Db::getInstance((bool) _PS_USE_SQL_SLAVE_);
        $this->secondSlave = Db::getInstance((bool) _PS_USE_SQL_SLAVE_);

        //Then
        $this->assertNotSame($this->firstSlave, $this->secondSlave);
        $this->assertNotSame($this->master, $this->secondSlave);
        $this->assertNotSame($this->master, $this->firstSlave);

        $this->assertTwoCallsOnFirst_ThenOneOnSecondSlave();

        $this->assertSame($this->master, Db::getInstance());

        $this->assertTwoCallsOnFirst_ThenOneOnSecondSlave();
        $this->assertTwoCallsOnFirst_ThenOneOnSecondSlave();
    }

    private function assertTwoCallsOnFirst_ThenOneOnSecondSlave(): void
    {
        // Third and fourth calls are on first slave
        $this->assertSame($this->firstSlave, Db::getInstance((bool) _PS_USE_SQL_SLAVE_));
        $this->assertSame($this->firstSlave, Db::getInstance((bool) _PS_USE_SQL_SLAVE_));

        // Fifth call is on second slave
        $this->assertSame($this->secondSlave, Db::getInstance(_PS_USE_SQL_SLAVE_));
    }

    private function loadSlaves(int $nbServers = 0): void
    {
        Db::$_slave_servers_loaded = true;
        Db::$_servers = [];
        for ($i = 0; $i <= $nbServers; ++$i) {
            Db::$_servers[] = ['server' => _DB_SERVER_, 'user' => _DB_USER_, 'password' => _DB_PASSWD_, 'database' => _DB_NAME_];
        }
    }
}
