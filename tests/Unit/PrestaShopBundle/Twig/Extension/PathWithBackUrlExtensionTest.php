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

namespace Tests\Unit\PrestaShopBundle\Twig\Extension;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\Url\BackUrlProvider;
use PrestaShopBundle\Twig\Extension\PathWithBackUrlExtension;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class PathWithBackUrlExtensionTest extends TestCase
{
    const FALLBACK_URL = 'https://www.prestashop.com/en';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RoutingExtension
     */
    private $routingExtensionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    private $requestStackMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|BackUrlProvider
     */
    private $backUrlProviderMock;

    protected function setUp()
    {
        parent::setUp();

        $this->routingExtensionMock = $this
            ->getMockBuilder(RoutingExtension::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this
            ->routingExtensionMock
            ->method('getPath')
            ->willReturn(self::FALLBACK_URL)
        ;

        $this->requestStackMock = $this
            ->getMockBuilder(RequestStack::class)
            ->getMock()
        ;

        $this->backUrlProviderMock = $this
            ->getMockBuilder(BackUrlProvider::class)
            ->getMock()
        ;
    }

    public function testItFallBacksToDefaultUrlWhenRequestStackIsNull()
    {
        $extension = new PathWithBackUrlExtension(
            $this->routingExtensionMock,
            $this->backUrlProviderMock,
            null
        );

        $url = $extension->getPathWithBackUrl('prestashop');

        $this->assertEquals(self::FALLBACK_URL, $url);
    }

    public function testItFallBacksToDefaultUrlWhenBackUrlIsNotFound()
    {
        $requestMock = $this
            ->getMockBuilder(Request::class)
            ->getMock()
        ;

        $requestMock->query = new ParameterBag();

        $this->requestStackMock
            ->method('getCurrentRequest')
            ->willReturn($requestMock)
        ;

        $this->backUrlProviderMock
            ->method('getBackUrl')
            ->willReturn('')
        ;

        $extension = new PathWithBackUrlExtension(
            $this->routingExtensionMock,
            $this->backUrlProviderMock,
            $this->requestStackMock
        );

        $url = $extension->getPathWithBackUrl('prestashop');

        $this->assertEquals(self::FALLBACK_URL, $url);
    }

    public function testItReturnsBackUrl()
    {
        $expectedUrl = 'http://localhost';

        $requestMock = $this
            ->getMockBuilder(Request::class)
            ->getMock()
        ;

        $requestMock->query = new ParameterBag();

        $this->requestStackMock
            ->method('getCurrentRequest')
            ->willReturn($requestMock)
        ;

        $this->backUrlProviderMock
            ->method('getBackUrl')
            ->willReturn($expectedUrl);

        $extension = new PathWithBackUrlExtension(
            $this->routingExtensionMock,
            $this->backUrlProviderMock,
            $this->requestStackMock
        );

        $url = $extension->getPathWithBackUrl('prestashop');

        $this->assertEquals(
            $expectedUrl,
            $url
        );
    }
}
