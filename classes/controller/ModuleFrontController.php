<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShop\PrestaShop\Adapter\Module\Controller\ModuleFrontControllerTrait;

class ModuleFrontControllerCore extends FrontController
{
    use ModuleFrontControllerTrait;

    public function __construct()
    {
        $this->initializeModuleFromRequest();

        parent::__construct();
    }
}
