<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener\Admin\Context;

use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\LegacyControllerContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\EventListener\Admin\Context\LegacyControllerContextSubscriber;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Tests\Unit\PrestaShopBundle\EventListener\ContextEventListenerTestCase;
use Twig\Environment;

class LegacyControllerContextSubscriberTest extends ContextEventListenerTestCase
{
    /**
     * @dataProvider getControllerNameValues
     *
     * @param Request $request
     * @param string $expectedControllerName
     */
    public function testControllerName(Request $request, string $expectedControllerName): void
    {
        $builder = $this->getBuilder();
        $legacyControllerContextListener = new LegacyControllerContextSubscriber($builder);
        $legacyControllerContextListener->onKernelRequest($this->createRequestEvent($request));
        $this->assertEquals($expectedControllerName, $this->getPrivateField($builder, 'controllerName'));
        $this->assertEquals(null, $this->getPrivateField($builder, 'redirectionUrl'));
    }

    public function getControllerNameValues(): iterable
    {
        yield 'simple query controller' => [
            new Request(['controller' => 'AdminProducts']),
            'AdminProducts',
        ];

        yield 'query controller with controller suffix' => [
            new Request(['controller' => 'AdminProductsController']),
            'AdminProducts',
        ];

        yield 'controller from attribute' => [
            new Request([], [], ['_legacy_controller' => 'AdminOrder']),
            'AdminOrder',
        ];

        yield 'controller from attribute with controller suffix' => [
            new Request([], [], ['_legacy_controller' => 'AdminOrderController']),
            'AdminOrder',
        ];
    }

    public function testRedirectionUrl(): void
    {
        $builder = $this->getBuilder();
        $legacyControllerContextListener = new LegacyControllerContextSubscriber($builder);
        $legacyControllerContextListener->onKernelRequest($this->createRequestEvent(new Request(['back' => 'index.php?toto=tata'])));
        $this->assertEquals('index.php?toto=tata', $this->getPrivateField($builder, 'redirectionUrl'));
    }

    private function getBuilder(): LegacyControllerContextBuilder
    {
        return new LegacyControllerContextBuilder(
            $this->createMock(EmployeeContext::class),
            ['AdminCarts'],
            $this->createMock(TabRepository::class),
            $this->createMock(ContainerInterface::class),
            $this->mockConfiguration(),
            $this->createMock(RequestStack::class),
            $this->createMock(ShopContext::class),
            $this->createMock(LanguageContext::class),
            'admin-dev',
            '9.0.0',
            $this->createMock(Environment::class)
        );
    }
}
