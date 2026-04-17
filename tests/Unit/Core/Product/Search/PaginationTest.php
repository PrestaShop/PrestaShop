<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Product\Search;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Product\Search\Pagination;

class PaginationTest extends TestCase
{
    /**
     * @var Pagination
     */
    private $pagination;

    protected function setUp(): void
    {
        $this->pagination = new Pagination();
    }

    public function testPaginationAddsContextFirstAndLastPageAndPreviousNext()
    {
        $this->pagination
            ->setPagesCount(10)
            ->setPage(5);

        $this->assertEquals([
            ['type' => 'previous', 'clickable' => true, 'page' => 4,      'current' => false],
            ['type' => 'page', 'clickable' => true, 'page' => 1,      'current' => false],
            ['type' => 'spacer', 'clickable' => false, 'page' => null,   'current' => false],
            ['type' => 'page', 'clickable' => true, 'page' => 4,      'current' => false],
            ['type' => 'page', 'clickable' => false, 'page' => 5,      'current' => true],
            ['type' => 'page', 'clickable' => true, 'page' => 6,      'current' => false],
            ['type' => 'spacer', 'clickable' => false, 'page' => null,   'current' => false],
            ['type' => 'page', 'clickable' => true, 'page' => 10,     'current' => false],
            ['type' => 'next', 'clickable' => true, 'page' => 6,      'current' => false],
        ], $this->pagination->buildLinks());
    }

    public function testPaginationContextWhenOnFirstPage()
    {
        $this->pagination
            ->setPagesCount(10)
            ->setPage(1);

        $this->assertEquals([
            ['type' => 'previous', 'clickable' => false, 'page' => 1,      'current' => false],
            ['type' => 'page', 'clickable' => false, 'page' => 1,      'current' => true],
            ['type' => 'page', 'clickable' => true, 'page' => 2,      'current' => false],
            ['type' => 'page', 'clickable' => true, 'page' => 3,      'current' => false],
            ['type' => 'spacer', 'clickable' => false, 'page' => null,   'current' => false],
            ['type' => 'page', 'clickable' => true, 'page' => 10,     'current' => false],
            ['type' => 'next', 'clickable' => true, 'page' => 2,      'current' => false],
        ], $this->pagination->buildLinks());
    }

    public function testPaginationContextWhenOnLastPage()
    {
        $this->pagination
            ->setPagesCount(10)
            ->setPage(10);

        $this->assertEquals([
            ['type' => 'previous', 'clickable' => true, 'page' => 9,     'current' => false],
            ['type' => 'page', 'clickable' => true, 'page' => 1,     'current' => false],
            ['type' => 'spacer', 'clickable' => false, 'page' => null,  'current' => false],
            ['type' => 'page', 'clickable' => true, 'page' => 8,     'current' => false],
            ['type' => 'page', 'clickable' => true, 'page' => 9,     'current' => false],
            ['type' => 'page', 'clickable' => false, 'page' => 10,    'current' => true],
            ['type' => 'next', 'clickable' => false, 'page' => 10,    'current' => false],
        ], $this->pagination->buildLinks());
    }

    public function testPaginationContextMakesSenseWhenOnlyOnePage()
    {
        $this->pagination
            ->setPagesCount(1)
            ->setPage(1);

        $this->assertEquals([
            ['type' => 'previous', 'clickable' => false, 'page' => 1,     'current' => false],
            ['type' => 'page', 'clickable' => false, 'page' => 1,     'current' => true],
            ['type' => 'next', 'clickable' => false, 'page' => 1,     'current' => false],
        ], $this->pagination->buildLinks());
    }
}
