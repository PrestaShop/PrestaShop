<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This class is used only because some parts of the Back Office require a Controller
 * to function (like the NullDispatcher). It is also used in integration tests for override.
 */
class DummyAdminControllerCore extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->id = 0;
        $this->controller_type = 'dummy';
    }

    public function checkAccess()
    {
        return true;
    }

    public function viewAccess($disable = false)
    {
        return true;
    }

    public function postProcess()
    {
        return true;
    }

    public function display()
    {
        return '';
    }

    public function setMedia($isNewTheme = false)
    {
        return null;
    }

    public function initHeader()
    {
        return '';
    }

    public function initContent()
    {
        return '';
    }

    public function initCursedPage()
    {
        return '';
    }

    public function initFooter()
    {
        return '';
    }

    protected function redirect()
    {
        return '';
    }

    protected function buildContainer(): ContainerInterface
    {
        return SymfonyContainer::getInstance();
    }
}
