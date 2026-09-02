<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\Language\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\IsoCode;

class IsoCodeTest extends TestCase
{
    /**
     * @dataProvider getValidTwoLetterIsoCodes
     */
    public function testIsoCodeCanBeCreatedWithValidTwoLetterIsoCode($twoLetterIsoCode, $expectedIsoCodeValue)
    {
        $isoCode = new IsoCode($twoLetterIsoCode);

        $this->assertEquals($expectedIsoCodeValue, $isoCode->getValue());
    }

    /**
     * @dataProvider getInvalidCodes
     */
    public function testIsoCodeCannotBeCreatedWithInvalidValue($invalidIsoCode)
    {
        $this->expectException(LanguageConstraintException::class);

        new IsoCode($invalidIsoCode);
    }

    public function getValidTwoLetterIsoCodes()
    {
        yield ['lt', 'lt'];
        yield ['fr', 'fr'];
        yield ['GB', 'gb'];
        yield ['SW', 'sw'];
    }

    public function getInvalidCodes()
    {
        yield [''];
        yield ['12'];
        yield [23];
        yield ['?!'];
        yield [null];
    }
}
