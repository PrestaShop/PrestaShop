<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Util\File;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\File\FileSizeConverter;

class FileSizeConverterTest extends TestCase
{
    /**
     * @var FileSizeConverter
     */
    protected $converter;

    protected function setUp(): void
    {
        $this->converter = new FileSizeConverter();
    }

    /**
     * @dataProvider sizeDataProvider
     *
     * @param int $bytes
     * @param string $expectedResult
     */
    public function testConvert(int $bytes, string $expectedResult)
    {
        $result = $this->converter->convert($bytes);

        $this->assertEquals($expectedResult, $result);
    }

    public function sizeDataProvider(): array
    {
        return [
            [0, '0B'],
            [1, '1B'],
            [1024, '1.00kB'],
            [1048576, '1.00MB'],
            [1073741824, '1.00GB'],
            [-0, '0B'],
            [-123456789123, '0B'],
            [-15521, '0B'],
            [1651361681351684, '1501.90TB'],
            [21321635843254, '19.39TB'],
            [2132112, '2.03MB'],
            [5954, '5.81kB'],
        ];
    }
}
