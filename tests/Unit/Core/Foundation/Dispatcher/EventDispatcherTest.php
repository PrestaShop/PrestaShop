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
namespace PrestaShop\PrestaShop\tests\Unit\Core\Foundation\Dispatcher;

use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\EventDispatcher;
use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;

class EventDispatcherTest extends UnitTestCase
{
    private function setup_env()
    {
        $fakeRoot = dirname(dirname(dirname(dirname(__DIR__)))); // to tests folder
        $this->assertEquals('tests', substr($fakeRoot, -5));

        return $this->setConfiguration(array(
            '_PS_ROOT_DIR_' => $fakeRoot,
            '_PS_CACHE_DIR_' => $fakeRoot.'/cache/',
            '_PS_MODULE_DIR_' => $fakeRoot.'/resources/module/',
            '_PS_MODE_DEV_' => true
        ));
    }

    public function test_event_dispatcher_instantiation()
    {
        $conf = $this->setup_env();
        EventDispatcher::initDispatchers(
            $conf->get('_PS_ROOT_DIR_'),
            $conf->get('_PS_CACHE_DIR_'),
            $conf->get('_PS_MODULE_DIR_'),
            true);
        $defaultInstance = EventDispatcher::getInstance();
        $this->assertAttributeNotCount(0, 'instances', $defaultInstance);
        $this->assertAttributeContains($defaultInstance, 'instances', $defaultInstance);

        $defaultInstance->addListener('thaï', array($this, 'defaultListener'));
        $defaultInstance->dispatch('thaï', new BaseEvent('thé'));

        $this->assertAttributeEquals(true, 'listenerCalled', $this);
    }

    private $listenerCalled = false;
    public function defaultListener(BaseEvent $e, $event)
    {
        $this->assertEquals($event, 'thaï', 'Event name mismatch.');
        $this->listenerCalled = true;
    }
}
