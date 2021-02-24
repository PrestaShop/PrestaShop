<?php

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
    }
}
