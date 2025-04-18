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

namespace PrestaShopBundle\EventListener\API\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Core\Context\CurrencyContextBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\PrestaShopBundle\EventListener\ContextEventListenerTestCase;

class CurrencyContextListenerTest extends ContextEventListenerTestCase
{
    private const DEFAULT_CURRENCY_ID = 42;
    private const QUERY_CURRENCY_ID = 51;
    private const SHOP_ID = 69;

    public function testCurrencyContextBasedOnRequestParameter(): void
    {
        // Create request that mimic a call to external API
        $event = $this->createRequestEvent(new Request(['currencyId' => self::QUERY_CURRENCY_ID]));

        $currencyContextBuilder = new CurrencyContextBuilder(
            $this->createMock(CurrencyRepository::class),
            $this->createMock(ContextStateManager::class),
            $this->createMock(LanguageContext::class)
        );

        $listener = new CurrencyContextListener(
            $currencyContextBuilder,
            $this->mockConfiguration(['PS_CURRENCY_DEFAULT' => self::DEFAULT_CURRENCY_ID], ShopConstraint::shop(self::SHOP_ID)),
            $this->mockShopContext()
        );

        $listener->onKernelRequest($event);
        $this->assertEquals(self::QUERY_CURRENCY_ID, $this->getPrivateField($currencyContextBuilder, 'currencyId'));
    }

    public function testCurrencyContextBasedOnShopConfiguration(): void
    {
        // Create request that mimic a call to external API (no currencyId parameter)
        $event = $this->createRequestEvent(new Request());

        $currencyContextBuilder = new CurrencyContextBuilder(
            $this->createMock(CurrencyRepository::class),
            $this->createMock(ContextStateManager::class),
            $this->createMock(LanguageContext::class)
        );

        $listener = new CurrencyContextListener(
            $currencyContextBuilder,
            $this->mockConfiguration(['PS_CURRENCY_DEFAULT' => self::DEFAULT_CURRENCY_ID], ShopConstraint::shop(self::SHOP_ID)),
            $this->mockShopContext()
        );

        $listener->onKernelRequest($event);
        $this->assertEquals(self::DEFAULT_CURRENCY_ID, $this->getPrivateField($currencyContextBuilder, 'currencyId'));
    }

    private function mockShopContext(): ShopContext|MockObject
    {
        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('getId')->willReturn(self::SHOP_ID);

        return $shopContext;
    }
}
