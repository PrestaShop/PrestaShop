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

namespace Tests\Unit\Core\Util\Number;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractor;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractorException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class NumberExtractorTest extends TestCase
{
    /** @var NumberExtractor */
    private $numberExtractor;

    public function setUp(): void
    {
        parent::setUp();
        $this->numberExtractor = new NumberExtractor(PropertyAccess::createPropertyAccessor());
    }

    /**
     * @dataProvider getValidData
     *
     * @param array|FakeClass $resource
     * @param string $path
     * @param DecimalNumber $expectedResult
     */
    public function testItExtractsNumberFromArrayOrObject($resource, string $path, DecimalNumber $expectedResult)
    {
        $actualResult = $this->numberExtractor->extract($resource, $path);

        $this->assertTrue($expectedResult->equals($actualResult));
    }

    /**
     * @dataProvider getDataWithInvalidResourcePropertyType
     *
     * @param array|FakeClass3 $resource
     * @param string $path
     */
    public function testItThrowsExceptionWhenNonNumericValueIsProvidedInAccessedProperty($resource, string $path)
    {
        $this->expectException(NumberExtractorException::class);
        $this->expectExceptionCode(NumberExtractorException::NON_NUMERIC_PROPERTY);

        $this->numberExtractor->extract($resource, $path);
    }

    /**
     * @return Generator
     */
    public function getValidData(): Generator
    {
        $fakeClass = new FakeClass();

        yield [
            ['test' => 15],
            '[test]',
            new DecimalNumber('15'),
        ];
        yield [
            ['test' => ['hello' => '13']],
            '[test][hello]',
            new DecimalNumber('13'),
        ];
        yield [
            ['test' => ['hello' => [1 => 17.3]]],
            '[test][hello][1]',
            new DecimalNumber('17.3'),
        ];
        yield [
            $fakeClass,
            'test',
            new DecimalNumber('17'),
        ];
        yield [
            $fakeClass,
            'obj2.test2',
            new DecimalNumber('19.5'),
        ];
    }

    /**
     * @return Generator
     */
    public function getDataWithInvalidResourcePropertyType(): Generator
    {
        $obj = new FakeClass3();

        yield [
            ['test' => 'this is not a numeric value'],
            '[test]',
        ];
        yield [
            $obj,
            'notNumeric',
        ];
    }
}

class FakeClass
{
    public $test = 17;

    private $obj2;

    private $obj3;

    public function __construct()
    {
        $this->obj2 = new FakeClass2();
        $this->obj3 = new FakeClass3();
    }

    /**
     * @return int return different number than $test property to allow checking if public property has priority against public getter
     */
    public function getTest()
    {
        return 50;
    }

    /**
     * @return FakeClass2
     */
    public function getObj2(): FakeClass2
    {
        return $this->obj2;
    }

    /**
     * @return FakeClass3
     */
    public function getObj3(): FakeClass3
    {
        return $this->obj3;
    }
}

class FakeClass2
{
    private $test2 = 19.5;

    public function getTest2(): float
    {
        return $this->test2;
    }
}

class FakeClass3
{
    private $test3 = '300.03';

    private $notNumeric = 'this string is not numeric';

    public function getTest3(): string
    {
        return $this->test3;
    }

    public function getNotNumeric(): string
    {
        return $this->notNumeric;
    }
}
