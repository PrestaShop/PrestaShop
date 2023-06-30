<?php

namespace PrestaShop\PrestaShop\Core\Grid;

interface GridFactoryContainerInterface
{
    public function getGridFactory(string $gridFactoryName): GridFactoryInterface;
}
