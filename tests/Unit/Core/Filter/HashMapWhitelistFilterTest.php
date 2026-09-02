<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Filter;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use PrestaShop\PrestaShop\Core\Filter\HashMapWhitelistFilter;

class HashMapWhitelistFilterTest extends TestCase
{
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
    /**
     * @arrayAccess
     */
    public function getSomeProperty()
    {
        return 'some_property';
    }

    /**
     * @arrayAccess
     */
    public function getSomeOtherProperty()
    {
        return 'some_other_property';
    }
}
