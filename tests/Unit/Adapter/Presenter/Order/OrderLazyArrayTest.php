<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Presenter\Order;

use Order;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Presenter\Order\OrderLazyArray;
use ReflectionClass;

class OrderLazyArrayTest extends TestCase
{
    private const REFERENCE = 'XKBKNABJK';
    private const SECURE_KEY = 'b44a6d9efd7a0076a0fbce6b15eaf3b1';

    /**
     * UploadController serves a customization file to a visitor who proves ownership of the order
     * with its reference AND its secure key, so every file link must carry both.
     */
    public function testCustomizationFileUrlsCarryTheOrderReferenceAndSecureKey(): void
    {
        $products = [
            [
                'customizations' => [
                    [
                        'fields' => [
                            [
                                'type' => 'text',
                                'text' => 'engraving',
                            ],
                            [
                                'type' => 'image',
                                'image' => [
                                    'small' => ['url' => 'http://shop.test/index.php?controller=upload&file=abc_small'],
                                    'large' => ['url' => 'http://shop.test/index.php?controller=upload&file=abc'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $presented = $this->addOwnershipProof($products);

        $fields = $presented[0]['customizations'][0]['fields'];
        self::assertSame('engraving', $fields[0]['text']);
        self::assertSame(
            'http://shop.test/index.php?controller=upload&file=abc_small&reference=' . self::REFERENCE . '&secure_key=' . self::SECURE_KEY,
            $fields[1]['image']['small']['url']
        );
        self::assertSame(
            'http://shop.test/index.php?controller=upload&file=abc&reference=' . self::REFERENCE . '&secure_key=' . self::SECURE_KEY,
            $fields[1]['image']['large']['url']
        );
    }

    /**
     * The presenter is bound to legacy context and services, so the private helper is exercised directly.
     */
    private function addOwnershipProof(array $products): array
    {
        $order = (new ReflectionClass(Order::class))->newInstanceWithoutConstructor();
        $order->reference = self::REFERENCE;
        $order->secure_key = self::SECURE_KEY;

        $lazyArrayReflection = new ReflectionClass(OrderLazyArray::class);
        $lazyArray = $lazyArrayReflection->newInstanceWithoutConstructor();
        $orderProperty = $lazyArrayReflection->getProperty('order');
        $orderProperty->setAccessible(true);
        $orderProperty->setValue($lazyArray, $order);

        $method = $lazyArrayReflection->getMethod('addOrderOwnershipProofToCustomizationFileUrls');
        $method->setAccessible(true);

        return $method->invoke($lazyArray, $products);
    }
}
