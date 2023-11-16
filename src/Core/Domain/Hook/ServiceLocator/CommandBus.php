<?php

namespace PrestaShop\PrestaShop\Core\Domain\Hook\ServiceLocator;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;

class CommandBus
{
    public function __construct(
        // creates a service locator with all the services tagged with 'app.handler'
        #[TaggedLocator('prestashop.api.handler')]
        private ContainerInterface $locator,
    ) {
    }
}
