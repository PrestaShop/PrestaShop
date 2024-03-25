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

namespace Tests\Unit\PrestaShopBundle\EventListener\Admin;

use Context;
use Employee;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Util\Url\BackUrlProvider;
use PrestaShopBundle\EventListener\Admin\BackUrlRedirectResponseListener;
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

        /** @var RedirectResponse $actual */
        $actual = $filterResponseEventMock->getResponse();
        $this->assertEquals($expectedUrl, $actual->getTargetUrl());
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

        /** @var RedirectResponse $actual */
        $actual = $filterResponseEventMock->getResponse();
        $this->assertEquals($expectedUrl, $actual->getTargetUrl());
    }

    /**
     * @dataProvider getBackUrlsToTest
     */
    public function testBackUrlUpdates(Request $currentRequest, string $redirectTarget, string $backUrl, string $expectedTarget)
    {
        $legacyContextMock = $this->getLegacyContextMock();
        $backUrlProviderMock = $this->getBackUrlProviderMock($backUrl);

        $originalRedirectResponse = new RedirectResponse($redirectTarget);
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

        /** @var RedirectResponse $actual */
        $actual = $filterResponseEventMock->getResponse();
        $this->assertEquals($expectedTarget, $actual->getTargetUrl());
    }

    public function getBackUrlsToTest(): iterable
    {
        yield 'redirect to current url without back url, nothing changes' => [
            Request::create('http://localhost.org'),
            'http://localhost.org',
            '',
            'http://localhost.org',
        ];

        yield 'redirect to current url with back url, nothing changes' => [
            Request::create('http://localhost.org'),
            'http://localhost.org',
            'http://localhost.org/other',
            'http://localhost.org',
        ];

        yield 'redirect to different url without back url, nothing changes' => [
            Request::create('http://localhost.org'),
            'http://localhost.org/other',
            '',
            'http://localhost.org/other',
        ];

        yield 'redirect to different url with back url, use back url' => [
            Request::create('http://localhost.org'),
            'http://localhost.org/other',
            'http://localhost.org/back',
            'http://localhost.org/back',
        ];

        yield 'redirect to same legacy page, back url ignored' => [
            Request::create('http://localhost.org/admin/index.php?controller=legacy'),
            'http://localhost.org/admin/index.php?controller=legacy',
            'http://localhost.org/back',
            'http://localhost.org/admin/index.php?controller=legacy',
        ];

        yield 'redirect to same legacy page with additional parameters, back url ignored' => [
            Request::create('http://localhost.org/admin/index.php?controller=legacy'),
            'http://localhost.org/admin/index.php?controller=legacy&conf=4',
            'http://localhost.org/back',
            'http://localhost.org/admin/index.php?controller=legacy&conf=4',
        ];

        yield 'redirect to same legacy page with different parameters, back url is used' => [
            Request::create('http://localhost.org/admin/index.php?controller=legacy'),
            'http://localhost.org/admin/index.php?controller=othercontroller&conf=4',
            'http://localhost.org/back',
            'http://localhost.org/back',
        ];

        yield 'redirect to same legacy page but without scheme, back url is ignored' => [
            Request::create('http://localhost.org/admin/index.php?controller=legacy'),
            '//localhost.org/admin/index.php?controller=legacy&conf=4',
            'http://localhost.org/back',
            '//localhost.org/admin/index.php?controller=legacy&conf=4',
        ];

        yield 'redirect to same legacy page but without domain, back url is ignored' => [
            Request::create('http://localhost.org/admin/index.php?controller=legacy'),
            '/admin/index.php?controller=legacy&conf=4',
            'http://localhost.org/back',
            '/admin/index.php?controller=legacy&conf=4',
        ];

        yield 'use case save and redirect when editing customer cart rule, conf parameter is kept' => [
            Request::create('http://prestashop.symfony-layout.local/admin-dev/index.php?controller=AdminCartRules&id_cart_rule=4&addcart_rule=1&back=http%3A%2F%2Fprestashop.symfony-layout.local%2Fadmin-dev%2Findex.php%2Fsell%2Fcustomers%2F2%2Fview%3F_token%3D42c2518bdfbd4.7JCMbxlSkY6Mij6WCXx059N2Gmfq6BZ6VfNsvZSVt-I.vsPaBEA4ovjj5mHmSEkioIJBfwqBi04lOKIKiN_ewtSI8b8eLyHiy9nvCg%26conf%3D4&token=9b4200264b205137.j4VBTIfoha573aV9Lf9QO_yeg8Q1EXE3GoUnTkmX1Sg.3dYXJ96CttgUsfoNbMoGfK2p5qlecilod9RBewLcoB7r5HI9sZv26y64kQ'),
            'http://prestashop.symfony-layout.local/admin-dev/index.php/sell/customers/2/view?_token=42c2518bdfbd4.7JCMbxlSkY6Mij6WCXx059N2Gmfq6BZ6VfNsvZSVt-I.vsPaBEA4ovjj5mHmSEkioIJBfwqBi04lOKIKiN_ewtSI8b8eLyHiy9nvCg&conf=4',
            'http://prestashop.symfony-layout.local/admin-dev/index.php/sell/customers/2/view?_token=42c2518bdfbd4.7JCMbxlSkY6Mij6WCXx059N2Gmfq6BZ6VfNsvZSVt-I.vsPaBEA4ovjj5mHmSEkioIJBfwqBi04lOKIKiN_ewtSI8b8eLyHiy9nvCg',
            'http://prestashop.symfony-layout.local/admin-dev/index.php/sell/customers/2/view?_token=42c2518bdfbd4.7JCMbxlSkY6Mij6WCXx059N2Gmfq6BZ6VfNsvZSVt-I.vsPaBEA4ovjj5mHmSEkioIJBfwqBi04lOKIKiN_ewtSI8b8eLyHiy9nvCg&conf=4',
        ];

        yield 'use case save and stay when editing customer cart rule' => [
            Request::create('http://prestashop.symfony-layout.local/admin-dev/index.php?controller=AdminCartRules&id_cart_rule=4&addcart_rule=1&back=http%3A%2F%2Fprestashop.symfony-layout.local%2Fadmin-dev%2Findex.php%2Fsell%2Fcustomers%2F2%2Fview%3F_token%3D42c2518bdfbd4.7JCMbxlSkY6Mij6WCXx059N2Gmfq6BZ6VfNsvZSVt-I.vsPaBEA4ovjj5mHmSEkioIJBfwqBi04lOKIKiN_ewtSI8b8eLyHiy9nvCg%26conf%3D4&token=9b4200264b205137.j4VBTIfoha573aV9Lf9QO_yeg8Q1EXE3GoUnTkmX1Sg.3dYXJ96CttgUsfoNbMoGfK2p5qlecilod9RBewLcoB7r5HI9sZv26y64kQ'),
            'http://prestashop.symfony-layout.local/admin-dev/index.php?controller=AdminCartRules&back=http%3A%2F%2Fprestashop.symfony-layout.local%2Fadmin-dev%2Findex.php%2Fsell%2Fcustomers%2F2%2Fview%3F_token%3D42c2518bdfbd4.7JCMbxlSkY6Mij6WCXx059N2Gmfq6BZ6VfNsvZSVt-I.vsPaBEA4ovjj5mHmSEkioIJBfwqBi04lOKIKiN_ewtSI8b8eLyHiy9nvCg%26conf%3D4&id_cart_rule=4&conf=4&updatecart_rule&token=f616adec1ce078c48557.kx5-fLqLIS7O45Bx1q1zwE-2kptqvse9m99whk2Z1Tw.wU0oF-PhElihj88Bl5glhx6B9_YB3Z_i9o4WswbSoAr3f00NjPhSa5uGpA',
            'http://prestashop.symfony-layout.local/admin-dev/index.php/sell/customers/2/view?_token=42c2518bdfbd4.7JCMbxlSkY6Mij6WCXx059N2Gmfq6BZ6VfNsvZSVt-I.vsPaBEA4ovjj5mHmSEkioIJBfwqBi04lOKIKiN_ewtSI8b8eLyHiy9nvCg&conf=4',
            'http://prestashop.symfony-layout.local/admin-dev/index.php?controller=AdminCartRules&back=http%3A%2F%2Fprestashop.symfony-layout.local%2Fadmin-dev%2Findex.php%2Fsell%2Fcustomers%2F2%2Fview%3F_token%3D42c2518bdfbd4.7JCMbxlSkY6Mij6WCXx059N2Gmfq6BZ6VfNsvZSVt-I.vsPaBEA4ovjj5mHmSEkioIJBfwqBi04lOKIKiN_ewtSI8b8eLyHiy9nvCg%26conf%3D4&id_cart_rule=4&conf=4&updatecart_rule&token=f616adec1ce078c48557.kx5-fLqLIS7O45Bx1q1zwE-2kptqvse9m99whk2Z1Tw.wU0oF-PhElihj88Bl5glhx6B9_YB3Z_i9o4WswbSoAr3f00NjPhSa5uGpA',
        ];

        yield 'use case delete after the discount was just updated' => [
            Request::create('http://prestashop.symfony-layout.local/admin-dev/index.php?controller=AdminCartRules&id_cart_rule=12&deletecart_rule=1&back=http%3A%2F%2Fprestashop.symfony-layout.local%2Fadmin-dev%2Findex.php%2Fsell%2Fcustomers%2F2%2Fview%3F_token%3D3b045718e482e3d98b7ff76abb601.nKMJ0zLpPVZUO1Zy2i4uOoh0M9MvcwPkAlfDycgPCvY.9ddBglWhRDI4CGEorXRDf-0Qcf4fIlC1cRi1vItBR6HD_F-lc79PAhVUHw%26conf%3D4&token=bd766cdf70b3d0106db.Eqrd-1UrP7gqLuf0bIhUoXdJKHdkiIBCHi6nmKRacic.e96VqjJjRtxGHdCuG9I55BItalpU2dMTbWHR7ecUP3BN9YuNFH1N7GtBrg'),
            'http://prestashop.symfony-layout.local/admin-dev/index.php/sell/customers/2/view?_token=3b045718e482e3d98b7ff76abb601.nKMJ0zLpPVZUO1Zy2i4uOoh0M9MvcwPkAlfDycgPCvY.9ddBglWhRDI4CGEorXRDf-0Qcf4fIlC1cRi1vItBR6HD_F-lc79PAhVUHw&conf=4&conf=1',
            'http://prestashop.symfony-layout.local/admin-dev/index.php/sell/customers/2/view?_token=3b045718e482e3d98b7ff76abb601.nKMJ0zLpPVZUO1Zy2i4uOoh0M9MvcwPkAlfDycgPCvY.9ddBglWhRDI4CGEorXRDf-0Qcf4fIlC1cRi1vItBR6HD_F-lc79PAhVUHw&conf=4',
            'http://prestashop.symfony-layout.local/admin-dev/index.php/sell/customers/2/view?_token=3b045718e482e3d98b7ff76abb601.nKMJ0zLpPVZUO1Zy2i4uOoh0M9MvcwPkAlfDycgPCvY.9ddBglWhRDI4CGEorXRDf-0Qcf4fIlC1cRi1vItBR6HD_F-lc79PAhVUHw&conf=4&conf=1',
        ];
    }

    public function testWhenEmployeeIsNotConnected()
    {
        $expectedUrl = 'http://localhost.dev';
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
            new RedirectResponse($expectedUrl)
        );

        $responseListener->onKernelResponse($filterResponseEventMock);

        /** @var RedirectResponse $actual */
        $actual = $filterResponseEventMock->getResponse();
        $this->assertEquals($expectedUrl, $actual->getTargetUrl());
    }
}
