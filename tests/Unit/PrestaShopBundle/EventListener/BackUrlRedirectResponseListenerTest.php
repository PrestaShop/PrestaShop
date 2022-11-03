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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Unit\PrestaShopBundle\EventListener;

use Context;
use Employee;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Util\Url\BackUrlProvider;
use PrestaShopBundle\EventListener\BackUrlRedirectResponseListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BackUrlRedirectResponseListenerTest extends TestCase
{
    /**
     * @var MockObject|FilterResponseEvent
     */
    private $filterResponseEventMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filterResponseEventMock = $this
            ->getMockBuilder(FilterResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    protected function getLegacyContextMock($isConnected = true)
    {
        $legacyContextMock = $this->getMockBuilder(LegacyContext::class)
            ->setMethods([
                'getContext',
            ])
            ->getMock();

        $employeeMock = $this->getMockBuilder(Employee::class)->getMock();
        $employeeMock->id = $isConnected ? 1 : null;

        $contextMock = $this->getMockBuilder(Context::class)->getMock();
        $contextMock->employee = $employeeMock;

        $legacyContextMock->expects($this->any())->method('getContext')->willReturn($contextMock);

        return $legacyContextMock;
    }

    protected function getBackUrlProviderMock($backUrl)
    {
        $backUrlProviderMock = $this
            ->getMockBuilder(BackUrlProvider::class)
            ->getMock()
        ;

        $backUrlProviderMock
            ->method('getBackUrl')
            ->willReturn($backUrl)
        ;

        return $backUrlProviderMock;
    }

    public function testItSetsResponseWithBackUrl()
    {
        $expectedUrl = 'http://localhost';

        $legacyContextMock = $this->getLegacyContextMock();
        $backUrlProviderMock = $this->getBackUrlProviderMock(
            $expectedUrl
        );

        $this->filterResponseEventMock
            ->method('getResponse')
            ->willReturn(new RedirectResponse('http://localhost.dev'))
        ;

        $this->filterResponseEventMock
            ->method('getRequest')
            ->willReturn(new Request())
        ;

        $responseListener = new BackUrlRedirectResponseListener(
            $backUrlProviderMock,
            $legacyContextMock
        );

        $responseListener->onKernelResponse($this->filterResponseEventMock);

        $actual = $this->filterResponseEventMock->getResponse();
        $expected = new RedirectResponse($expectedUrl);

        $this->assertEquals($expected, $actual);
    }

    public function testWhenRequestAndResponseUrlsAreEqualItDoesNotModifyOriginalResponse()
    {
        $expectedUrl = 'http://localhost';

        $legacyContextMock = $this->getLegacyContextMock();
        $backUrlProviderMock = $this->getBackUrlProviderMock(
            'http://localhost-not-called.dev'
        );

        $originalRedirectResponse = new RedirectResponse($expectedUrl);

        $this->filterResponseEventMock
            ->method('getResponse')
            ->willReturn($originalRedirectResponse)
        ;

        $currentRequest = $this
            ->getMockBuilder(Request::class)
            ->getMock();

        $currentRequest
            ->method('getRequestUri')
            ->willReturn($expectedUrl)
        ;

        $this->filterResponseEventMock
            ->method('getRequest')
            ->willReturn($currentRequest)
        ;

        $responseListener = new BackUrlRedirectResponseListener(
            $backUrlProviderMock,
            $legacyContextMock
        );

        $responseListener->onKernelResponse($this->filterResponseEventMock);

        $actual = $this->filterResponseEventMock->getResponse();

        $this->assertEquals($originalRedirectResponse, $actual);
    }

    public function testWhenEmployeeIsNotConnected()
    {
        $expectedUrl = 'http://localhost';

        $legacyContextMock = $this->getLegacyContextMock(false);
        $backUrlProviderMock = $this->getBackUrlProviderMock(
            'http://localhost-not-called.dev'
        );

        $responseListener = new BackUrlRedirectResponseListener(
            $backUrlProviderMock,
            $legacyContextMock
        );

        $this->assertNull($responseListener->onKernelResponse($this->filterResponseEventMock));
    }
}
