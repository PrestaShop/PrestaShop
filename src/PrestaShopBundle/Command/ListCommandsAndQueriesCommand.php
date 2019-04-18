<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Command;

use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommandsAndQueriesCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this
            ->setName('prestashop:cqrs:commands-and-queries')
            ->setDescription('Lists available CQRS commands and queries')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commands = $this->getContainer()->getParameter('prestashop.commands_and_queries');
        $this->setOutputStyles($output);

        foreach ($commands as $key => $commandName) {
            $docComment = preg_replace('/[\*\/]/', '', (new ReflectionClass($commandName))->getDocComment());
            $typeByName = $this->getType($commandName);

            $output->writeln(++$key . '.');
            $output->writeln("<blue>Class: </blue><info>$commandName</info>");
            $output->writeln("<blue>Type: </blue><info>$typeByName</info>");
            $output->writeln("<comment>$docComment</comment>");
        }
    }

    /**
     * Checks whether the command is of type Query or Command by provided name
     *
     * @param $commandName
     *
     * @return string
     */
    private function getType($commandName)
    {
        if (strpos($commandName, 'Command')) {
            return 'Command';
        }

        return 'Query';
    }

    /**
     * Sets custom output styles
     *
     * @param OutputInterface $output
     */
    private function setOutputStyles(OutputInterface $output)
    {
        $outputStyle = new OutputFormatterStyle('blue', null);
        $output->getFormatter()->setStyle('blue', $outputStyle);
    }
}
