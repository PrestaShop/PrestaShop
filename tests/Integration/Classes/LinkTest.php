<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
