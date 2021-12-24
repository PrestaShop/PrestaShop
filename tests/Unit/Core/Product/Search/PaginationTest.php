<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Product\Search;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Product\Search\Pagination;

class PaginationTest extends Testcase
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
