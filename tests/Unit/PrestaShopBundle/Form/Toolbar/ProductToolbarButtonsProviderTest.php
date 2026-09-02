<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
