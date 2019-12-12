<?php

namespace Tests\Unit\PrestaShopBundle\Utils;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_AssertionFailedError;
use PrestaShopBundle\Utils\BoolParser;

class BoolParserTest extends TestCase
{
    /**
     * @throws PHPUnit_Framework_AssertionFailedError
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
        // this case is open for discussion - not sure if it matters at all
        $this->assertTrue(BoolParser::castToBool('-0'));
    }
}
