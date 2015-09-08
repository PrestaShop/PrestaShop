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
namespace PrestaShop\PrestaShop\tests\Unit\Core\Foundation\Log;

use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use PrestaShop\PrestaShop\Core\Foundation\Log\MessageStackManager;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;

class MessageStackTest extends UnitTestCase
{
    private function cleanStacks($stackManager)
    {
        while ($stackManager->getErrorIterator()->count()) {
            $stackManager->getErrorIterator()->dequeue();
        }
        while ($stackManager->getWarningIterator()->count()) {
            $stackManager->getWarningIterator()->dequeue();
        }
        while ($stackManager->getInfoIterator()->count()) {
            $stackManager->getInfoIterator()->dequeue();
        }
        while ($stackManager->getSuccessIterator()->count()) {
            $stackManager->getSuccessIterator()->dequeue();
        }
    }

    public function test_message_stack()
    {
        $stackManager = MessageStackManager::getInstance();
        $this->assertAttributeInstanceOf('\\SplQueue', 'errorQueue', $stackManager);
        $this->assertAttributeInstanceOf('\\SplQueue', 'warningQueue', $stackManager);
        $this->assertAttributeInstanceOf('\\SplQueue', 'infoQueue', $stackManager);
        $this->assertAttributeInstanceOf('\\SplQueue', 'successQueue', $stackManager);
        $this->cleanStacks($stackManager); // others Unit tests ran before could have enqueue elements.

        $be1 = new BaseEvent('test event', new \Core_Foundation_Exception_Exception('test exception'));
        $stackManager->onError($be1);
        $this->assertEquals(1, $stackManager->getErrorIterator()->count(), "Error stack should contains 1 exception.");
        $this->assertEquals(0, $stackManager->getWarningIterator()->count(), "Warning stack should contains 0 exception.");
        $this->assertEquals(0, $stackManager->getInfoIterator()->count(), "Info stack should contains 0 message.");
        $this->assertEquals(0, $stackManager->getSuccessIterator()->count(), "Success stack should contains 0 message.");
        
        $be2 = $stackManager->getErrorIterator()->dequeue();
        $this->assertEquals($be1->getException(), $be2);
        $this->assertEquals(0, $stackManager->getErrorIterator()->count(), "Error stack should contains 0 event after dequeue().");
        
        $stackManager->onWarning($be1);
        $stackManager->onInfo($be1);
        $stackManager->onSuccess($be1);
        $this->assertEquals(0, $stackManager->getErrorIterator()->count(), "Error stack should contains 0 exception.");
        $this->assertEquals(1, $stackManager->getWarningIterator()->count(), "Warning stack should contains 1 exception.");
        $this->assertEquals(1, $stackManager->getInfoIterator()->count(), "Info stack should contains 1 message.");
        $this->assertEquals(1, $stackManager->getSuccessIterator()->count(), "Success stack should contains 1 message.");
        
        $be3 = $stackManager->getWarningIterator()->dequeue();
        $this->assertEquals($be1->getException(), $be3);
        $be4 = $stackManager->getInfoIterator()->dequeue();
        $this->assertEquals($be1->getMessage(), $be4);
        $be5 = $stackManager->getSuccessIterator()->dequeue();
        $this->assertEquals($be1->getMessage(), $be5);
    }
}
