<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Controller;

use CartController;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * A cart error that quotes a quantity taken from a combination has to name that combination too.
 * Several combinations of one product can sit in the same cart, and a message naming only the
 * product does not say which line it is about.
 */
class CartProductNameTest extends KernelTestCase
{
    /**
     * @dataProvider getCartLines
     */
    public function testACartLineIsNamedWithItsCombination(array $cartLine, string $expected, string $because): void
    {
        self::bootKernel();

        self::assertSame($expected, $this->nameOf($cartLine), $because);
    }

    public static function getCartLines(): array
    {
        return [
            'a combination' => [
                ['name' => 'Hummingbird printed t-shirt', 'attributes' => 'Size : S, Color : Black'],
                'Hummingbird printed t-shirt Size : S, Color : Black',
                'the customer has to be able to tell this line from another combination of the same product',
            ],
            // Cart::getProducts() merges every row with Cart::DEFAULT_ATTRIBUTES_KEYS, so a product
            // without combinations arrives with an empty string rather than no key at all.
            'no combination' => [
                ['name' => 'Mug The best is yet to come', 'attributes' => ''],
                'Mug The best is yet to come',
                'a product without combinations must not gain a trailing separator',
            ],
            'attributes key absent' => [
                ['name' => 'Mug The best is yet to come'],
                'Mug The best is yet to come',
                'a row that never went through the attributes merge must not raise a notice',
            ],
        ];
    }

    private function nameOf(array $cartLine): string
    {
        $controller = (new ReflectionClass(CartController::class))->newInstanceWithoutConstructor();

        $method = new ReflectionMethod(CartController::class, 'getCartProductName');
        $method->setAccessible(true);

        return $method->invoke($controller, $cartLine);
    }
}
