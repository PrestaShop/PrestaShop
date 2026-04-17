<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;

class VersionNumberTest extends TestCase
{
    public function testConstruct()
    {
        $number = new VersionNumber(1.7, 2, 3);

        $this->assertEquals(1.7, $number->getMajor());
        $this->assertEquals(2, $number->getMinor());
        $this->assertEquals(3, $number->getPatch());
    }

    public function testToString()
    {
        $number = new VersionNumber(1.6, 1, 21);

        $this->assertEquals('1.6.1.21', $number->__toString());
    }

    public function testFromStringGoodUsecases()
    {
        $this->assertEquals(
            '1.6.1.20',
            VersionNumber::fromString('1.6.1.20')->__toString()
        );
        $this->assertEquals(
            '1.7.0.0',
            VersionNumber::fromString('1.7.0.0')->__toString()
        );
        $this->assertEquals(
            '1.8.4.2',
            VersionNumber::fromString('1.8.4.2')->__toString()
        );
    }

    public function testFromStringBadUsecases()
    {
        $this->expectException(\InvalidArgumentException::class);
        $number = VersionNumber::fromString('1.2.3.4.5');

        $this->expectException(\InvalidArgumentException::class);
        $number = VersionNumber::fromString('1.2');

        $this->expectException(\InvalidArgumentException::class);
        $number = VersionNumber::fromString('a');

        $this->expectException(\InvalidArgumentException::class);
        $number = VersionNumber::fromString('1......89.2');

        $this->expectException(\InvalidArgumentException::class);
        $number = VersionNumber::fromString('1..7.5.0');

        $this->expectException(\InvalidArgumentException::class);
        $number = VersionNumber::fromString('17.18.19..20');

        $this->expectException(\InvalidArgumentException::class);
        $number = VersionNumber::fromString('17.18..19.29');
    }

    public function testCompareSameVersions()
    {
        $number1 = new VersionNumber(1.6, 2, 3);
        $number2 = new VersionNumber(1.6, 2, 3);

        $this->assertEquals(0, $number1->compare($number2));
    }

    public function testCompareHigher()
    {
        $number1 = new VersionNumber(1.7, 2, 3);
        $number2 = new VersionNumber(1.6, 2, 3);

        $this->assertEquals(1, $number1->compare($number2));

        $number3 = new VersionNumber(1.8, 2, 3);
        $number4 = new VersionNumber(1.8, 1, 3);

        $this->assertEquals(1, $number3->compare($number4));

        $number5 = new VersionNumber(1.8, 2, 3);
        $number6 = new VersionNumber(1.8, 2, 1);

        $this->assertEquals(1, $number5->compare($number6));
    }

    public function testCompareLower()
    {
        $number1 = new VersionNumber(1.5, 2, 3);
        $number2 = new VersionNumber(1.6, 2, 3);

        $this->assertEquals(-1, $number1->compare($number2));

        $number3 = new VersionNumber(1.8, 2, 3);
        $number4 = new VersionNumber(1.8, 5, 3);

        $this->assertEquals(-1, $number3->compare($number4));

        $number5 = new VersionNumber(1.8, 2, 3);
        $number6 = new VersionNumber(1.8, 2, 7);

        $this->assertEquals(-1, $number5->compare($number6));
    }
}
