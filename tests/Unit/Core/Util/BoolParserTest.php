<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Util;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\BoolParser;

class BoolParserTest extends TestCase
{
    /**
     * @throws AssertionFailedError
     */
    public function testCastToBool()
    {
        $this->assertFalse(BoolParser::castToBool('false'));
        $this->assertFalse(BoolParser::castToBool('0'));
        $this->assertFalse(BoolParser::castToBool(0));
        $this->assertFalse(BoolParser::castToBool(false));
        $this->assertFalse(BoolParser::castToBool(-0));

        $this->assertTrue(BoolParser::castToBool('1'));
        $this->assertTrue(BoolParser::castToBool('-1'));
        $this->assertTrue(BoolParser::castToBool(1));
        $this->assertTrue(BoolParser::castToBool(-1));
        $this->assertTrue(BoolParser::castToBool('true'));
        $this->assertTrue(BoolParser::castToBool(true));
        $this->assertTrue(BoolParser::castToBool('anything else'));
        $this->assertTrue(BoolParser::castToBool('on'));
        // this case is open for discussion - not sure if it matters at all
        $this->assertTrue(BoolParser::castToBool('-0'));
    }
}
