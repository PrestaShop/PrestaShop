<?php

/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

namespace PrestaShop\PrestaShop\Tests\Integration\Classes\Db;

use Db;
use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;

class DbTest extends IntegrationTestCase
{
    private $first_slave;

    private $second_slave;

    private $master;

    public function tearDown()
    {
        Db::$_slave_servers_loaded = false;
        Db::$_servers = null;
        Db::$instance = array();
    }

    public function test_getInstance_ShouldLoadSlavesCorrectly_EvenWhenMasterIsCalled()
    {
        $this->loadSlaves(2);

        $this->master = Db::getInstance();

        //When
        $this->first_slave = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $this->second_slave = Db::getInstance(_PS_USE_SQL_SLAVE_);

        //Then
        $this->assertNotSame($this->first_slave, $this->second_slave);
        $this->assertNotSame($this->master, $this->second_slave);
        $this->assertNotSame($this->master, $this->first_slave);

        $this->assert_TwoCallsOnFirst_ThenOneOnSecondSlave();

        $this->assertSame($this->master, Db::getInstance());

        $this->assert_TwoCallsOnFirst_ThenOneOnSecondSlave();
        $this->assert_TwoCallsOnFirst_ThenOneOnSecondSlave();
    }

    public function assert_TwoCallsOnFirst_ThenOneOnSecondSlave()
    {
        // Third and fourth calls are on first slave
        $this->assertSame($this->first_slave, Db::getInstance(_PS_USE_SQL_SLAVE_));
        $this->assertSame($this->first_slave, Db::getInstance(_PS_USE_SQL_SLAVE_));

        // Fifth call is on second slave
        $this->assertSame($this->second_slave, Db::getInstance(_PS_USE_SQL_SLAVE_));
    }

    public function loadSlaves($nb_servers = 0)
    {
        Db::$_slave_servers_loaded = true;
        Db::$_servers = array();
        for ($i = 0; $i <= $nb_servers; $i++) {
            Db::$_servers[] = array('server' => _DB_SERVER_, 'user' => _DB_USER_, 'password' => _DB_PASSWD_, 'database' => _DB_NAME_);
        }
    }
}
