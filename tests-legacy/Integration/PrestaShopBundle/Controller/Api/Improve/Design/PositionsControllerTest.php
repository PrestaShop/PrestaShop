<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Integration\PrestaShopBundle\Controller\Api\Improve\Design;

use Cache;
use Db;
use Hook;
use LegacyTests\Integration\PrestaShopBundle\Test\WebTestCase;
use Module;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group api
 */
class PositionsControllerTest extends WebTestCase
{
    protected $firstModuleId;
    protected $secondModuleId;
    protected $hookId;

    protected function setUp()
    {
        Cache::clear();
        Module::clearStaticCache();

        parent::setUp();

        /** @var ModuleManager */
        $moduleManager = self::$kernel->getContainer()->get('prestashop.module.manager');

        // Unregister all modules hooked on displayHome
        Db::getInstance()->execute('
            DELETE FROM `' . _DB_PREFIX_ . 'hook_module`
            WHERE `id_hook` = '. (int) Hook::getIdByName('displayHome')
        );

        //We use modules present in tests-legacy/resources/modules to be independent with the external API
        //We install two modules that are not present in the test db to be sure every step of the install performs correctly
        //And both modules have a common hook displayHome
        //We force the re-installation because modules may be in database but without their hook registered and this way we are sure
        //of their respective positions at each test
        if ($moduleManager->isInstalled('ps_banner')) {
            $moduleManager->uninstall('ps_banner');
        }
        $moduleManager->install('ps_banner');

        if ($moduleManager->isInstalled('bankwire')) {
            $moduleManager->uninstall('bankwire');
        }
        $moduleManager->install('bankwire');

        $this->firstModuleId = $moduleManager->getModuleIdByName('ps_banner');
        $this->secondModuleId = $moduleManager->getModuleIdByName('bankwire');
        $this->hookId = Hook::getIdByName('displayHome');
    }

    /**
     * Way = 1 means we increment the position (correspond to "bottom" of grid)
     * Module is unknown so we can't update it
     */
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
                'positions' => [],
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

    /**
     * Way = 1 means we increment the position (correspond to "bottom" of grid)
     * Positions are not defined, so the new position is defined only by the way parameter
     * First module can be "put down" so eevrything works fine
     */
    public function testWithoutPositionsFromFirstToSecondSuccess()
    {
        $this->client->request(
            'POST',
            $this->router->generate(
                'api_improve_design_positions_update'
            ),
            [
                'moduleId' => $this->firstModuleId,
                'hookId' => $this->hookId,
                'way' => 1,
                'positions' => [],
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

    /**
     * Way = 1 means we increment the position (correspond to "bottom" of grid)
     * Positions are not defined, so the new position is defined only by the way parameter
     * This time we try to put down the second module which is already the last one so the request fails
     */
    public function testWithoutPositionsFromLastToLastFailure()
    {
        $this->client->request(
            'POST',
            $this->router->generate(
                'api_improve_design_positions_update'
            ),
            [
                'moduleId' => $this->secondModuleId,
                'hookId' => $this->hookId,
                'way' => 1,
                'positions' => [],
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

    /**
     * Way = 0 means we decrement the position (correspond to "top" of grid)
     * Positions are defined, so they indicate the expected order
     * This time we try to put up the second module so it is supposed to work
     */
    public function testWithPositionsFromSecondToFirstSuccess()
    {
        $this->client->request(
            'POST',
            $this->router->generate(
                'api_improve_design_positions_update'
            ),
            [
                'moduleId' => $this->secondModuleId,
                'hookId' => $this->hookId,
                'way' => 0,
                'positions' => [
                    sprintf('%d_%d', $this->secondModuleId, $this->hookId),
                    sprintf('%d_%d', $this->firstModuleId, $this->hookId),
                ],
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

    /**
     * Way = 0 means we decrement the position (correspond to "top" of grid)
     * Positions are defined, so they indicate the expected order
     * This time we try to put up the first module which is already at the top so it fails
     */
    public function testWithPositionsFromFirstToFirstFailure()
    {
        $this->client->request(
            'POST',
            $this->router->generate(
                'api_improve_design_positions_update'
            ),
            [
                'moduleId' => $this->firstModuleId,
                'hookId' => $this->hookId,
                'way' => 0,
                'positions' => [
                    sprintf('%d_%d', $this->firstModuleId, $this->hookId),
                    sprintf('%d_%d', $this->secondModuleId, $this->hookId),
                ],
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
}
