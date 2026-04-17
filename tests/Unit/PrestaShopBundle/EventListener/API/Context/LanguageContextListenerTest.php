<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener\API\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Language\Repository\LanguageRepository as ObjectModelLanguageRepository;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale\RepositoryInterface;
use PrestaShopBundle\EventListener\API\Context\LanguageContextListener;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\PrestaShopBundle\EventListener\ContextEventListenerTestCase;

class LanguageContextListenerTest extends ContextEventListenerTestCase
{
    private const DEFAULT_LANGUAGE_ID = 42;
    private const QUERY_LANGUAGE_ID = 51;
    private const SHOP_ID = 69;

    public function testLanguageContextBasedOnRequestParameter(): void
    {
        // Create request that mimic a call to external API
        $event = $this->createRequestEvent(new Request(['langId' => self::QUERY_LANGUAGE_ID]));

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
        $event = $this->createRequestEvent(new Request());

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

    private function mockShopContext(): ShopContext|MockObject
    {
        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('getId')->willReturn(self::SHOP_ID);

        return $shopContext;
    }
}
