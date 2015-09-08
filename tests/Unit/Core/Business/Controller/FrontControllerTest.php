<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Business\Controller;

use Exception;
use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController;
use PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException;
use PrestaShop\PrestaShop\Core\Business\Controller\FrontController;
use PrestaShop\PrestaShop\Core\Business\Routing\FrontRouter;

class FakeFrontController extends FrontController
{
    public $formatHtmlCalledWith = false;
    public $encapsulateCalledWith = false;

    protected function formatHtmlResponse(Response &$response)
    {
        $this->formatHtmlCalledWith = $response;
    }

    protected function encapsulateLayout(Response &$response)
    {
        $this->encapsulateCalledWith = $response;
    }
}

class FrontControllerTest extends UnitTestCase
{
    private function setup_env()
    {
        $fakeRoot = dirname(dirname(dirname(dirname(__DIR__)))); // to tests folder
        $this->assertEquals('tests', substr($fakeRoot, -5));

        $this->setConfiguration(array(
            '_PS_ROOT_DIR_' => $fakeRoot,
            '_PS_CACHE_DIR_' => $fakeRoot.'/cache/',
            '_PS_MODULE_DIR_' => $fakeRoot.'/resources/module/',
            '_PS_MODE_DEV_' => true
        ));
    }

    public function test_front_controller_methods()
    {
        $this->setup_env();

        $router = FrontRouter::getInstance();
        $controller = new FakeFrontController($router);
        // TODO : le controller et ses traits de base.
        // TODO : test URL generation for Front
    }
}
