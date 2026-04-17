<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\Customer\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\ApeCode;

class ApeCodeTest extends TestCase
{
    /**
     * @dataProvider getValidApeCodes
     */
    public function testItCreatesApeCodeWithValidValue($code)
    {
        $apeCode = new ApeCode($code);

        $this->assertEquals($code, $apeCode->getValue());
    }

    /**
     * @dataProvider getInvalidApeCodes
     */
    public function testItThrowExceptionWhenCreatingApeCodeWithInvalidValue($code)
    {
        $this->expectException(CustomerConstraintException::class);
        $this->expectExceptionCode(CustomerConstraintException::INVALID_APE_CODE);

        new ApeCode($code);
    }

    public function getValidApeCodes()
    {
        yield [''];
        yield ['001A'];
        yield ['1039B'];
    }

    public function getInvalidApeCodes()
    {
        yield ['not_valid'];
        yield ['1236'];
        yield [123];
        yield [[]];
    }
}
