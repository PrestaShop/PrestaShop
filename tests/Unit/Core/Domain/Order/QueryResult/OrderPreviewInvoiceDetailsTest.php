<?php

namespace Tests\Unit\Core\Domain\Order\QueryResult;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreviewInvoiceDetails;

class OrderPreviewInvoiceDetailsTest extends TestCase
{
    public function testConstruct(): void
    {
        $instance = new OrderPreviewInvoiceDetails('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l');
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
        $this->assertEquals('k', $instance->getEmail());
        $this->assertEquals('l', $instance->getPhone());
        $this->assertNull($instance->getDNI());
    }

    public function testConstructWithDNI(): void
    {
        $instance = new OrderPreviewInvoiceDetails('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm');
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
        $this->assertEquals('k', $instance->getEmail());
        $this->assertEquals('l', $instance->getPhone());
        $this->assertEquals('m', $instance->getDni());
    }
}
