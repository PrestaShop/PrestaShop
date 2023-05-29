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

use PrestaShop\PrestaShop\Core\CommandBus\Parser\CommandDefinitionParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Lists all commands and queries definitions
 */
class ListCommandsAndQueriesCommand extends Command
{
    /**
     * @var CommandDefinitionParser
     */
    private $commandDefinitionParser;

    /**
     * @var array
     */
    private $commandAndQueries;

    public function __construct(CommandDefinitionParser $commandDefinitionParser, array $commandAndQueries)
    {
        parent::__construct();
        $this->commandDefinitionParser = $commandDefinitionParser;
        $this->commandAndQueries = $commandAndQueries;
    }

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
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputStyle = new OutputFormatterStyle('blue', null);
        $output->getFormatter()->setStyle('blue', $outputStyle);

        foreach ($this->commandAndQueries as $key => $commandName) {
            $commandDefinition = $this->commandDefinitionParser->parseDefinition($commandName);

            $output->writeln(++$key . '.');
            $output->writeln('<blue>Class: </blue><info>' . $commandDefinition->getClassName() . '</info>');
            $output->writeln('<blue>Type: </blue><info>' . $commandDefinition->getCommandType() . '</info>');
            $output->writeln('<comment>' . $commandDefinition->getDescription() . '</comment>');
            $output->writeln('');
        }

        return 0;
    }
}
