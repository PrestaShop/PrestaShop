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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Collects all Commands & Queries and puts them into container for later processing.
 */
class CommandAndQueryCollectorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!in_array($container->getParameter('kernel.environment'), ['dev', 'test'])) {
            return;
        }

        $commandsAndQueries = $this->findCommandsAndQueries($container);
        $container->setParameter('prestashop.commands_and_queries', $commandsAndQueries);
    }

    /**
     * Gets command for each provided handler
     *
     * @param ContainerBuilder $container
     *
     * @return string[]
     */
    private function findCommandsAndQueries(ContainerBuilder $container)
    {
        $handlers = $container->findTaggedServiceIds('tactician.handler');

        $commands = [];
        foreach ($handlers as $handler) {
            if (isset(current($handler)['command'])) {
                $commands[] = current($handler)['command'];
            }
        }

        return $commands;
    }
}
