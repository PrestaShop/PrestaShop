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
