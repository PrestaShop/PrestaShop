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

namespace Tests\Unit\Core\Domain\Order\QueryResult;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderInvoiceAddressForViewing;

class OrderInvoiceAddressForViewingTest extends TestCase
{
    public function testConstruct(): void
    {
        $instance = new OrderInvoiceAddressForViewing(1, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k');
        $this->assertEquals(1, $instance->getAddressId());
        $this->assertEquals('a b', $instance->getFullName());
        $this->assertEquals('c', $instance->getCompanyName());
        $this->assertEquals('d', $instance->getAddress1());
        $this->assertEquals('e', $instance->getAddress2());
        $this->assertEquals('f', $instance->getStateName());
        $this->assertEquals('g', $instance->getCityName());
        $this->assertEquals('h', $instance->getCountryName());
        $this->assertEquals('i', $instance->getPostCode());
        $this->assertEquals('j', $instance->getPhoneNumber());
        $this->assertEquals('k', $instance->getMobilePhoneNumber());
        $this->assertNull($instance->getVatNumber());
        $this->assertNull($instance->getDni());
    }

    public function testConstructWithVatNumber(): void
    {
        $instance = new OrderInvoiceAddressForViewing(1, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l');
        $this->assertEquals(1, $instance->getAddressId());
        $this->assertEquals('a b', $instance->getFullName());
        $this->assertEquals('c', $instance->getCompanyName());
        $this->assertEquals('d', $instance->getAddress1());
        $this->assertEquals('e', $instance->getAddress2());
        $this->assertEquals('f', $instance->getStateName());
        $this->assertEquals('g', $instance->getCityName());
        $this->assertEquals('h', $instance->getCountryName());
        $this->assertEquals('i', $instance->getPostCode());
        $this->assertEquals('j', $instance->getPhoneNumber());
        $this->assertEquals('k', $instance->getMobilePhoneNumber());
        $this->assertEquals('l', $instance->getVatNumber());
        $this->assertNull($instance->getDni());
    }

    public function testConstructWithDNI(): void
    {
        $instance = new OrderInvoiceAddressForViewing(1, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm');
        $this->assertEquals(1, $instance->getAddressId());
        $this->assertEquals('a b', $instance->getFullName());
        $this->assertEquals('c', $instance->getCompanyName());
        $this->assertEquals('d', $instance->getAddress1());
        $this->assertEquals('e', $instance->getAddress2());
        $this->assertEquals('f', $instance->getStateName());
        $this->assertEquals('g', $instance->getCityName());
        $this->assertEquals('h', $instance->getCountryName());
        $this->assertEquals('i', $instance->getPostCode());
        $this->assertEquals('j', $instance->getPhoneNumber());
        $this->assertEquals('k', $instance->getMobilePhoneNumber());
        $this->assertEquals('l', $instance->getVatNumber());
        $this->assertEquals('m', $instance->getDni());
    }
}
