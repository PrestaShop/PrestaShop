<?php

namespace PrestaShop\PrestaShop\tests\Unit\Core\Business\Product\Search;

use PHPUnit_Framework_Testcase;
use PrestaShop\PrestaShop\Core\Business\Product\Search\PaginationResult;

class PaginationResultTest extends PHPUnit_Framework_Testcase
{
    public function setup()
    {
        $this->pagination = new PaginationResult;
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
