<?php
/**
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use Core_Foundation_Event_Event as Event;
use Core_Foundation_Event_EventManager as EventManager;

class fakeEvent1
{
    public function getName()
    {
        return 'hiver';
    }
}
class fakeEvent2
{
    public function getName()
    {
        return 'primtemps';
    }
}

class Core_Foundation_Event_EventManagerTest extends UnitTestCase
{
    private $events;

    public function __construct()
    {
        $this->events = new SplPriorityQueue;
    }

    private function addEvents()
    {
        $this->events->insert(array('MyEvent', array(new fakeEvent1(), 'getName')), 1000);
        $this->events->insert(array('MyEvent2', array(new fakeEvent2(), 'getName')), 1);
    }

    public function testAttachEvent()
    {
        $this->addEvents();
        $event = $this->events->extract();

        $this->assertEquals($event[0], 'MyEvent');
        $this->assertTrue(is_object($event[1][0]), 'Not an object');
        $this->assertEquals($event[1][1], 'getName');
    }

    public function testTrigger()
    {
        $this->addEvents();
        $newQueue = [];
        $outputExec = '';

        foreach ($this->events as $event) {
            if ($event[0] === 'MyEvent') {
                $e = new Core_Foundation_Event_Event('MyEvent');
                $outputExec .= $event[1]($e);
            } else {
                $newQueue[] = $event;
            }
        }

        $this->assertEquals($outputExec, 'hiver');
        $this->assertCount(0, $this->events);

        $this->events = $newQueue;

        $this->assertCount(1, $this->events);
    }
}
