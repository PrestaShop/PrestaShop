<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener\Admin\Context;

use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Country\Repository\CountryRepository;
use PrestaShop\PrestaShop\Core\Context\CountryContextBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShopBundle\EventListener\Admin\Context\CountryContextSubscriber;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\PrestaShopBundle\EventListener\ContextEventListenerTestCase;

class CountryContextSubscriberTest extends ContextEventListenerTestCase
{
    public function testKernelRequest(): void
    {
        $countryContextBuilder = new CountryContextBuilder(
            $this->createMock(CountryRepository::class),
            $this->createMock(ContextStateManager::class),
            $this->createMock(LanguageContext::class),
        );
        $listener = new CountryContextSubscriber(
            $countryContextBuilder,
            $this->mockConfiguration(['PS_COUNTRY_DEFAULT' => 42]),
        );

        $event = $this->createRequestEvent(new Request());
        $listener->onKernelRequest($event);
        $this->assertEquals(42, $this->getPrivateField($countryContextBuilder, 'countryId'));
    }
}
