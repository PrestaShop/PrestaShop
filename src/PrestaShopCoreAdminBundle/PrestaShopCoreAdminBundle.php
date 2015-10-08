<?php

namespace PrestaShopCoreAdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use PrestaShopCoreAdminBundle\DependencyInjection\CoreAdminExtension;

class PrestaShopCoreAdminBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new CoreAdminExtension();
    }
}
