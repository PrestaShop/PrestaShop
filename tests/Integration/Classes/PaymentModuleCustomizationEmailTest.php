<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use PaymentModule;
use Product;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The order-confirmation email lists each line's customizations. Product::getAllCustomizedDatas()
 * buckets them under the cart's delivery address, so a multi-shipping package (whose own delivery
 * address differs) used to lose them. PaymentModule now flattens the address buckets.
 */
class PaymentModuleCustomizationEmailTest extends KernelTestCase
{
    public function testCustomizationsAreFoundRegardlessOfTheDeliveryAddressBucket(): void
    {
        self::bootKernel();

        // getAllCustomizedDatas() indexes the line's customizations under the cart's delivery
        // address (here 4); a multi-shipping package ships to a different address (5).
        $cartAddressId = 4;
        $packageAddressId = 5;
        $customizedDatas = [
            7 => [ // id_product
                0 => [ // id_product_attribute
                    $cartAddressId => [
                        99 => [ // id_customization
                            'quantity' => 1,
                            'id_customization' => 99,
                            'datas' => [
                                Product::CUSTOMIZE_TEXTFIELD => [
                                    ['name' => 'Message', 'value' => 'Hello'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // The previous code indexed by the package address, which has no bucket -> customizations lost.
        self::assertArrayNotHasKey($packageAddressId, $customizedDatas[7][0]);

        $flatten = new ReflectionMethod(PaymentModule::class, 'flattenProductCustomizations');
        $flatten->setAccessible(true);
        $customizations = $flatten->invoke(null, $customizedDatas, 7, 0);

        self::assertCount(1, $customizations, 'the customization must be found even under the cart address bucket');
        self::assertSame('Hello', $customizations[0]['datas'][Product::CUSTOMIZE_TEXTFIELD][0]['value']);
    }

    public function testReturnsEmptyWhenTheLineHasNoCustomization(): void
    {
        self::bootKernel();

        $flatten = new ReflectionMethod(PaymentModule::class, 'flattenProductCustomizations');
        $flatten->setAccessible(true);

        self::assertSame([], $flatten->invoke(null, [], 7, 0));
    }
}
