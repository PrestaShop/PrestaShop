<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Util\String;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\String\ModifyAllShopsUtil;

class ModifyAllShopsUtilTest extends TestCase
{
    /**
     * @dataProvider getTestPrefixFieldPathData
     *
     * @param string $fieldPath
     * @param string $prefix
     * @param string $expectedResult
     */
    public function testPrefixFieldPath(string $fieldPath, string $prefix, string $expectedResult): void
    {
        self::assertSame($expectedResult, ModifyAllShopsUtil::prefixFieldPathWithAllShops($fieldPath, $prefix));
    }

    public function getTestPrefixFieldPathData(): iterable
    {
        yield [
            '',
            'what',
            '',
        ];

        yield [
            '[foo][bar]',
            'hello',
            '[foo][hellobar]',
        ];

        yield [
            '[stock][delta_quantity][delta]',
            'modify_all_shops_',
            '[stock][delta_quantity][modify_all_shops_delta]',
        ];
    }
}
