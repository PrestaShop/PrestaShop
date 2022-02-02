<?php

namespace PrestaShopBundle\Bridge;

/**
 * Define contract for all hydrator
 */
interface HydratorInterface
{
    /**
     * @param ControllerConfiguration $controllerConfiguration
     *
     * @return void
     */
    public function hydrate(ControllerConfiguration $controllerConfiguration);
}
