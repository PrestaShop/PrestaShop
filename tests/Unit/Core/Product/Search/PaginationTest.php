<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Product\Search;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Product\Search\Pagination;

/**
 * @doc ./vendor/bin/phpunit -c tests/phpunit.xml --filter="PaginationTest"
 */
class PaginationTest extends TestCase
{
    /**
     * @var Pagination
     */
    private $pagination;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->pagination = new Pagination();
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->pagination = null;
    }

    public function testCreateANewPagination()
    {
        // Pagination public integrity of data types.
        $this->assertInternalType('integer', $this->pagination->getPagesCount());
        $this->assertInternalType('integer', $this->pagination->getPage());
        $this->assertInternalType('array', $this->pagination->buildLinks());

        // Pagination public integrity of default Pagination data
        $this->assertSame(0, $this->pagination->getPagesCount());
        $this->assertSame(0, $this->pagination->getPage());
        $this->assertSame(
            [
                ['type' => 'previous', 'page' => 1, 'clickable' => true, 'current' => false],
                ['type' => 'next', 'page' => 0, 'clickable' => false, 'current' => false]
            ],
            $this->pagination->buildLinks()
        );
    }

    public function testGetterAndSetterPage()
    {
        $this->assertInstanceOf(Pagination::class, $this->pagination->setPage(3));
        $this->assertSame(3, $this->pagination->getPage());
    }

    public function testGetterAndSetterPagesCount()
    {
        $this->assertInstanceOf(Pagination::class, $this->pagination->setPagesCount(7));
        $this->assertSame(7, $this->pagination->getPagesCount());
    }

    public function testPaginationAddsContextFirstAndLastPageAndPreviousNext()
    {
        $this->pagination
            ->setPagesCount(10)
            ->setPage(5)
        ;

        $this->assertEquals([
            ['type' => 'previous', 'clickable' => true, 'page' => 4, 'current' => false],
            ['type' => 'page', 'clickable' => true, 'page' => 1, 'current' => false],
            ['type' => 'spacer', 'clickable' => false, 'page' => null, 'current' => false],
            ['type' => 'page', 'clickable' => true, 'page' => 4, 'current' => false],
            ['type' => 'page', 'clickable' => false, 'page' => 5, 'current' => true ],
            ['type' => 'page', 'clickable' => true, 'page' => 6, 'current' => false],
            ['type' => 'spacer', 'clickable' => false, 'page' => null, 'current' => false],
            ['type' => 'page', 'clickable' => true, 'page' => 10, 'current' => false],
            ['type' => 'next', 'clickable' => true, 'page' => 6, 'current' => false],
        ], $this->pagination->buildLinks());
    }

    public function testPaginationWhenOnFirstPage()
    {
        $this->pagination
            ->setPagesCount(10)
            ->setPage(1)
        ;

        $this->assertEquals([
            ['type' => 'previous', 'clickable' => false, 'page' => 1, 'current' => false],
            ['type' => 'page', 'clickable' => false, 'page' => 1, 'current' => true],
            ['type' => 'page', 'clickable' => true, 'page' => 2, 'current' => false],
            ['type' => 'page', 'clickable' => true, 'page' => 3, 'current' => false],
            ['type' => 'spacer', 'clickable' => false, 'page' => null, 'current' => false],
            ['type' => 'page', 'clickable' => true, 'page' => 10, 'current' => false],
            ['type' => 'next', 'clickable' => true, 'page' => 2, 'current' => false],
        ], $this->pagination->buildLinks());
    }

    public function testPaginationWhenOnLastPage()
    {
        $this->pagination
            ->setPagesCount(10)
            ->setPage(10)
        ;

        $this->assertEquals([
            ['type' => 'previous', 'clickable' => true, 'page' => 9, 'current' => false],
            ['type' => 'page', 'clickable' => true, 'page' => 1, 'current' => false],
            ['type' => 'spacer', 'clickable' => false, 'page' => null, 'current' => false],
            ['type' => 'page', 'clickable' => true, 'page' => 8, 'current' => false],
            ['type' => 'page', 'clickable' => true, 'page' => 9, 'current' => false],
            ['type' => 'page', 'clickable' => false, 'page' => 10, 'current' => true],
            ['type' => 'next', 'clickable' => false, 'page' => 10, 'current' => false],
        ], $this->pagination->buildLinks());
    }

    public function testPaginationMakesSenseWhenOnlyOnePage()
    {
        $this->pagination
            ->setPagesCount(1)
            ->setPage(1)
        ;

        $this->assertEquals([
            ['type' => 'previous', 'clickable' => false, 'page' => 1, 'current' => false],
            ['type' => 'page', 'clickable' => false, 'page' => 1, 'current' => true],
            ['type' => 'next', 'clickable' => false, 'page' => 1, 'current' => false],
        ], $this->pagination->buildLinks());
    }
}