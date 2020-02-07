<?php

namespace PrestaShopBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class LoadCheckServicesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('kernel.active_modules')) {
            return;
        }

        $activeModules = $container->getParameter('kernel.active_modules');
        if (!in_array('ps_healthcheck', $activeModules, true)) {
            return;
        }

        // Check if the primary service is defined
        if (!$container->has('prestashop.module.healthcheck.checks_runner')) {
            return;
        }

        $definition = $container->findDefinition('prestashop.module.healthcheck.checks_runner');

        // Find all service IDs with the prestashop.healthcheck tag
        $taggedServices = $container->findTaggedServiceIds('admin.prestashop.healthcheck');

        foreach ($taggedServices as $id => $tags) {
            // add the service to the Checks collection service
            $definition->addMethodCall('addCheck', [new Reference($id)]);
        }
    }
}
