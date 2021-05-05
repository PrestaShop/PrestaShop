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
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreviewShippingDetails;

class OrderPreviewShippingDetailsTest extends TestCase
{
    public function testConstruct(): void
    {
        $instance = new OrderPreviewShippingDetails('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm');
        $this->assertEquals('a', $instance->getFirstName());
        $this->assertEquals('b', $instance->getLastName());
        $this->assertEquals('c', $instance->getCompany());
        $this->assertEquals('d', $instance->getVatNumber());
        $this->assertEquals('e', $instance->getAddress1());
        $this->assertEquals('f', $instance->getAddress2());
        $this->assertEquals('g', $instance->getCity());
        $this->assertEquals('h', $instance->getPostalCode());
        $this->assertEquals('i', $instance->getStateName());
        $this->assertEquals('j', $instance->getCountry());
        $this->assertEquals('k', $instance->getPhone());
        $this->assertEquals('l', $instance->getCarrierName());
        $this->assertEquals('m', $instance->getTrackingNumber());
        $this->assertNull($instance->getDNI());
        $this->assertNull($instance->getTrackingUrl());
    }

    public function testConstructWithDNI(): void
    {
        $instance = new OrderPreviewShippingDetails('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n');
        $this->assertEquals('a', $instance->getFirstName());
        $this->assertEquals('b', $instance->getLastName());
        $this->assertEquals('c', $instance->getCompany());
        $this->assertEquals('d', $instance->getVatNumber());
        $this->assertEquals('e', $instance->getAddress1());
        $this->assertEquals('f', $instance->getAddress2());
        $this->assertEquals('g', $instance->getCity());
        $this->assertEquals('h', $instance->getPostalCode());
        $this->assertEquals('i', $instance->getStateName());
        $this->assertEquals('j', $instance->getCountry());
        $this->assertEquals('k', $instance->getPhone());
        $this->assertEquals('l', $instance->getCarrierName());
        $this->assertEquals('m', $instance->getTrackingNumber());
        $this->assertEquals('n', $instance->getDni());
        $this->assertNull($instance->getTrackingUrl());
    }

    public function testConstructWithTrackingUrl(): void
    {
        $instance = new OrderPreviewShippingDetails('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o');
        $this->assertEquals('a', $instance->getFirstName());
        $this->assertEquals('b', $instance->getLastName());
        $this->assertEquals('c', $instance->getCompany());
        $this->assertEquals('d', $instance->getVatNumber());
        $this->assertEquals('e', $instance->getAddress1());
        $this->assertEquals('f', $instance->getAddress2());
        $this->assertEquals('g', $instance->getCity());
        $this->assertEquals('h', $instance->getPostalCode());
        $this->assertEquals('i', $instance->getStateName());
        $this->assertEquals('j', $instance->getCountry());
        $this->assertEquals('k', $instance->getPhone());
        $this->assertEquals('l', $instance->getCarrierName());
        $this->assertEquals('m', $instance->getTrackingNumber());
        $this->assertEquals('n', $instance->getDni());
        $this->assertEquals('o', $instance->getTrackingUrl());
    }
}
