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

namespace Tests\Integration\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class GetHookStatusTest extends ApiTestCase
{
    public function testGetHookStatus(): void
    {
        $inactiveHook = new \Hook();
        $inactiveHook->name = 'inactiveHook';
        $inactiveHook->active = false;
        $inactiveHook->add();

        $activeHook = new \Hook();
        $activeHook->name = 'activeHook';
        $activeHook->active = true;
        $activeHook->add();

        $response = static::createClient()->request('GET', '/new-api/hooks/' . $inactiveHook->id);
        self::assertEquals($response->getContent(), 'false');
        $response = static::createClient()->request('GET', '/new-api/hooks/' . $activeHook->id);
        self::assertEquals($response->getContent(), 'true');

        $inactiveHook->delete();
        $activeHook->delete();
    }
}
