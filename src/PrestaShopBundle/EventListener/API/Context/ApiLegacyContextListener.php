<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\API\Context;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class ApiLegacyContextListener
{
    public function __construct(
        private readonly iterable $legacyBuilders
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        foreach ($this->legacyBuilders as $legacyBuilder) {
            $legacyBuilder->buildLegacyContext();
        }
    }
}
