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

namespace Tests\Integration\Classes;

use Context;
use Dispatcher;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class LinkTest extends TestCase
{
    private function getProductLink(bool $statusUseRoutes, int $id_product, ?int $id_product_attribute): array
    {
        $reflectionDispatcher = new ReflectionClass('Dispatcher');
        $property = $reflectionDispatcher->getProperty('use_routes');
        $property->setAccessible(true);
        $property->setValue(Dispatcher::getInstance(), $statusUseRoutes);

        $url = Context::getContext()->link->getProductLink(
            $id_product,
            null,
            null,
            null,
            Context::getContext()->language->id,
            null,
            $id_product_attribute,
            false,
            false,
            true
        );

        return parse_url($url);
    }

    public function testUrlTakesVariantIntoAccountWithUrlRewriting(): void
    {
        $filename = basename($this->getProductLink(true, 1, 2)['path']);

        $this->assertEquals('1-2-hummingbird-printed-t-shirt.html', $filename);
    }

    public function testUrlIgnoresVariantIfNotSpecifiedWithUrlRewriting(): void
    {
        $filename = basename($this->getProductLink(true, 1, null)['path']);

        $this->assertEquals('1-hummingbird-printed-t-shirt.html', $filename);
    }

    public function testUrlTakesVariantIntoAccountWithoutUrlRewriting(): void
    {
        parse_str($this->getProductLink(false, 1, 6)['query'], $query);

        $this->assertEquals(1, $query['id_product']);
        $this->assertEquals(6, $query['id_product_attribute']);
    }

    public function testUrlIgnoresVariantIfNotSpecifiedWithoutUrlRewriting(): void
    {
        parse_str($this->getProductLink(false, 1, null)['query'], $query);

        $this->assertEquals(1, $query['id_product']);
        $this->assertArrayNotHasKey('id_product_attribute', $query);
    }
}
