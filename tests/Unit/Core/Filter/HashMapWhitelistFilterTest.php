<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Filter;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\LazyArrayAttribute;
use PrestaShop\PrestaShop\Core\Filter\HashMapWhitelistFilter;

class HashMapWhitelistFilterTest extends TestCase
{
    protected function setUp(): void
    {
        TestLazyArray::$countedPropertyCalls = 0;
    }

    /**
     * @param array $subject
     * @param array $whitelist
     * @param array $expectedResult
     *
     * @dataProvider provideTestCases
     */
    public function testItOnlyKeepsWhitelistedKeysWithoutLosingValues(array $subject, array $whitelist, array $expectedResult): void
    {
        $filter = new HashMapWhitelistFilter();
        $filter->whitelist($whitelist);

        $result = $filter->filter($subject);

        $this->assertSame($expectedResult, $result);
    }

    public function testKeysCanBeRemovedFromWhitelist(): void
    {
        $subject = [
            'foo' => 'something',
            'bar' => null,
            'baz' => [],
        ];

        $filter = new HashMapWhitelistFilter();
        $filter->whitelist([
            'foo', 'bar',
        ]);

        $expected = [
            'foo' => 'something',
            'bar' => null,
        ];

        $this->assertSame($expected, $filter->filter($subject));

        // remove 'foo' from whitelist and filter again
        $filter->removeFromWhitelist('foo');
        $expected = [
            'bar' => null,
        ];

        $this->assertSame($expected, $filter->filter($subject));
    }

    public function testKeysCanBeAddedToWhitelist(): void
    {
        $subject = [
            'foo' => 'something',
            'bar' => null,
            'baz' => [],
        ];

        $filter = new HashMapWhitelistFilter();
        $filter->whitelist([
            'foo',
        ]);

        $expected = [
            'foo' => 'something',
        ];

        $this->assertSame($expected, $filter->filter($subject));

        // add 'bar' to the whitelist and filter again
        $filter->whitelist(['bar']);
        $expected = [
            'foo' => 'something',
            'bar' => null,
        ];

        $this->assertSame($expected, $filter->filter($subject));
    }

    public function testFilteringLazyArrayReturnsNewInstanceOfSubject(): void
    {
        $subject = new TestLazyArray();

        $filter = new HashMapWhitelistFilter();
        $filter->whitelist([
            'some_property',
        ]);

        $filteredSubject = $filter->filter($subject);

        $this->assertNotSame($subject, $filteredSubject);
        $this->assertArrayNotHasKey(
            'some_other_property',
            $filteredSubject,
            'The filtered subject still has properties that should have been filtered out'
        );
        $this->assertArrayHasKey(
            'some_other_property',
            $subject,
            'The original subject has been altered'
        );
    }

    public function testFilteringLazyArrayComputesNoWhitelistedValue(): void
    {
        $filter = new HashMapWhitelistFilter();
        $filter->whitelist([
            'counted_property',
        ]);

        $filter->filter(new TestLazyArray());

        $this->assertSame(0, TestLazyArray::$countedPropertyCalls);
    }

    public function testValueComputedOnFilteredLazyArrayIsReusedByTheOriginal(): void
    {
        $subject = new TestLazyArray();

        $filter = new HashMapWhitelistFilter();
        $filter->whitelist([
            'counted_property',
        ]);

        $filteredSubject = $filter->filter($subject);

        $this->assertSame(1, $filteredSubject['counted_property']);
        $this->assertSame(1, $subject['counted_property']);
        $this->assertSame(1, TestLazyArray::$countedPropertyCalls);
    }

    public function testValueComputedOnTheOriginalIsReusedByFilteredLazyArray(): void
    {
        $subject = new TestLazyArray();

        $filter = new HashMapWhitelistFilter();
        $filter->whitelist([
            'counted_property',
        ]);

        $filteredSubject = $filter->filter($subject);

        $this->assertSame(1, $subject['counted_property']);
        $this->assertSame(1, $filteredSubject['counted_property']);
        $this->assertSame(1, TestLazyArray::$countedPropertyCalls);
    }

