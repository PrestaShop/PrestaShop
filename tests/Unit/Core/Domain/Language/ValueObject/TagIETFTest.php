<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\Language\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\TagIETF;

class TagIETFTest extends TestCase
{
    /**
     * @dataProvider getValidTagIETFValues
     */
    public function testTagIETFCanBeCreatedWithValidValues($validTagIETFValue)
    {
        $tagIETF = new TagIETF($validTagIETFValue);

        $this->assertEquals($validTagIETFValue, $tagIETF->getValue());
    }

    /**
     * @dataProvider getInvalidTagIETFValues
     */
    public function testTagIETFCanBeCreatedWithInvalidValues($invalidTagIETFValue)
    {
        $this->expectException(LanguageConstraintException::class);

        new TagIETF($invalidTagIETFValue);
    }

    /**
     * @dataProvider getTagIETFAndExpectedLocale
     */
    public function testItDerivesTheLocaleFromTheTag(string $tagIETF, ?string $expectedLocale): void
    {
        $this->assertSame($expectedLocale, (new TagIETF($tagIETF))->toLocale());
    }

    public static function getTagIETFAndExpectedLocale(): iterable
    {
        yield 'lower region' => ['de-ch', 'de-CH'];
        yield 'already cased' => ['lt-LT', 'lt-LT'];
        yield 'upper language' => ['EN-gb', 'en-GB'];
        yield 'no region' => ['fr', null];
        yield 'no region, upper' => ['DE', null];
        yield 'no region, no CLDR locale from repetition' => ['en', null];
    }

    public function getValidTagIETFValues()
    {
        yield ['fr'];
        yield ['lt-LT'];
        yield ['en-us'];
        yield ['EN-gb'];
        yield ['EN-AU'];
    }

    public function getInvalidTagIETFValues()
    {
        yield ['enUS'];
        yield ['ENGB'];
        yield [1234];
        yield [null];
    }
}
