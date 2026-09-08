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
     * A language whose ISO code PrestaShop does not ship has no locale to look up, and the tag is
     * the only thing left that says which one the merchant meant.
     *
     * @dataProvider getTagIETFAndExpectedLocale
     */
    public function testItDerivesTheLocaleFromTheTag(string $tagIETF, string $expectedLocale)
    {
        $this->assertSame($expectedLocale, (new TagIETF($tagIETF))->toLocale());
    }

    public function getTagIETFAndExpectedLocale()
    {
        // A tag carrying a region simply gets the casing Validate::isLocale() asks for.
        yield 'the reported case' => ['de-ch', 'de-CH'];
        yield 'already cased' => ['lt-LT', 'lt-LT'];
        yield 'upper language' => ['EN-gb', 'en-GB'];
        // Without a region there is none to take, so the language stands in for it - which is the
        // shape PrestaShop's own list already stores for such a language (fr is fr-FR).
        yield 'no region' => ['fr', 'fr-FR'];
        yield 'no region, upper' => ['DE', 'de-DE'];
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
