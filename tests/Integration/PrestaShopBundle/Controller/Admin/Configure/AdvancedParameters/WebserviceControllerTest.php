<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Tests\Integration\Utility\LoginTrait;
use WebserviceKey;

class WebserviceControllerTest extends WebTestCase
{
    use LoginTrait;

    /**
     * @var KernelBrowser
     */
    protected $client;
    /**
     * @var Router
     */
    protected $router;
    /**
     * @var WebserviceKey
     */
    protected $webserviceKey;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();
        $this->loginUser($this->client);
        $this->router = self::$kernel->getContainer()->get('router');

        $this->webserviceKey = new WebserviceKey();
        $this->webserviceKey->key = 'DFS51LTKBBMBAF5QQRG523JMQYEHA4X7';
        $this->webserviceKey->description = 'Description WS Key';
        $this->webserviceKey->active = true;
        $this->webserviceKey->add();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->webserviceKey->delete();
    }

    /**
     * @return array<array<bool>>
     */
    public function dataProviderBoolean(): array
    {
        return [
            [
                true,
            ],
            [
                false,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderBoolean
     */
    public function testBulkEnable(bool $actual): void
    {
        $this->webserviceKey->active = $actual;
        $this->webserviceKey->save();

        $route = $this->router->generate(
            'admin_webservice_keys_bulk_enable'
        );

        $this->client->request(
            'POST',
            $route,
            [
                'webservice_key_bulk_action' => [
                    $this->webserviceKey->id,
                ],
            ]
        );
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirection());

        // Check session
        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();
        $all = $session->getFlashBag()->all();
        $this->assertArrayHasKey('success', $all);
        $this->assertSame('The status has been successfully updated.', $all['success'][0]);

        // Check status
        $webserviceKey = new WebserviceKey($this->webserviceKey->id);
        $this->assertTrue((bool) $webserviceKey->active);
    }

    /**
     * @dataProvider dataProviderBoolean
     */
    public function testBulkDisable(bool $actual): void
    {
        $this->webserviceKey->active = $actual;
        $this->webserviceKey->save();

        $this->client->request(
            'POST',
            $this->router->generate(
                'admin_webservice_keys_bulk_disable'
            ),
            [
                'webservice_key_bulk_action' => [
                    $this->webserviceKey->id,
                ],
            ]
        );
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirection());

        // Check session
        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();
        $all = $session->getFlashBag()->all();
        $this->assertArrayHasKey('success', $all);
        $this->assertSame('The status has been successfully updated.', $all['success'][0]);

        // Check status
        $webserviceKey = new WebserviceKey($this->webserviceKey->id);
        $this->assertFalse((bool) $webserviceKey->active);
    }
}
