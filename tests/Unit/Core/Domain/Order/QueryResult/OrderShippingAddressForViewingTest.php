<?php

namespace Tests\Unit\Core\Domain\Order\QueryResult;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderShippingAddressForViewing;

class OrderShippingAddressForViewingTest extends TestCase
{
    public function testConstruct(): void
    {
        $instance = new OrderShippingAddressForViewing(1, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k');
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
        $instance = new OrderShippingAddressForViewing(1, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l');
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
        $instance = new OrderShippingAddressForViewing(1, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm');
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
