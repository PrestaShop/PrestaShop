<?php

namespace PrestaShop\PrestaShop\Tests\Unit\Adapter;

use Adapter_ServiceLocator;
use Core_Foundation_IoC_Container;

use PHPUnit_Framework_TestCase;

class Adapter_ServiceLocatorTest extends PHPUnit_Framework_TestCase
{
    public function test_get_delegates_to_service_container()
    {
        Adapter_ServiceLocator::setServiceContainerInstance(
            new Core_Foundation_IoC_Container
        );

        $this->assertInstanceOf(
            'Core_Foundation_IoC_Container',
            Adapter_ServiceLocator::get('Core_Foundation_IoC_Container')
        );
    }
}
