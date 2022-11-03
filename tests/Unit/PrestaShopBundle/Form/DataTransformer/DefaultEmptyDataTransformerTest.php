<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
