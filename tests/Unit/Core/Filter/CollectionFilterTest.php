<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Filter;

use PrestaShop\PrestaShop\Core\Filter\CollectionFilter;
use PrestaShop\PrestaShop\Core\Filter\HashMapWhitelistFilter;

class CollectionFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param array $subject
     * @param array $queue
     * @param array $expectedResult
     *
     * @dataProvider provideTestCases
     *
     * @throws \PrestaShop\PrestaShop\Core\Filter\FilterException
     */
    public function testItProcessesAllItems(array $subject, array $queue, array $expectedResult): void
    {
        $filter = new CollectionFilter();
        $filter->queue($queue);

        $result = $filter->filter($subject);

        $this->assertSame($expectedResult, $result);
    }

    public function provideTestCases(): array
    {
        $subject = [
            [
                'foo' => 'something',
                'bar' => null,
                'baz' => [],
            ],
            [
                'foo' => 'something',
            ],
            [
                'bar' => null,
                'baz' => [],
            ],
            [],
        ];

        return [
            [
                'subject' => $subject,
                'queue' => [
                    (new HashMapWhitelistFilter())
                        ->whitelist(['foo']),
                ],
                'expectedResult' => [
                    [
                        'foo' => 'something',
                    ],
                    [
                        'foo' => 'something',
                    ],
                    [],
                    [],
                ],
            ],
        ];
    }
}
