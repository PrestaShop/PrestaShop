<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Shop\Query;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\SearchShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\SearchShops;

class SearchShopsTest extends TestCase
{
    /**
     * @dataProvider validSearchTermProvider
     */
    public function testItAcceptsAValidSearchTerm(string $searchTerm): void
    {
        $query = new SearchShops($searchTerm);

        self::assertSame($searchTerm, $query->getSearchTerm());
    }

    public function validSearchTermProvider(): iterable
    {
        yield 'zero' => ['0'];
        yield 'word' => ['Presta'];
        yield 'zero inside a word' => ['Shop 10'];
    }

    /**
     * @dataProvider emptySearchTermProvider
     */
    public function testItRejectsAnEmptySearchTerm(string $searchTerm): void
    {
        $this->expectException(SearchShopException::class);

        new SearchShops($searchTerm);
    }

    public function emptySearchTermProvider(): iterable
    {
        yield 'empty string' => [''];
        yield 'spaces' => ['   '];
    }
}
