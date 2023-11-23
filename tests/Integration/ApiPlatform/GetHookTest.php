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

namespace Tests\Integration\ApiPlatform;

use Tests\Resources\DatabaseDump;

class GetHookTest extends ApiTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(['hook']);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['hook']);
    }

    public function testGetHook(): void
    {
        $hook = new \Hook();
        $hook->name = 'testHook';
        $hook->active = true;
        $hook->add();

        $bearerToken = $this->getBearerToken([
            'hook_read',
            'hook_write',
        ]);

        $response = static::createClient()->request('GET', '/api/hooks/' . (int) $hook->id, ['auth_bearer' => $bearerToken]);
        self::assertEquals(json_decode($response->getContent())->active, $hook->active);
        self::assertResponseStatusCodeSame(200);

        static::createClient()->request('GET', '/api/hooks/' . 9999, ['auth_bearer' => $bearerToken]);
        self::assertResponseStatusCodeSame(404);

        static::createClient()->request('GET', '/api/hooks/' . $hook->id);
        self::assertResponseStatusCodeSame(401);

        $hook->delete();
    }
}
