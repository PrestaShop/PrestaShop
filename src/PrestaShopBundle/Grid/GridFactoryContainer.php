<?php

namespace PrestaShopBundle\Grid;

use PrestaShop\PrestaShop\Core\Grid\GridFactoryContainerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use Psr\Container\ContainerInterface;

class GridFactoryContainer implements GridFactoryContainerInterface
{
    public function __construct(
        private readonly ContainerInterface $container)
    {
    }

    public function getGridFactory(string $gridFactoryName): GridFactoryInterface
    {
        return $this->container->get($gridFactoryName);
    }
}
