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

namespace Tests\Unit\Adapter\Hook;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Hook\HookDispatcher;
use PrestaShopBundle\Service\Hook\HookEvent;

class HookDispatcherTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|HookEvent
     */
    private $hookEventMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|HookDispatcher
     */
    private $hookDispatcherMock;

    protected function setUp()
    {
        parent::setUp();

        $this->hookEventMock = $this
            ->getMockBuilder(HookEvent::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->hookDispatcherMock = $this
            ->getMockBuilder(HookDispatcher::class)
            ->setMethods(['getListeners', 'doDispatch'])
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    /**
     * @dataProvider getHookEventNames
     *
     * @param string $eventName
     */
    public function testItCallsHookDispatcherWithCaseInsensitiveEventNames($eventName)
    {
        $lowerCasedEventName = strtolower($eventName);

        $this->hookDispatcherMock
            ->method('getListeners')
            ->with($lowerCasedEventName)
            ->willReturn([$lowerCasedEventName])
        ;

        $this->hookDispatcherMock
            ->expects($this->once())
            ->method('doDispatch')
            ->with([$lowerCasedEventName], $eventName, $this->hookEventMock)
        ;

        $this->hookDispatcherMock->dispatch($eventName, $this->hookEventMock);
    }

    public function getHookEventNames()
    {
        yield [
            'normalHook',
        ];

        yield [
            'HOOKINUPPERCASE',
        ];

        yield [
            'hookINanyCASE',
        ];

        yield [
            'lowercasehookalso',
        ];
    }
}
