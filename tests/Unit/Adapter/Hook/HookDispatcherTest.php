<?php

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
            ->with([$lowerCasedEventName], $eventName, $this->hookEventMock)
            ->method('doDispatch')
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
