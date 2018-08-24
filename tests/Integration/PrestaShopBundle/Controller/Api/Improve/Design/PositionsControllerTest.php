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

use Symfony\Component\HttpFoundation\Response;
use Tests\Integration\PrestaShopBundle\Test\WebTestCase;

/**
 * @group api
 */
class PositionsControllerTest extends WebTestCase
{
    public function testMoveHookPositionWithUnknowModule()
    {
        $this->client->request(
            'POST',
            $this->router->generate(
                'api_improve_design_positions_update'
            ),
            [
                'moduleId' => 999999,
                'hookId' => 161,
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
                'moduleId' => 4,
                'hookId' => 161,
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
                'moduleId' => 4,
                'hookId' => 3,
                'way' => 1,
                'positions' => [
                    '65_3',
                    '65_4',
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
                'moduleId' => 4,
                'hookId' => 3,
                'way' => 0,
                'positions' => [
                    '65_4',
                    '65_3',
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
