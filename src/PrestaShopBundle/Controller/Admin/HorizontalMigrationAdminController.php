<?php

namespace PrestaShopBundle\Controller\Admin;

use PrestaShopBundle\Bridge\ControllerConfiguration;
use Symfony\Component\HttpFoundation\Response;

class HorizontalMigrationAdminController extends FrameworkBundleAdminController
{
    /**
     * @var ControllerConfiguration
     */
    protected $configurator;

    public function renderSmarty(string $content, ControllerConfiguration $configurator): Response
    {
        return $this->get('prestashop.core.bridge.smarty_bridge')->render($content, $configurator);
    }
}
