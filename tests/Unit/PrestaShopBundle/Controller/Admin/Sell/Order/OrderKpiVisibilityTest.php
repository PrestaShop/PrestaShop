<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Controller\Admin\Sell\Order;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Grid\GridFactory;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Kpi\Row\HookableKpiRowFactory;
use PrestaShop\PrestaShop\Core\Kpi\Row\KpiRowInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\CartFilter;
use PrestaShop\PrestaShop\Core\Search\Filters\OrderFilters;
use PrestaShopBundle\Controller\Admin\Sell\Order\CartController;
use PrestaShopBundle\Controller\Admin\Sell\Order\OrderController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderKpiVisibilityTest extends TestCase
{
    /**
     * @param class-string<OrderController|CartController> $controllerClass
     *
     * @dataProvider provideVisibility
     */
    public function testListDoesNotBuildDisabledKpis(string $controllerClass, mixed $storedValue, bool $enabled): void
    {
        $controller = $this->getMockBuilder($controllerClass)->disableOriginalConstructor()->onlyMethods([
            'getConfiguration', 'getShopContext', 'presentGrid', 'generateSidebarLink',
            'createForm', 'generateUrl', 'trans', 'render',
        ])->getMock();
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->expects(self::once())->method('get')->with('PS_DISPLAY_BACKOFFICE_KPIS')->willReturn($storedValue);
        $controller->method('getConfiguration')->willReturn($configuration);
        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('getShopConstraint')->willReturn(ShopConstraint::shop(1));
        $controller->method('getShopContext')->willReturn($shopContext);
        $controller->method('presentGrid')->willReturn([]);
        $controller->method('generateSidebarLink')->willReturn('');
        $controller->method('generateUrl')->willReturn('/');
        $controller->method('trans')->willReturn('');
        $form = $this->createMock(FormInterface::class);
        $form->method('createView')->willReturn(new FormView());
        $controller->method('createForm')->willReturn($form);
        $isOrder = $controllerClass === OrderController::class;
        $controller->expects(self::once())->method('render')->willReturnCallback(
            function (string $view, array $parameters) use ($enabled, $isOrder): Response {
                $row = $parameters[$isOrder ? 'orderKpi' : 'cartsKpi'];
                if ($enabled) {
                    self::assertInstanceOf(KpiRowInterface::class, $row);
                } else {
                    self::assertNull($row);
                }

                return new Response();
            }
        );
        $hooks = $this->createMock(HookDispatcherInterface::class);
        $hooks->expects($enabled ? self::once() : self::never())->method('dispatchWithParameters');
        $factory = new HookableKpiRowFactory([], $hooks, 'orders');
        $gridFactory = $this->createMock(GridFactory::class);
        $gridFactory->method('getGrid')->willReturn($this->createMock(GridInterface::class));
        $filters = $isOrder ? new OrderFilters() : new CartFilter(ShopConstraint::shop(1));
        $request = new Request();
        $request->attributes->set('_legacy_controller', $isOrder ? 'AdminOrders' : 'AdminCarts');
        $controller->indexAction($request, $filters, $factory, $gridFactory);
    }

    public static function provideVisibility(): iterable
    {
        foreach ([OrderController::class, CartController::class] as $controller) {
            yield $controller . ' missing setting' => [$controller, null, true];
            yield $controller . ' enabled' => [$controller, '1', true];
            yield $controller . ' disabled' => [$controller, '0', false];
        }
    }
}
