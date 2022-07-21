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

namespace Tests\Unit\Core\Search;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Search\Pagination;

class PaginationTest extends TestCase
{
    public function testIsOffsetOutOfRange(): void
    {
        $this->assertTrue(Pagination::isOffsetOutOfRange(10, 20));
        $this->assertFalse(Pagination::isOffsetOutOfRange(20, 10));
        $this->assertTrue(Pagination::isOffsetOutOfRange(10, 10));
        $this->assertFalse(Pagination::isOffsetOutOfRange(10));
    }

    public function testComputeValidOffset(): void
    {
        $this->assertEquals(0, Pagination::computeValidOffset(10, 20, 10));
        $this->assertEquals(10, Pagination::computeValidOffset(20, 10, 10));
        $this->assertEquals(0, Pagination::computeValidOffset(10, 20, 30));
        $this->assertEquals(10, Pagination::computeValidOffset(20, 10, 30));
        $this->assertEquals(0, Pagination::computeValidOffset(10, 20));
        $this->assertEquals(10, Pagination::computeValidOffset(20, 10));
        $this->assertEquals(0, Pagination::computeValidOffset(10));
    }
}