    public function testFilteredLazyArrayDoesNotSeeValuesOutsideTheWhitelist(): void
    {
        $subject = new TestLazyArray();
        $this->assertSame(1, $subject['counted_property']);

        $filter = new HashMapWhitelistFilter();
        $filter->whitelist([
            'some_property',
        ]);

        $filteredSubject = $filter->filter($subject);

        $this->assertArrayNotHasKey('counted_property', $filteredSubject);
        $this->assertSame([], $filteredSubject['counted_property']);
        $this->assertSame(['some_property' => 'some_property'], json_decode(json_encode($filteredSubject), true));
    }

    public function testNestedFilteringLazyArrayComputesNestedValuesOnceForBothInstances(): void
    {
        $nested = new TestLazyArray();
        $subject = new TestLazyArray();
        $subject->nested = $nested;

        $nestedFilter = new HashMapWhitelistFilter();
        $nestedFilter->whitelist([
            'counted_property',
        ]);
        $filter = new HashMapWhitelistFilter();
        $filter->whitelist([
            'nested_property' => $nestedFilter,
        ]);

        $filteredSubject = $filter->filter($subject);

        $this->assertNotSame($nested, $filteredSubject['nested_property']);
        $this->assertSame(1, $filteredSubject['nested_property']['counted_property']);
        $this->assertArrayNotHasKey('some_property', $filteredSubject['nested_property']);
        $this->assertSame($nested, $subject['nested_property']);
        $this->assertArrayHasKey('some_property', $nested);
        $this->assertSame(1, $nested['counted_property']);
        $this->assertSame(1, TestLazyArray::$countedPropertyCalls);
    }

    public function provideTestCases(): array
    {
        $basicArray = [
            'foo' => 'something',
            'bar' => null,
            'baz' => [],
        ];

        $nestedArray = [
            'foo' => 'something',
            'bar' => null,
            'baz' => $basicArray,
        ];

        return [
            'keep 1st' => [
                'subject' => $basicArray,
                'whitelist' => [
                    'foo',
                ],
                'expected' => [
                    'foo' => 'something',
                ],
            ],
            'keep 2nd' => [
                'subject' => $basicArray,
                'whitelist' => [
                    'bar',
                ],
                'expected' => [
                    'bar' => null,
                ],
            ],
            'keep 3rd' => [
                'subject' => $basicArray,
                'whitelist' => [
                    'baz',
                ],
                'expected' => [
                    'baz' => [],
                ],
            ],
            'keep 1st and 2nd' => [
                'subject' => $basicArray,
                'whitelist' => [
                    'foo', 'bar',
                ],
                'expected' => [
                    'foo' => 'something',
                    'bar' => null,
                ],
            ],
            'keep all' => [
                'subject' => $basicArray,
                'whitelist' => [
                    'foo', 'bar', 'baz',
                ],
                'expected' => [
                    'foo' => 'something',
                    'bar' => null,
                    'baz' => [],
                ],
            ],
            'keep none' => [
                'subject' => $basicArray,
                'whitelist' => [],
                'expected' => [],
            ],
            'nested filter' => [
                'subject' => $nestedArray,
                'whitelist' => [
                    'foo',
                    'baz' => (new HashMapWhitelistFilter())->whitelist(['foo', 'baz']),
                ],
                'expected' => [
                    'foo' => 'something',
                    'baz' => [
                        'foo' => 'something',
                        'baz' => [],
                    ],
                ],
            ],
        ];
    }
}

class TestLazyArray extends AbstractLazyArray
{
    public static int $countedPropertyCalls = 0;

    public ?TestLazyArray $nested = null;

    #[LazyArrayAttribute(arrayAccess: true)]
    public function getCountedProperty(): int
    {
        return ++self::$countedPropertyCalls;
    }

    #[LazyArrayAttribute(arrayAccess: true)]
    public function getNestedProperty(): ?TestLazyArray
    {
        return $this->nested;
    }

    #[LazyArrayAttribute(arrayAccess: true)]
    public function getSomeProperty()
    {
        return 'some_property';
    }

    #[LazyArrayAttribute(arrayAccess: true)]
    public function getSomeOtherProperty()
    {
        return 'some_other_property';
    }
}
