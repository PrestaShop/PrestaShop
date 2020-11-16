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

namespace PrestaShopBundle\Command;

use PrestaShop\PrestaShop\Core\CommandBus\Parser\CommandHandlerDefinitionParser;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Lists all commands and queries definitions
 */
class ListCommandsAndQueriesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('prestashop:list:commands-and-queries')
            ->setDescription('Lists available CQRS commands and queries')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commands = $this->getContainer()->getParameter('prestashop.commands_and_queries');
        /** @var CommandHandlerDefinitionParser $handlerDefinitionParser */
        $handlerDefinitionParser = $this->getContainer()->get('prestashop.core.provider.command_handler_definition_parser');

        $outputStyle = new OutputFormatterStyle('blue', null);
        $output->getFormatter()->setStyle('blue', $outputStyle);

        $i = 1;
        foreach ($commands as $handlerName => $commandName) {
            $commandDefinition = $handlerDefinitionParser->parseDefinition($handlerName, $commandName);

            if (empty($commandDefinition->getHandlerInterfaces())) {
                $interfaces = '';
            } else {
                $interfaces = sprintf('(Implements: %s)', implode(', ', $commandDefinition->getHandlerInterfaces()));
            }

            $output->writeln($i++ . '.');
            $output->writeln('<blue>' . ucfirst($commandDefinition->getType()) . ': </blue><info>' . $commandDefinition->getCommandClass() . '</info>');
            $output->writeln('<blue>Handler: </blue><info>' . $commandDefinition->getHandlerClass() . '. ' . $interfaces . '</info>');
            $output->writeln('<blue>Return type: </blue><info>' . $commandDefinition->getReturnType() ?: 'not defined' . '</info>');
            $output->writeln('<comment>' . $commandDefinition->getDescription() . '</comment>');
            $output->writeln('');
        }

        return 0;
    }
}
