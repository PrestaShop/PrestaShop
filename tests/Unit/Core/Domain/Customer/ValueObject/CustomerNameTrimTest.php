<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Customer\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;

/**
 * The back office customer form trims names before they reach the domain, so a customer created
 * there is stored without surrounding whitespace. Entry points that build the value objects
 * directly - the Admin API among them - have to end up with the same value.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/42132
 */
class CustomerNameTrimTest extends TestCase
{
    /**
     * @dataProvider provideNames
     */
    public function testFirstNameIsTrimmed(string $given, string $expected): void
    {
        $this->assertSame($expected, (new FirstName($given))->getValue());
    }

    /**
     * @dataProvider provideNames
     */
    public function testLastNameIsTrimmed(string $given, string $expected): void
    {
        $this->assertSame($expected, (new LastName($given))->getValue());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public function provideNames(): iterable
    {
        yield 'leading and trailing spaces' => ['  John  ', 'John'];
        yield 'leading spaces' => ['   John', 'John'];
        yield 'trailing spaces' => ['John   ', 'John'];
        yield 'tab and newline' => ["\tJohn\n", 'John'];
        yield 'inner spacing is preserved' => ['  Anne Marie  ', 'Anne Marie'];
        yield 'already clean value is untouched' => ['John', 'John'];
        yield 'accented characters survive' => ['  Zoé  ', 'Zoé'];
    }
}
