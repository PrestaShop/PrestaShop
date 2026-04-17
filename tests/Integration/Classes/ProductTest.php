<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use PHPUnit\Framework\TestCase;
use Product;
use Tests\Integration\Utility\ContextMockerTrait;

class ProductTest extends TestCase
{
    use ContextMockerTrait;

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();
    }

    public function testSaveActiveRecordStyle(): void
    {
        $product = new Product(null, false, 1);
        $product->name = 'A Product';
        $product->price = 42.42;
        $product->link_rewrite = 'a-product';
        $this->assertTrue($product->save());
    }
}
