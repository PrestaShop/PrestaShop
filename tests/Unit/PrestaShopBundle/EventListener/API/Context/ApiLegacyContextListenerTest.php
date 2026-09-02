<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener\API\Context;

use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShopBundle\EventListener\API\Context\ApiLegacyContextListener;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\PrestaShopBundle\EventListener\ContextEventListenerTestCase;

class ApiLegacyContextListenerTest extends ContextEventListenerTestCase
{
    public function testLegacyContextIsBuilt(): void
    {
        $event = $this->createRequestEvent(new Request());
        $builder = $this->createMock(LanguageContextBuilder::class);

        $listener = new ApiLegacyContextListener(
            [
                $builder,
            ]
        );

        $builder->expects(static::once())->method('buildLegacyContext');

        $listener->onKernelRequest($event);
    }
}
