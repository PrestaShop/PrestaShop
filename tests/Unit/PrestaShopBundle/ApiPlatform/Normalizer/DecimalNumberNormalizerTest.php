<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\ApiPlatform\Normalizer;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShopBundle\ApiPlatform\Normalizer\DecimalNumberNormalizer;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

class DecimalNumberNormalizerTest extends TestCase
{
    /**
     * @dataProvider numericValueProvider
     */
    public function testNumericValuesAreDenormalized(mixed $data, string $expected): void
    {
        $decimal = (new DecimalNumberNormalizer())->denormalize($data, DecimalNumber::class);

        $this->assertInstanceOf(DecimalNumber::class, $decimal);
        $this->assertSame($expected, (string) $decimal);
    }

    /**
     * @return iterable<string, array{mixed, string}>
     */
    public static function numericValueProvider(): iterable
    {
        yield 'numeric string' => ['12.87', '12.87'];
        yield 'int' => [10, '10'];
        yield 'float' => [1.5, '1.5'];
        yield 'negative' => ['-0.25', '-0.25'];
        yield 'already a decimal' => [new DecimalNumber('3.14'), '3.14'];
    }

    /**
     * @dataProvider nonNumericValueProvider
     */
    public function testNonNumericValuesRaiseTheSerializerException(mixed $data): void
    {
        $this->expectException(NotNormalizableValueException::class);
        $this->expectExceptionMessage('cannot be interpreted as a number');

        (new DecimalNumberNormalizer())->denormalize($data, DecimalNumber::class, null, ['deserialization_path' => 'price']);
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function nonNumericValueProvider(): iterable
    {
        yield 'text' => ['n/a'];
        yield 'bool' => [true];
        yield 'array' => [['1']];
        yield 'null' => [null];
    }

    public function testTheExceptionCarriesTheExpectedTypeAndPath(): void
    {
        try {
            (new DecimalNumberNormalizer())->denormalize('abc', DecimalNumber::class, null, ['deserialization_path' => 'price']);
            $this->fail('Expected a NotNormalizableValueException');
        } catch (NotNormalizableValueException $e) {
            $this->assertSame(['number'], $e->getExpectedTypes());
            $this->assertSame('price', $e->getPath());
            $this->assertSame('string', $e->getCurrentType());
            $this->assertTrue($e->canUseMessageForUser());
        }
    }

    public function testNormalizeReturnsAFloat(): void
    {
        $this->assertSame(12.87, (new DecimalNumberNormalizer())->normalize(new DecimalNumber('12.87')));
    }
}
