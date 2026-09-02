<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Util;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\ArabicToLatinDigitConverter;

class ArabicToLatinDigitConverterTest extends TestCase
{
    public function testConvertFromArabicToLatin()
    {
        $converter = new ArabicToLatinDigitConverter();
        $this->assertEquals($converter->convert('٨٤'), '84');
    }

    public function testConvertFromLatinToArabic()
    {
        $converter = new ArabicToLatinDigitConverter();
        $this->assertEquals($converter->reverseConvert('84'), '٨٤');
    }
}
