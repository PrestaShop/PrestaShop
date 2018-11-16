<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\PrestaShopBundle\Controller\Api\Improve\Design;

use Cache;
use Module;
use Hook;
use Symfony\Component\HttpFoundation\Response;
use Tests\Integration\PrestaShopBundle\Test\WebTestCase;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManager;

/**
 * @group api
 */
class PositionsControllerTest extends WebTestCase
{
    protected $moduleId;
    protected $otherModuleId;
    protected $hookId;

    public function setUp()
    {
        Cache::clear();
        Module::clearStaticCache();

        parent::setUp();

        /* @var ModuleManager */
        $moduleManager = self::$kernel->getContainer()->get('prestashop.module.manager');
        if (!Module::isInstalled('bankwire')) {
            $moduleManager->install('bankwire');
        }

        if (!Module::isInstalled('ps_featuredproducts')) {
            $moduleManager->install('ps_featuredproducts');
        }

        $this->moduleId = Module::getModuleIdByName('bankwire');
        $this->otherModuleId = Module::getModuleIdByName('ps_featuredproducts');
        $this->hookId = Hook::getIdByName('displayHome');
    }

    public function testMoveHookPositionWithUnknownModule()
    {
        $this->client->request(
            'POST',
            $this->router->generate(
                'api_improve_design_positions_update'
            ),
            [
                'moduleId' => 999999,
                'hookId' => $this->hookId,
                'way' => 1,
                'positions' => []
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode()
        );

        $json = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('hasError', $json['data']);
        $this->assertTrue($json['data']['hasError']);
        $this->assertEquals(['This module cannot be loaded.'], $json['data']['errors']);
    }

    public function testMoveHookPositionToBottomWithUnavailablePositions()
    {
        $this->client->request(
            'POST',
            $this->router->generate(
                'api_improve_design_positions_update'
            ),
            [
                'moduleId' => $this->moduleId,
                'hookId' => $this->hookId,
                'way' => 1,
                'positions' => []
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode()
        );

        $json = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('hasError', $json['data']);
        $this->assertTrue($json['data']['hasError']);
        $this->assertEquals(['Cannot update module position.'], $json['data']['errors']);
    }

    public function testMoveHookPositionToBottom()
    {
        $this->client->request(
            'POST',
            $this->router->generate(
                'api_improve_design_positions_update'
            ),
            [
                'moduleId' => $this->moduleId,
                'hookId' => $this->otherModuleId,
                'way' => 1,
                'positions' => [
                    sprintf('%d_%d', $this->otherModuleId, $this->hookId),
                    sprintf('%d_%d', $this->moduleId, $this->hookId),
                ]
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode()
        );

        $json = json_decode($response->getContent(), true);
        $this->assertArrayNotHasKey('hasError', $json['data']);
        $this->assertEquals([], $json['data']);
    }

    public function testMoveHookPositionToTop()
    {
        $this->client->request(
            'POST',
            $this->router->generate(
                'api_improve_design_positions_update'
            ),
            [
                'moduleId' => $this->moduleId,
                'hookId' => $this->hookId,
                'way' => 0,
                'positions' => [
                    sprintf('%d_%d', $this->moduleId, $this->hookId),
                    sprintf('%d_%d', $this->otherModuleId, $this->hookId),
                ]
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode()
        );

        $json = json_decode($response->getContent(), true);
        $this->assertArrayNotHasKey('hasError', $json['data']);
        $this->assertEquals([], $json['data']);
    }
}
