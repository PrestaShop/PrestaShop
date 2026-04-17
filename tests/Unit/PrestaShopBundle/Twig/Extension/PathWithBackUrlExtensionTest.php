<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
