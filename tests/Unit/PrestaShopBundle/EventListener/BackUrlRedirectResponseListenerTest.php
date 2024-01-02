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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Unit\PrestaShopBundle\EventListener;

use Context;
use Employee;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Util\Url\BackUrlProvider;
use PrestaShopBundle\EventListener\BackUrlRedirectResponseListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class BackUrlRedirectResponseListenerTest extends TestCase
{
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

        $filterResponseEventMock = new ResponseEvent(
            new HttpKernel(
                new EventDispatcher(),
                new ControllerResolver(),
                new RequestStack(),
                new ArgumentResolver()
            ),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            new RedirectResponse('http://localhost.dev')
        );

        $responseListener = new BackUrlRedirectResponseListener(
            $backUrlProviderMock,
            $legacyContextMock
        );

        $responseListener->onKernelResponse($filterResponseEventMock);

        $actual = $filterResponseEventMock->getResponse();
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

        $currentRequest = $this
            ->getMockBuilder(Request::class)
            ->getMock();

        $currentRequest
            ->method('getRequestUri')
            ->willReturn($expectedUrl)
        ;

        $filterResponseEventMock = new ResponseEvent(
            new HttpKernel(
                new EventDispatcher(),
                new ControllerResolver(),
                new RequestStack(),
                new ArgumentResolver()
            ),
            $currentRequest,
            HttpKernelInterface::MAIN_REQUEST,
            $originalRedirectResponse
        );

        $responseListener = new BackUrlRedirectResponseListener(
            $backUrlProviderMock,
            $legacyContextMock
        );

        $responseListener->onKernelResponse($filterResponseEventMock);

        $actual = $filterResponseEventMock->getResponse();

        $this->assertEquals($originalRedirectResponse, $actual);
    }

    public function testWhenEmployeeIsNotConnected()
    {
        $legacyContextMock = $this->getLegacyContextMock(false);
        $backUrlProviderMock = $this->getBackUrlProviderMock(
            'http://localhost-not-called.dev'
        );

        $responseListener = new BackUrlRedirectResponseListener(
            $backUrlProviderMock,
            $legacyContextMock
        );

        $filterResponseEventMock = new ResponseEvent(
            new HttpKernel(
                new EventDispatcher(),
                new ControllerResolver(),
                new RequestStack(),
                new ArgumentResolver()
            ),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            new RedirectResponse('http://localhost.dev')
        );

        $this->assertNull($responseListener->onKernelResponse($filterResponseEventMock));
    }
}
