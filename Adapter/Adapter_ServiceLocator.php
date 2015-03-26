<?php

class Adapter_ServiceLocator
{
	/**
	 * @var Core_Foundation_IoC_Container
	 */
    private static $service_container;

    public static function setServiceContainerInstance(Core_Foundation_IoC_Container $container)
    {
        self::$service_container = $container;
    }

    public static function get($serviceName)
    {
        return self::$service_container->make($serviceName);
    }
}
