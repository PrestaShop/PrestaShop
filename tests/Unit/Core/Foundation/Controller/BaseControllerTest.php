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

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\Controller;

use Exception;
use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use Core_Business_Stock_StockManager;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController;
use PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException;

class FakeBaseController extends BaseController
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

class BaseControllerTest extends UnitTestCase
{
    private function setup_env()
    {
        $fakeRoot = dirname(dirname(dirname(dirname(__DIR__)))); // to tests folder
        $this->assertEquals('tests', substr($fakeRoot, -5));

        $this->setConfiguration(array(
            '_PS_MODE_DEV_' => true
        ));
    }

    public function test_base_controller_methods()
    {
        $this->setup_env();

        $controller = new FakeBaseController();
        $w = new WarningException('message!', 42);
        $controller->addWarnings($w);
        $this->assertAttributeContains($w, 'warnings', $controller, 'Warning not inserted in the controller.');
        $this->assertAttributeCount(1, 'warnings', $controller, 'Warning array should be void before first insert.');

        $response = new Response();

        // test json
        $response->setContentData(array('a' => 'AA', 'b' => 'BB'));
        $controller->formatResponse('json', $response);
        $this->assertJson($response->getContent());
        $this->assertContains('"a": "AA"', $response->getContent());

        // test raw text (no transformation)
        $response->setContent('Hello Tom!');
        $controller->formatResponse('raw', $response);
        $this->assertEquals('Hello Tom!', $response->getContent());

        // test unknown format
        try {
            $controller->formatResponse('hello_tim', $response);
            $this->fail('Unknown format should throw ErrorException.');
        } catch (\ErrorException $ee) {
            $this->assertEquals('Unknown format.', $ee->getMessage());
        }

        // test html format call
        $response->setContentData(array('a' => 'AA', 'b' => 'BB'));
        $controller->formatResponse('html', $response);
        $this->assertAttributeEquals($response, 'formatHtmlCalledWith', $controller);

        // test none encap
        $response->setContent('Hello Tom!');
        $controller->encapsulateResponse('none', $response);
        $this->assertEquals('Hello Tom!', $response->getContent());

        // test html nude encap
        $response->setContent('Hello Tom!');
        $controller->encapsulateResponse('nude', $response);
        $this->assertContains('Hello Tom!', $response->getContent());
        $this->assertContains('<!DOCTYPE html>', $response->getContent());

        // test unknown encap
        try {
            $controller->encapsulateResponse('hello_tim', $response);
            $this->fail('Unknown encap should throw ErrorException.');
        } catch (\ErrorException $ee) {
            $this->assertEquals('Unknown encapsulation.', $ee->getMessage());
        }

        // test Layout encap
        $response->setContent('Hello Tom!');
        $controller->encapsulateResponse('layout', $response);
        $this->assertAttributeEquals($response, 'encapsulateCalledWith', $controller);
    }
}
