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

namespace Tests\Unit\Classes;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Cart\AmountImmutable;

class AmountImmutableTest extends TestCase
{
    public function testGet(): void
    {
        $amount = new AmountImmutable(2.3, 3.5);

        $this->assertEquals(2.3, $amount->getTaxIncluded());
        $this->assertEquals(3.5, $amount->getTaxExcluded());
    }

    public function testAdd(): void
    {
        $amount = new AmountImmutable(2.3, 3.5);
        $amount1 = new AmountImmutable(4.6, 7.2);
        $amount2 = $amount->add($amount1);

        $this->assertEquals(2.3, $amount->getTaxIncluded());
        $this->assertEquals(3.5, $amount->getTaxExcluded());

        $this->assertEquals(4.6, $amount1->getTaxIncluded());
        $this->assertEquals(7.2, $amount1->getTaxExcluded());

        $this->assertEquals(6.9, $amount2->getTaxIncluded());
        $this->assertEquals(10.7, $amount2->getTaxExcluded());
    }

    public function testSub(): void
    {
        $amount = new AmountImmutable(2.3, 3.5);
        $amount1 = new AmountImmutable(4.8, 7.2);
        $amount2 = $amount1->sub($amount);

        $this->assertEquals(2.3, $amount->getTaxIncluded());
        $this->assertEquals(3.5, $amount->getTaxExcluded());

        $this->assertEquals(4.8, $amount1->getTaxIncluded());
        $this->assertEquals(7.2, $amount1->getTaxExcluded());

        $this->assertEquals(2.5, $amount2->getTaxIncluded());
        $this->assertEquals(3.7, $amount2->getTaxExcluded());
    }
}
