<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\DataTransformer;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Form\DataTransformer\DefaultEmptyDataTransformer;

class DefaultEmptyDataTransformerTest extends TestCase
{
    /**
     * @dataProvider getTransformValues
     *
     * @param mixed $emptyData
     * @param mixed $inputValue
     * @param mixed $expectedValue
     */
    public function testTransform($emptyData, $inputValue, $expectedValue): void
    {
        $transformer = new DefaultEmptyDataTransformer($emptyData);
        $transformedValue = $transformer->transform($inputValue);
        // Assert the values are equal
        $this->assertEquals($expectedValue, $transformedValue);
        // Additional test for strict equality (even type)
        $this->assertTrue($expectedValue === $transformedValue);
    }

    /**
     * @dataProvider getTransformValues
     *
     * @param mixed $emptyData
     * @param mixed $inputValue
     * @param mixed $expectedValue
     */
    public function testReverseTransform($emptyData, $inputValue, $expectedValue): void
    {
        $transformer = new DefaultEmptyDataTransformer($emptyData);
        $reverseTransformedValue = $transformer->reverseTransform($inputValue);
        // Assert the values are equal
        $this->assertEquals($expectedValue, $reverseTransformedValue);
        // Additional test for strict equality (even type)
        $this->assertTrue($expectedValue === $reverseTransformedValue);
    }

    public function testViewEmptyData(): void
    {
        $transformer = new DefaultEmptyDataTransformer(0);
        $transformedValue = $transformer->transform(null);
        $this->assertEquals(0, $transformedValue);
        $this->assertTrue(0 === $transformedValue);

        $reverseTransformedValue = $transformer->reverseTransform(null);
        $this->assertEquals(0, $reverseTransformedValue);
        $this->assertTrue(0 === $reverseTransformedValue);

        // With extra view empty data now
        $transformer = new DefaultEmptyDataTransformer(0, null);
        $transformedValue = $transformer->transform(null);
        $this->assertEquals(null, $transformedValue);
        $this->assertTrue(null === $transformedValue);

        $reverseTransformedValue = $transformer->reverseTransform(null);
        $this->assertEquals(0, $reverseTransformedValue);
        $this->assertTrue(0 === $reverseTransformedValue);
    }

    public function getTransformValues()
    {
        yield [0, null, 0];
        yield [1, null, 1];
        yield ['0', null, '0'];
        yield ['plop', null, 'plop'];

        yield [0, '', 0];
        yield [1, '', 1];
        yield ['0', '', '0'];
        yield ['plop', '', 'plop'];

        yield [0, 0, 0];
        yield [1, 0, 1];
        yield ['0', 0, '0'];
        yield ['plop', 0, 'plop'];

        yield [0, '0', 0];
        yield [1, '0', 1];
        yield ['0', '0', '0'];
        yield ['plop', '0', 'plop'];

        yield [0, [], 0];
        yield [1, [], 1];
        yield ['0', [], '0'];
        yield ['plop', [], 'plop'];

        yield [0, 2, 2];
        yield [1, 2, 2];
        yield ['0', 2, 2];
        yield ['plop', 2, 2];

        yield [0, '1', '1'];
        yield [1, '1', '1'];
        yield ['0', '1', '1'];
        yield ['plop', '1', '1'];
    }
}
