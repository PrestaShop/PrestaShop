<?php

namespace PrestaShopCoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use PrestaShopCoreBundle\DependencyInjection\CoreExtension;

/**
 * Symfony entry point: adds Extension, that will add other stuff.
 */
class PrestaShopCoreBundle extends Bundle
{
    /* (non-PHPdoc)
     * @see \Symfony\Component\HttpKernel\Bundle\Bundle::getContainerExtension()
     */
    public function getContainerExtension()
    {
        return new CoreExtension();
    }
}
