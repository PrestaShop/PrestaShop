<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener;

use Context;
use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use ReflectionProperty;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Tests\Resources\classes\TestCookie;
use Tests\Unit\Core\Configuration\MockConfigurationTrait;

abstract class ContextEventListenerTestCase extends KernelTestCase
{
    use MockConfigurationTrait;

    /**
     * Since the listeners job is mostly to fill the builders's properties, that are private, this accessor method is convenient
     *
     * @param $object
     * @param string $propertyName
     *
     * @return mixed
     */
    protected function getPrivateField($object, string $propertyName)
    {
        $reflectionProperty = new ReflectionProperty(get_class($object), $propertyName);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }

    protected function mockLegacyContext(array $cookieValues = []): LegacyContext|MockObject
    {
        $cookie = new TestCookie();
        foreach ($cookieValues as $cookieKey => $cookieValue) {
            $cookie->{$cookieKey} = $cookieValue;
        }

        $context = $this->createMock(Context::class);
        $context->cookie = $cookie;

        $legacyContext = $this->createMock(LegacyContext::class);
        $legacyContext
            ->method('getContext')
            ->willReturn($context)
        ;

        return $legacyContext;
    }

    protected function createRequestEvent(Request $request): RequestEvent
    {
        return new RequestEvent(static::createKernel(), $request, HttpKernelInterface::MAIN_REQUEST);
    }

    protected function mockShopRepository(int $expectedShopId): ShopRepository|MockObject
    {
        $fakeShop = new Shop();
        $fakeShop->id = $expectedShopId;
        $fakeShop->name = 'Fake shop';
        $fakeShop->active = true;
        $fakeShop->theme_name = Theme::getDefaultTheme();
        $fakeShop->color = 'red';
        $fakeShop->physical_uri = $fakeShop->virtual_uri = $fakeShop->domain = $fakeShop->domain_ssl = '';

        $shopRepository = $this->createMock(ShopRepository::class);
        $shopRepository
            ->method('get')
            ->with(self::callback(function ($shopId) use ($expectedShopId) {
                self::assertInstanceOf(ShopId::class, $shopId);
                self::assertEquals($expectedShopId, $shopId->getValue());

                return true;
            }))
            ->willReturn($fakeShop)
        ;

        return $shopRepository;
    }

    protected function mockContextStateManager(): ContextStateManager|MockObject
    {
        return $this->createMock(ContextStateManager::class);
    }
}
