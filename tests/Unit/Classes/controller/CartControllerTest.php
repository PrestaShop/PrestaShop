<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Controller;

use CartControllerCore;
use PHPUnit\Framework\TestCase;
use Product;

class CartControllerTest extends TestCase
{
    /**
     * @dataProvider nonPositiveQuantityProvider
     */
    public function testItDoesNotRaiseAvailabilityErrorForNonPositiveQuantity(int $idProductAttribute, int $qtyToCheck): void
    {
        $controller = new TestCartController();
        $controller->setIdProductAttribute($idProductAttribute);

        $product = $this->createMock(Product::class);

        $this->assertFalse($controller->checkAvailabilityError($product, $qtyToCheck));
    }

    public static function nonPositiveQuantityProvider(): iterable
    {
        yield 'simple product zero quantity' => [0, 0];
        yield 'simple product negative quantity' => [0, -1];
        yield 'combination product zero quantity' => [42, 0];
        yield 'combination product negative quantity' => [42, -3];
    }
}

class TestCartController extends CartControllerCore
{
    public function __construct()
    {
    }

    public function setIdProductAttribute(int $idProductAttribute): void
    {
        $this->id_product_attribute = $idProductAttribute;
    }

    public function checkAvailabilityError(Product $product, int $qtyToCheck): bool
    {
        return $this->shouldAvailabilityErrorBeRaised($product, $qtyToCheck);
    }
}
