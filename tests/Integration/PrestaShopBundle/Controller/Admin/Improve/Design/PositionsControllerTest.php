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

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Improve\Design;

use Cache;
use Hook;
use Module;
use Symfony\Component\HttpFoundation\Response;
use Tests\Integration\PrestaShopBundle\Test\WebTestCase;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManager;

/**
 * @group demo
 */
class PositionsControllerTest extends WebTestCase
{
    protected $moduleId;
    protected $hookId;

    public function setUp()
    {
        Cache::clear();
        Module::clearStaticCache();

        parent::setUp();

        if (!Module::isInstalled('ps_emailsubscription')) {
            /* @var ModuleManager */
            $moduleManager = self::$kernel->getContainer()->get('prestashop.module.manager');
            $moduleManager->install('ps_emailsubscription');
        }

        $this->moduleId = Module::getModuleIdByName('ps_emailsubscription');
        $this->hookId = Hook::getIdByName('displayFooterBefore');
    }

    public function testUnhooksListAction()
    {
        $this->client->request(
            'POST',
            $this->router->generate(
                'admin_modules_positions_unhook'
            ),
            [
                'unhooks' => [
                    sprintf('%d_%d', $this->hookId, $this->moduleId),
                    sprintf('%d_1000', $this->hookId),
                    sprintf('10000_%d', $this->moduleId),
                    sprintf(
                        '%d_%d',
                        $this->hookId,
                        $this->moduleId
                    ),
                    'aa_dd',
                    'something'
                ],
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(
            Response::HTTP_FOUND,
            $response->getStatusCode()
        );

        $messages = self::$kernel->getContainer()->get('session')->getFlashBag()->all();
        $this->assertArrayHasKey(
            'error',
            $messages
        );
        $this->assertContains(
            'This module cannot be loaded.',
            $messages['error']
        );
        $this->assertContains(
            'Hook cannot be loaded.',
            $messages['error']
        );
        $this->assertArrayNotHasKey(
            'success',
            $messages
        );
    }

    public function testUnhooksWithQueryAction()
    {
        $this->client->request(
            'GET',
            $this->router->generate(
                'admin_modules_positions_unhook'
            ),
            [
                'moduleId' => $this->moduleId,
                'hookId' => $this->hookId,
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(
            Response::HTTP_FOUND,
            $response->getStatusCode()
        );

        $messages = self::$kernel->getContainer()->get('session')->getFlashBag()->all();
        $this->assertArrayNotHasKey(
            'error',
            $messages
        );
        $this->assertArrayHasKey(
            'success',
            $messages
        );
        $this->assertContains(
            'The module was successfully removed from the hook.',
            $messages['success']
        );
    }
}
