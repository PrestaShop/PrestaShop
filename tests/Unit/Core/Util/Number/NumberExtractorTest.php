<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Util\Number;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractor;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractorException;
use stdClass;
use Symfony\Component\PropertyAccess\PropertyAccess;

class NumberExtractorTest extends TestCase
{
    /**
     * @var NumberExtractor
     */
    private $numberExtractor;

    public function setUp()
    {
        parent::setUp();
        $this->numberExtractor = new NumberExtractor(PropertyAccess::createPropertyAccessor());
    }

    /**
     * @dataProvider getValidData
     *
     * @param $resource
     * @param $path
     * @param $expectedResult
     */
    public function testItExtractsNumberFromArrayOrObject($resource, string $path, Number $expectedResult)
    {
        $actualResult = $this->numberExtractor->extract($resource, $path);

        $this->assertTrue($expectedResult->equals($actualResult));
    }

    /**
     * @dataProvider getDataWithInvalidResourcePropertyType
     *
     * @param $resource
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
        $obj = new stdClass();
        $obj->test = 17;

        $obj2 = new stdClass();
        $obj2->test2 = 19.5;

        $obj3 = new stdClass();
        $obj3->test3 = '300.03';

        $obj->obj2 = $obj2;
        $obj->obj3 = $obj3;

        yield [
            ['test' => 15],
            '[test]',
            new Number('15'),
        ];
        yield [
            ['test' => ['hello' => '13']],
            '[test][hello]',
            new Number('13'),
        ];
        yield [
            ['test' => ['hello' => [1 => 17.3]]],
            '[test][hello][1]',
            new Number('17.3'),
        ];
        yield [
            $obj,
            'test',
            new Number('17'),
        ];
        yield [
            $obj,
            'obj2.test2',
            new Number('19.5'),
        ];
    }

    /**
     * @return Generator
     */
    public function getDataWithInvalidResourcePropertyType(): Generator
    {
        $obj = new stdClass();
        $obj->test = 'this is not a numeric value';
        $obj->test2 = null;

        yield [
            ['test' => 'this is not a numeric value'],
            '[test]',
        ];
        yield [
            $obj,
            'test',
        ];
        yield [
            $obj,
            'test2',
        ];
    }
}
