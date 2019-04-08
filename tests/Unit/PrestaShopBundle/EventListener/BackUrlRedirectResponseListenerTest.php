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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\PrestaShopBundle\EventListener;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\EventListener\BackUrlRedirectResponseListener;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BackUrlRedirectResponseListenerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    private $requestStackMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FilterResponseEvent
     */
    private $filterResponseEventMock;

    protected function setUp()
    {
        parent::setUp();

        $this->requestStackMock = $this
            ->getMockBuilder(RequestStack::class)
            ->getMock()
        ;

        $this->filterResponseEventMock = $this
            ->getMockBuilder(FilterResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    public function testItSetsResponseWithBackUrl()
    {
        $requestMock = $this
            ->getMockBuilder(Request::class)
            ->getMock()
        ;

        $requestMock->query = new ParameterBag([
            'back-url' => 'http%3A%2F%2Flocalhost',
        ]);

        $this->requestStackMock
            ->method('getCurrentRequest')
            ->willReturn($requestMock)
        ;

        $responseListener = new BackUrlRedirectResponseListener($this->requestStackMock);

        $originalRedirectResponse = new RedirectResponse('https://www.prestashop.com/en');

        $this->filterResponseEventMock
            ->method('getResponse')
            ->willReturn($originalRedirectResponse)
        ;

        $responseListener->onKernelResponse($this->filterResponseEventMock);

        $actual = $this->filterResponseEventMock->getResponse();
        $expected = new RedirectResponse('http://localhost');

        $this->assertEquals($expected, $actual);
    }

    public function testWhenRequestAndResponseUrlsAreEqualItDoesNotModifyOriginalResponse()
    {
        $requestAndResponseUrl = 'http://localhost';

        $requestMock = $this
            ->getMockBuilder(Request::class)
            ->getMock()
        ;

        $requestMock
            ->method('getRequestUri')
            ->willReturn($requestAndResponseUrl)
        ;

        $requestMock->query = new ParameterBag([
            'back-url' => 'http%3A%2F%2Flocalhost',
        ]);

        $this->requestStackMock
            ->method('getCurrentRequest')
            ->willReturn($requestMock)
        ;

        $responseListener = new BackUrlRedirectResponseListener($this->requestStackMock);

        $originalRedirectResponse = new RedirectResponse($requestAndResponseUrl);

        $this->filterResponseEventMock
            ->method('getResponse')
            ->willReturn($originalRedirectResponse)
        ;

        $responseListener->onKernelResponse($this->filterResponseEventMock);

        $actual = $this->filterResponseEventMock->getResponse();

        $this->assertEquals($originalRedirectResponse, $actual);
    }
}
