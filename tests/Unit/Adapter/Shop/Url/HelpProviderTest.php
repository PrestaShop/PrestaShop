<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Shop\Url;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Shop\Url\HelpProvider;
use PrestaShop\PrestaShop\Core\Foundation\Version;
use PrestaShop\PrestaShop\Core\Help\Documentation;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class HelpProviderTest extends TestCase
{
    private const HELP_HOST = 'https://help.prestashop.com/';

    public function testGetUrl(): void
    {
        $legacyContextMock = $this->getMockBuilder(LegacyContext::class)
            ->disableOriginalConstructor()
            ->getMock();
        $legacyContextMock->method('getEmployeeLanguageIso')
            ->willReturn('en');
        $translatorMock = $this->getMockBuilder(TranslatorInterface::class)->getMock();
        $translatorMock
            ->method('trans')
            ->willReturnArgument(0)
        ;
        $routerMock = $this->getMockBuilder(RouterInterface::class)->getMock();
        $routerMock
            ->method('generate')
            ->with(
                'admin_common_sidebar',
                $this->callback(function ($urlParameters) {
                    $this->assertEquals('https://help.prestashop.com/en/doc/products?version=8.0.0&country=en', urldecode($urlParameters['url']));
                    $this->assertEquals('Help', $urlParameters['title']);

                    return true;
                }
                ));

        $provider = new HelpProvider(
            $legacyContextMock,
            $translatorMock,
            $routerMock,
            $this->buildDocumentation()
        );

        $provider->getUrl('products');
    }

    private function buildDocumentation(): Documentation
    {
        $version = new Version('8.0.0', '8', 8);

        return new Documentation($version, self::HELP_HOST);
    }
}
