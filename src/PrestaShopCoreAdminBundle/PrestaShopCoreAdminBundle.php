<?php

namespace PrestaShopCoreAdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use PrestaShopCoreAdminBundle\DependencyInjection\CoreAdminExtension;

/**
 * Symfony entry point: adds Extension, that will add other stuff.
 */
class PrestaShopCoreAdminBundle extends Bundle
{
    /* (non-PHPdoc)
     * @see \Symfony\Component\HttpKernel\Bundle\Bundle::getContainerExtension()
     */
    public function getContainerExtension()
    {
        return new CoreAdminExtension();
    }
}
