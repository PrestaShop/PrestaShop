<?php

class Core_Business_ContainerBuilder
{
    public function build()
    {
        $container = new Core_Foundation_IoC_Container;

        $container->bind('Core_Business_Configuration', 'Adapter_Configuration', true);
        $container->bind('Core_Foundation_Database_Database', 'Adapter_Database', true);

        return $container;
    }
}
