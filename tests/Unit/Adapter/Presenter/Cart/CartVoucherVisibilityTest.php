<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Presenter\Cart;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartLazyArray;
use ReflectionClass;
use ReflectionMethod;

/**
 * A cart rule that reduces nothing and frees no shipping used to be listed as "-0.00", which is what
 * a free gift rule looks like once its product has been deleted. `Cart::getSummaryDetails()` already
 * drops that case; these pin the same rule for the cart page.
 */
class CartVoucherVisibilityTest extends TestCase
{
    /**
     * @dataProvider vouchers
     */
    public function testItHidesOnlyTheVouchersThatGiveNothing(
        array $cartVoucher,
        bool $freeShippingOnly,
        $reduction,
        bool $expectedHidden,
        string $message
    ): void {
        $method = new ReflectionMethod(CartLazyArray::class, 'cartVoucherReducesNothing');
        $method->setAccessible(true);

        $presenter = (new ReflectionClass(CartLazyArray::class))->newInstanceWithoutConstructor();

        $this->assertSame(
            $expectedHidden,
            $method->invoke($presenter, $cartVoucher, $freeShippingOnly, $reduction),
            $message
        );
    }

    public function vouchers(): array
    {
        return [
            [['free_shipping' => 0], false, 0, true, 'a gift rule whose product is gone gives nothing'],
            [['free_shipping' => 0], false, '0.00', true, 'the same when the amount arrives as a string'],
            [['free_shipping' => 0], false, 5.0, false, 'a real reduction is shown'],
            [['free_shipping' => 0], false, 0.01, false, 'so is a very small one'],
            [['free_shipping' => 1], true, 0, false, 'a free shipping rule shows as Free shipping'],
            [['free_shipping' => 1], false, 0, false, 'free shipping plus a spent reduction still frees the shipping'],
            [['free_shipping' => 1], false, 5.0, false, 'free shipping with a reduction is shown'],
        ];
    }
}
