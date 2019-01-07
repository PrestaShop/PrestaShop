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

class PaginationTest extends Testcase
{
    public function setUp()
    {
        $this->pagination = new Pagination;
    }

    public function test_pagination_adds_context_first_and_last_page_and_previous_next()
    {
        $this->pagination
            ->setPagesCount(10)
            ->setPage(5)
        ;

        $this->assertEquals([
            ['type' => 'previous', 'clickable' => true, 'page' => 4,      'current' => false],
            ['type' => 'page', 'clickable' => true, 'page' => 1,      'current' => false],
            ['type' => 'spacer', 'clickable' => false, 'page' => null,   'current' => false],
            ['type' => 'page', 'clickable' => true, 'page' => 4,      'current' => false],
            ['type' => 'page', 'clickable' => false, 'page' => 5,      'current' => true ],
            ['type' => 'page', 'clickable' => true, 'page' => 6,      'current' => false],
            ['type' => 'spacer', 'clickable' => false, 'page' => null,   'current' => false],
            ['type' => 'page', 'clickable' => true, 'page' => 10,     'current' => false],
            ['type' => 'next', 'clickable' => true, 'page' => 6,      'current' => false],
        ], $this->pagination->buildLinks());
    }

    public function test_pagination_context_when_on_first_page()
    {
        $this->pagination
            ->setPagesCount(10)
            ->setPage(1)
        ;

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

    public function test_pagination_context_when_on_last_page()
    {
        $this->pagination
            ->setPagesCount(10)
            ->setPage(10)
        ;

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

    public function test_pagination_context_makes_sense_when_only_one_page()
    {
        $this->pagination
            ->setPagesCount(1)
            ->setPage(1)
        ;

        $this->assertEquals([
            ['type' => 'previous', 'clickable' => false, 'page' => 1,     'current' => false],
            ['type' => 'page', 'clickable' => false, 'page' => 1,     'current' => true],
            ['type' => 'next', 'clickable' => false, 'page' => 1,     'current' => false],
        ], $this->pagination->buildLinks());
    }
}
