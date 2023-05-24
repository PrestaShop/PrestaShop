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

namespace Tests\Unit\PrestaShopBundle\Twig\Extension;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\Url\BackUrlProvider;
use PrestaShopBundle\Twig\Extension\PathWithBackUrlExtension;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PathWithBackUrlExtensionTest extends TestCase
{
    public const FALLBACK_URL = 'https://www.prestashop.com/en';

    /**
     * @var MockObject|UrlGeneratorInterface
     */
    private $urlGeneratorInterfaceMock;

    /**
     * @var MockObject|RequestStack
     */
    private $requestStackMock;

    /**
     * @var MockObject|BackUrlProvider
     */
    private $backUrlProviderMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->urlGeneratorInterfaceMock = $this
            ->getMockBuilder(UrlGeneratorInterface::class)
            ->getMock()
        ;

        $this->urlGeneratorInterfaceMock
            ->method('generate')
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

    public function testItFallBacksToDefaultUrlWhenBackUrlIsNotFound()
    {
        $requestMock = $this
            ->getMockBuilder(Request::class)
            ->getMock()
        ;

        $requestMock->query = new InputBag();

        $this->requestStackMock
            ->method('getCurrentRequest')
            ->willReturn($requestMock)
        ;

        $this->backUrlProviderMock
            ->method('getBackUrl')
            ->willReturn('')
        ;

        $extension = new PathWithBackUrlExtension(
            $this->urlGeneratorInterfaceMock,
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

        $requestMock->query = new InputBag();

        $this->requestStackMock
            ->method('getCurrentRequest')
            ->willReturn($requestMock)
        ;

        $this->backUrlProviderMock
            ->method('getBackUrl')
            ->willReturn($expectedUrl);

        $extension = new PathWithBackUrlExtension(
            $this->urlGeneratorInterfaceMock,
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
