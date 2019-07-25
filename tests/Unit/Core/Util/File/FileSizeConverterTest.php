<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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

    public function testConvertZeorByte()
    {
        $result = $this->converter->convert(0);

        $this->assertEquals('0b', $result);
    }

    public function testConvertByte()
    {
        $result = $this->converter->convert(1);

        $this->assertEquals('1b', $result);
    }

    public function testConvertKilobyte()
    {
        $result = $this->converter->convert(1024);

        $this->assertEquals('1.00k', $result);
    }

    public function testConvertMegabyte()
    {
        $result = $this->converter->convert(1048576);

        $this->assertEquals('1.00M', $result);
    }

    public function testConvertGigabyte()
    {
        $result = $this->converter->convert(1073741824);

        $this->assertEquals('1.00GB', $result);
    }
}
