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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
