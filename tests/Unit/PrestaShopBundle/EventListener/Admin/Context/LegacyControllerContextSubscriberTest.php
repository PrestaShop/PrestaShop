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
