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

namespace Tests\Unit\PrestaShopBundle\EventListener\Context\API;

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Language\Repository\LanguageRepository as ObjectModelLanguageRepository;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale\RepositoryInterface;
use PrestaShopBundle\EventListener\Context\API\LanguageContextListener;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\PrestaShopBundle\EventListener\Context\ContextEventListenerTestCase;

class LanguageContextListenerTest extends ContextEventListenerTestCase
{
    private const DEFAULT_LANGUAGE_ID = 42;
    private const QUERY_LANGUAGE_ID = 51;
    private const SHOP_ID = 69;

    public function testLanguageContextBasedOnRequestParameter(): void
    {
        // Create request that mimic a call to external API
        $event = $this->createRequestEvent(new Request(['langId' => self::QUERY_LANGUAGE_ID], [], ['_controller' => 'api_platform.action.placeholder']));

        $languageContextBuilder = new LanguageContextBuilder(
            $this->createMock(LanguageRepositoryInterface::class),
            $this->createMock(RepositoryInterface::class),
            $this->createMock(ContextStateManager::class),
            $this->createMock(ObjectModelLanguageRepository::class)
        );

        $listener = new LanguageContextListener(
            $languageContextBuilder,
            $this->mockConfiguration(['PS_LANG_DEFAULT' => self::DEFAULT_LANGUAGE_ID], ShopConstraint::shop(self::SHOP_ID)),
            $this->mockShopContext()
        );

        $listener->onKernelRequest($event);
        $this->assertEquals(self::QUERY_LANGUAGE_ID, $this->getPrivateField($languageContextBuilder, 'languageId'));
        $this->assertEquals(self::DEFAULT_LANGUAGE_ID, $this->getPrivateField($languageContextBuilder, 'defaultLanguageId'));
    }

    public function testLanguageContextBasedOnShopConfiguration(): void
    {
        // Create request that mimic a call to external API (no langId parameter)
        $event = $this->createRequestEvent(new Request([], [], ['_controller' => 'api_platform.action.placeholder']));

        $languageContextBuilder = new LanguageContextBuilder(
            $this->createMock(LanguageRepositoryInterface::class),
            $this->createMock(RepositoryInterface::class),
            $this->createMock(ContextStateManager::class),
            $this->createMock(ObjectModelLanguageRepository::class)
        );

        $listener = new LanguageContextListener(
            $languageContextBuilder,
            $this->mockConfiguration(['PS_LANG_DEFAULT' => self::DEFAULT_LANGUAGE_ID], ShopConstraint::shop(self::SHOP_ID)),
            $this->mockShopContext()
        );

        $listener->onKernelRequest($event);
        $this->assertEquals(self::DEFAULT_LANGUAGE_ID, $this->getPrivateField($languageContextBuilder, 'languageId'));
        $this->assertEquals(self::DEFAULT_LANGUAGE_ID, $this->getPrivateField($languageContextBuilder, 'defaultLanguageId'));
    }

    public function testRequestNotForApiIsIgnored(): void
    {
        $event = $this->createRequestEvent(new Request());

        $listener = new LanguageContextListener(
            $this->mockUnusedBuilder(),
            $this->mockConfiguration(),
            $this->mockShopContext()
        );
        $listener->onKernelRequest($event);
    }

    private function mockUnusedBuilder(): LanguageContextBuilder|MockObject
    {
        $builder = $this->createMock(LanguageContextBuilder::class);
        $builder->expects($this->never())->method('setLanguageId');
        $builder->expects($this->never())->method('setDefaultLanguageId');

        return $builder;
    }

    private function mockShopContext(): ShopContext|MockObject
    {
        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('getId')->willReturn(self::SHOP_ID);

        return $shopContext;
    }
}
