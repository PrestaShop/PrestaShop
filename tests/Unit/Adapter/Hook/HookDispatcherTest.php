<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Adapter\Hook;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Hook\HookDispatcher;
use PrestaShopBundle\Service\Hook\HookEvent;

class HookDispatcherTest extends TestCase
{
    /**
     * @var MockObject|HookEvent
     */
    private $hookEventMock;

    /**
     * @var MockObject|HookDispatcher
     */
    private $hookDispatcherMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hookEventMock = $this
            ->getMockBuilder(HookEvent::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->hookDispatcherMock = $this
            ->getMockBuilder(HookDispatcher::class)
            ->onlyMethods(['getListeners', 'doDispatch'])
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    /**
     * @dataProvider getHookEventNames
     *
     * @param string $eventName
     */
    public function testItCallsHookDispatcherWithCaseInsensitiveEventNames(string $eventName): void
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

        $this->hookDispatcherMock->dispatch($this->hookEventMock, $eventName);
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
