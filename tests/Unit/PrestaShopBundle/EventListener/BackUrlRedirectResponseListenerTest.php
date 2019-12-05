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

namespace Tests\Unit\PrestaShopBundle\EventListener;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\Url\BackUrlProvider;
use PrestaShopBundle\EventListener\BackUrlRedirectResponseListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BackUrlRedirectResponseListenerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FilterResponseEvent
     */
    private $filterResponseEventMock;

    protected function setUp()
    {
        parent::setUp();

        $this->filterResponseEventMock = $this
            ->getMockBuilder(FilterResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    public function testItSetsResponseWithBackUrl()
    {
        $expectedUrl = 'http://localhost';

        $backUrlProvider = $this
            ->getMockBuilder(BackUrlProvider::class)
            ->getMock()
        ;

        $backUrlProvider
            ->method('getBackUrl')
            ->willReturn($expectedUrl)
        ;

        $this->filterResponseEventMock
            ->method('getResponse')
            ->willReturn(new RedirectResponse('http://localhost.dev'))
        ;

        $this->filterResponseEventMock
            ->method('getRequest')
            ->willReturn(new Request())
        ;

        $responseListener = new BackUrlRedirectResponseListener($backUrlProvider);

        $responseListener->onKernelResponse($this->filterResponseEventMock);

        $actual = $this->filterResponseEventMock->getResponse();
        $expected = new RedirectResponse($expectedUrl);

        $this->assertEquals($expected, $actual);
    }

    public function testWhenRequestAndResponseUrlsAreEqualItDoesNotModifyOriginalResponse()
    {
        $requestAndResponseUrl = 'http://localhost';

        $backUrlProvider = $this
            ->getMockBuilder(BackUrlProvider::class)
            ->getMock()
        ;

        $backUrlProvider
            ->method('getBackUrl')
            ->willReturn('http://localhost-not-called.dev')
        ;

        $originalRedirectResponse = new RedirectResponse($requestAndResponseUrl);

        $this->filterResponseEventMock
            ->method('getResponse')
            ->willReturn($originalRedirectResponse)
        ;

        $currentRequest = $this
            ->getMockBuilder(Request::class)
            ->getMock();

        $currentRequest
            ->method('getRequestUri')
            ->willReturn($requestAndResponseUrl)
        ;

        $this->filterResponseEventMock
            ->method('getRequest')
            ->willReturn($currentRequest)
        ;

        $responseListener = new BackUrlRedirectResponseListener($backUrlProvider);

        $responseListener->onKernelResponse($this->filterResponseEventMock);

        $actual = $this->filterResponseEventMock->getResponse();

        $this->assertEquals($originalRedirectResponse, $actual);
    }
}
