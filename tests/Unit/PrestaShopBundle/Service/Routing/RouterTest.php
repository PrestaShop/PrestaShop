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

namespace Tests\Unit\PrestaShopBundle\Service\Routing;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Service\Routing\Router;

class RouterTest extends TestCase
{
    public function testGenerateTokenizedUrlWithFragments(): void
    {
        $url = 'my-shop.com/product#routing-in-prestashop';
        $route = Router::generateTokenizedUrl($url, 'token');
        static::assertEquals('my-shop.com/product?_token=token#routing-in-prestashop', $route);

        $url = 'my-shop.com/product?delete=1#routing-in-prestashop';
        $route = Router::generateTokenizedUrl($url, 'token');
        static::assertEquals('my-shop.com/product?delete=1&_token=token#routing-in-prestashop', $route);

        $url = 'localhost/shopp/product?delete=1&confirm=1#routing-in-prestashop/tokens?route';
        $route = Router::generateTokenizedUrl($url, 'token');
        static::assertEquals('localhost/shopp/product?delete=1&confirm=1&_token=token#routing-in-prestashop/tokens?route', $route);
    }
}
