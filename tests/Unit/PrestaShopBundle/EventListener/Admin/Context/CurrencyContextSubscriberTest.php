<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener\Admin\Context;

use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Core\Context\CurrencyContextBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShopBundle\EventListener\Admin\Context\CurrencyContextSubscriber;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\PrestaShopBundle\EventListener\ContextEventListenerTestCase;

class CurrencyContextSubscriberTest extends ContextEventListenerTestCase
{
    public function testKernelRequest(): void
    {
        $currencyContextBuilder = new CurrencyContextBuilder(
            $this->createMock(CurrencyRepository::class),
            $this->createMock(ContextStateManager::class),
            $this->createMock(LanguageContext::class)
        );
        $listener = new CurrencyContextSubscriber(
            $currencyContextBuilder,
            $this->mockConfiguration(['PS_CURRENCY_DEFAULT' => 42]),
        );

        $event = $this->createRequestEvent(new Request());
        $listener->onKernelRequest($event);
        $this->assertEquals(42, $this->getPrivateField($currencyContextBuilder, 'currencyId'));
    }
}
