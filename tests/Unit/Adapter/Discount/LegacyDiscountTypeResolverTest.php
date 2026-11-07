<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Discount;

use CartRule;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Discount\LegacyDiscountTypeResolver;
use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;

class LegacyDiscountTypeResolverTest extends TestCase
{
    /**
     * @dataProvider provideLegacyCartRules
     */
    public function testResolveType(array $properties, string $expectedType, array $expectedUnsupportedSettings): void
    {
        $resolver = new LegacyDiscountTypeResolver();
        $cartRule = $this->buildCartRule($properties);

        $type = $resolver->resolveType($cartRule);

        $this->assertSame($expectedType, $type);
        $this->assertSame($expectedUnsupportedSettings, $resolver->getUnsupportedSettings($cartRule, $type));
    }

    public static function provideLegacyCartRules(): iterable
    {
        yield 'percent on the whole cart' => [
            ['reduction_percent' => '10.00'],
            DiscountType::CART_LEVEL,
            [],
        ];
        yield 'amount on the whole cart' => [
            ['reduction_amount' => '5.000000'],
            DiscountType::CART_LEVEL,
            [],
        ];
        yield 'percent on a single product' => [
            ['reduction_percent' => '10.00', 'reduction_product' => 42],
            DiscountType::PRODUCT_LEVEL,
            [],
        ];
        yield 'percent on the cheapest product' => [
            ['reduction_percent' => '10.00', 'reduction_product' => DiscountSettings::CHEAPEST_PRODUCT],
            DiscountType::PRODUCT_LEVEL,
            [LegacyDiscountTypeResolver::UNSUPPORTED_CHEAPEST_PRODUCT],
        ];
        yield 'amount on a product selection' => [
            ['reduction_amount' => '5.000000', 'reduction_product' => DiscountSettings::PRODUCT_SEGMENT],
            DiscountType::PRODUCT_LEVEL,
            [LegacyDiscountTypeResolver::UNSUPPORTED_PRODUCT_SEGMENT],
        ];
        yield 'free gift' => [
            ['gift_product' => 12],
            DiscountType::FREE_GIFT,
            [],
        ];
        yield 'free shipping' => [
            ['free_shipping' => 1],
            DiscountType::FREE_SHIPPING,
            [],
        ];
        yield 'percent with free shipping and free gift' => [
            ['reduction_percent' => '10.00', 'free_shipping' => 1, 'gift_product' => 12],
            DiscountType::CART_LEVEL,
            [LegacyDiscountTypeResolver::UNSUPPORTED_FREE_SHIPPING, LegacyDiscountTypeResolver::UNSUPPORTED_FREE_GIFT],
        ];
        yield 'free gift with free shipping' => [
            ['gift_product' => 12, 'free_shipping' => 1],
            DiscountType::FREE_GIFT,
            [LegacyDiscountTypeResolver::UNSUPPORTED_FREE_SHIPPING],
        ];
        yield 'percent restricted with specific cart rules' => [
            ['reduction_percent' => '10.00', 'cart_rule_restriction' => 1],
            DiscountType::CART_LEVEL,
            [LegacyDiscountTypeResolver::UNSUPPORTED_CART_RULE_RESTRICTION],
        ];
        yield 'no action at all' => [
            [],
            DiscountType::CART_LEVEL,
            [],
        ];
    }

    private function buildCartRule(array $properties): CartRule
    {
        $cartRule = $this->getMockBuilder(CartRule::class)
            ->disableOriginalConstructor()
            ->getMock();

        $defaults = [
            'reduction_percent' => '0.00',
            'reduction_amount' => '0.000000',
            'reduction_product' => 0,
            'gift_product' => 0,
            'free_shipping' => 0,
            'cart_rule_restriction' => 0,
        ];
        foreach (array_merge($defaults, $properties) as $property => $value) {
            $cartRule->{$property} = $value;
        }

        return $cartRule;
    }
}
