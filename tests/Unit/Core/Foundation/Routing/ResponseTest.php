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

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\Routing;

use Exception;
use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use Core_Business_Stock_StockManager;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController;

class ResponseTest extends UnitTestCase
{
    private $called = false;

    public function test_content_data_manipulation()
    {
        $response = new Response('base content');
        
        $response->setContentData(array('k1' => 'v1', 'k2' => 'v2'));
        $this->assertAttributeContains('base content', 'content', $response, 'The content attribute should never be modified by any contentData operation.');
        $this->assertAttributeContains('v1', 'contentData', $response, 'The contentData initial set failed (values).');
        $this->assertAttributeContains('v2', 'contentData', $response, 'The contentData initial set failed (values).');
        $this->assertArrayHasKey('k1', $response->getContentData(), 'The contentData initial set failed (keys), or getter failed.');
        
        $response->addContentData('k3', 'v3');
        $this->assertAttributeContains('v3', 'contentData', $response, 'The contentData add failed (values).');
        $this->assertArrayHasKey('k3', $response->getContentData(), 'The contentData add failed (keys), or getter failed.');
        
        $r = $response->addContentData('k3', 'f3'); // forbidden by addContentData because key exists
        $this->assertFalse($r, 'addContentData() must reject adding an existing key.');
        $this->assertAttributeContains('v3', 'contentData', $response, 'The contentData add failed (value replacement forbidden from addContentData()).');
        
        $r = $response->replaceContentData('k3', 'r3');
        $this->assertAttributeNotContains('v3', 'contentData', $response, 'The contentData replacement failed (value replacement).');
        $this->assertAttributeContains('r3', 'contentData', $response, 'The contentData replacement failed (value replacement).');
        $this->assertEquals($response, $r, 'replaceContentData() must be chainable (return $this).');
        
        $r = $response->replaceContentData('k4', 'v4');
        $this->assertAttributeContains('v4', 'contentData', $response, 'The contentData replacement failed (value added by replaceContentData()).');
        $this->assertEquals($response, $r, 'replaceContentData() must be chainable (return $this).');
        
        $response->setContentData(array('k1' => 'r1', 'k2' => 'r2'));
        $this->assertAttributeNotContains('r3', 'contentData', $response, 'The contentData set failed (all values reset).');
        $this->assertAttributeContains('r1', 'contentData', $response, 'The contentData initial set failed (values).');
        
        $this->assertAttributeContains('base content', 'content', $response, 'The content attribute should never be modified by any contentData operation.');
    }

    public function test_response_format_manipulation()
    {
        $response = new Response('base content');
        $this->assertFalse($response->getResponseFormat(), 'Initial response format must be False.');
        $response->setResponseFormat(BaseController::RESPONSE_AJAX_HTML);
        $this->assertEquals(BaseController::RESPONSE_AJAX_HTML, $response->getResponseFormat());
        $this->assertAttributeContains('base content', 'content', $response, 'The content attribute should never be modified by any responseFormat operation.');
    }
    
    public function test_template_engine_manipulation()
    {
        $response = new Response('base content');
        $this->assertNotFalse($response->getTemplateEngine(), 'Initial template engine callable must not be False (auto init).');
        
        $that = $this;
        $this->called = false;
        
        $callable = function () use ($that) {
            $that->called = true;
        };
        $response->setTemplateEngine($callable);
        $r = $response->getTemplateEngine();
        $r();
        $this->assertTrue($this->called, 'TemplateEngine callable not called.');
    }
}
