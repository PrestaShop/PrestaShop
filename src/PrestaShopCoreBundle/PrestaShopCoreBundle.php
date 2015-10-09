<?php

namespace PrestaShopCoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use PrestaShopCoreBundle\DependencyInjection\CoreExtension;

class PrestaShopCoreBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new CoreExtension();
    }
}
