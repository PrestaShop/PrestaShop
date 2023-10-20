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

namespace Tests\Unit\PrestaShopBundle\EventListener;

use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\EventListener\ShopConstraintListener;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class ShopConstraintListenerTest extends KernelTestCase
{
    public function testMasterRequestEventHandling(): void
    {
        $request = new Request();
        $event = $this->createMasterRequestEvent($request);

        $listener = new ShopConstraintListener(
            $mockContext = $this->createMock(Context::class)
        );
        $allShopConstraint = ShopConstraint::allShops();

        $mockContext->expects($this->once())->method('getShopConstraint')->willReturn($allShopConstraint);

        $listener->onKernelRequest($event);

        self::assertSame($event->getRequest()->attributes->get('shopConstraint'), $allShopConstraint);
    }

    public function testCreateSubRequestEventHandling(): void
    {
        $request = new Request();
        $event = $this->createSubRequestEvent($request);

        $listener = new ShopConstraintListener(
            $this->createMock(Context::class)
        );

        $listener->onKernelRequest($event);

        self::assertNull($event->getRequest()->attributes->get('shopConstraint'));
    }

    private function createMasterRequestEvent(Request $request): RequestEvent
    {
        return new RequestEvent(static::createKernel(), $request, 1);
    }

    private function createSubRequestEvent(Request $request): RequestEvent
    {
        return new RequestEvent(static::createKernel(), $request, 2);
    }
}
