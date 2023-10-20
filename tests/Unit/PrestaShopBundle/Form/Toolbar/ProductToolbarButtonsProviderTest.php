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

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Toolbar;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Shop\Url\HelpProvider;
use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Toolbar\ProductToolbarButtonsProvider;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductToolbarButtonsProviderTest extends TestCase
{
    private const DEFAULT_BUTTONS = [
        'product_list' => [
            'type' => IconButtonType::class,
            'options' => [
                'type' => 'button',
                'icon' => 'list',
                'label' => 'Product list',
                'attr' => [
                    'title' => 'Product list',
                    'class' => 'toolbar-button btn-quicknav btn-sidebar',
                    'data-toggle' => 'sidebar',
                    'data-target' => '#right-sidebar',
                    'data-url' => 'http://local.light_product_list',
                ],
            ],
        ],
        'help' => [
            'type' => IconButtonType::class,
            'options' => [
                'type' => 'button',
                'icon' => 'help',
                'label' => 'Help',
                'attr' => [
                    'title' => 'Help',
                    'class' => 'toolbar-button btn-quicknav btn-sidebar',
                    'data-toggle' => 'sidebar',
                    'data-target' => '#right-sidebar',
                    'data-url' => 'http://help.product.com',
                ],
            ],
        ],
    ];

    private const STATS_OPTIONS = [
        'stats_link' => [
            'type' => IconButtonType::class,
            'options' => [
                'type' => 'link',
                'icon' => 'assessment',
                'label' => 'Sales',
                'attr' => [
                    'title' => 'Sales',
                    'href' => 'http://local.stats',
                    'class' => 'toolbar-button btn-sales',
                    'target' => '_blank',
                ],
            ],
        ],
    ];

    private const PRODUCT_ID = 42;

    public function testToolbarButtonsWithoutStats(): void
    {
        $provider = $this->buildProvider(false);
        $toolbarButtons = $provider->getToolbarButtonsOptions([]);
        $this->assertEquals(self::DEFAULT_BUTTONS, $toolbarButtons);
    }

    public function testToolbarButtonsWithStats(): void
    {
        $provider = $this->buildProvider(true);
        $toolbarButtons = $provider->getToolbarButtonsOptions(['productId' => self::PRODUCT_ID]);
        $this->assertEquals(array_merge(self::STATS_OPTIONS, self::DEFAULT_BUTTONS), $toolbarButtons);
    }

    private function buildProvider(bool $withStats): ProductToolbarButtonsProvider
    {
        $translatorMock = $this->getMockBuilder(TranslatorInterface::class)->getMock();
        $translatorMock
            ->method('trans')
            ->willReturnArgument(0)
        ;
        $routerMock = $this->getMockBuilder(RouterInterface::class)->getMock();
        $routerMock
            ->expects($this->once())
            ->method('generate')
            ->with('admin_products_light_list')
            ->willReturn('http://local.light_product_list')
        ;
        $helpProviderMock = $this->getMockBuilder(HelpProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $helpProviderMock
            ->expects($this->once())
            ->method('getUrl')
            ->with('AdminProducts')
            ->willReturn('http://help.product.com')
        ;
        $moduleDataProviderMock = $this->getMockBuilder(ModuleDataProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $legacyContextMock = $this->getMockBuilder(LegacyContext::class)
            ->disableOriginalConstructor()
            ->getMock();

        if ($withStats) {
            $moduleDataProviderMock
                ->expects($this->once())
                ->method('findByName')
                ->with('statsproduct')
                ->willReturn(['active' => true])
            ;
            $legacyContextMock
                ->expects($this->once())
                ->method('getAdminLink')
                ->with('AdminStats', true, ['module' => 'statsproduct', 'id_product' => self::PRODUCT_ID])
                ->willReturn('http://local.stats')
            ;
        } else {
            $moduleDataProviderMock
                ->expects($this->never())
                ->method('findByName')
            ;
            $legacyContextMock
                ->expects($this->never())
                ->method('getAdminLink')
            ;
        }

        return new ProductToolbarButtonsProvider(
            $translatorMock,
            $routerMock,
            $helpProviderMock,
            $moduleDataProviderMock,
            $legacyContextMock
        );
    }
}
