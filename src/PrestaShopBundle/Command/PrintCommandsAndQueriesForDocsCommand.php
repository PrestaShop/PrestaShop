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

declare(strict_types=1);

namespace PrestaShopBundle\Command;

use PrestaShop\PrestaShop\Core\CommandBus\Parser\CommandDefinition;
use PrestaShop\PrestaShop\Core\CommandBus\Parser\CommandHandlerDefinition;
use PrestaShop\PrestaShop\Core\CommandBus\Parser\CommandHandlerDefinitionParser;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

/**
 * Prints all existing commands and queries to .md file for documentation
 */
class PrintCommandsAndQueriesForDocsCommand extends ContainerAwareCommand
{
    /**
     * @var string
     */
    protected static $defaultName = 'prestashop:print-docs:commands-and-queries';

    /**
     * Command option name for providing destination file path
     */
    private const FILE_PATH_OPTION_NAME = 'file';

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var LoaderInterface
     */
    private $twigLoader;

    public function __construct()
    {
        $this->fs = new Filesystem();
        $this->twigLoader = new FilesystemLoader(__DIR__);

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setDescription('Prints available CQRS commands and queries to a file prepared for documentation')
            ->addOption(
                self::FILE_PATH_OPTION_NAME,
                null,
                InputOption::VALUE_REQUIRED,
                'Path to file into which all commands and queries should be printed'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    public function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $filePath = $input->getOption(self::FILE_PATH_OPTION_NAME);
        $this->validateFilePath($filePath);

        if (!$this->confirmExistingFileWillBeLost($filePath, $input, $output)) {
            $output->writeln('<comment>Cancelled</comment>');

            return null;
        }

        $this->fs->remove($filePath);
        $content = (new Environment($this->twigLoader))->render('views/cqrs-commands-list.md.twig', [
            'commandDefinitions' => $this->getCommandDefinitions(),
        ]);

        $this->fs->dumpFile($filePath, $content);
        $output->writeln(sprintf('<info>dumped commands & queries to %s</info>', $filePath));

        return 0;
    }

    /**
     * @return array<string, array<int, CommandDefinition>>
     */
    private function getCommandDefinitions(): array
    {
        $handlerDefinitions = $this->getContainer()->getParameter('prestashop.commands_and_queries');
        /** @var CommandHandlerDefinitionParser $commandHandlerDefinitionParser */
        $commandHandlerDefinitionParser = $this->getContainer()->get('prestashop.core.provider.command_handler_definition_parser');

        $commandDefinitions = [];
        foreach ($handlerDefinitions as $handlerClass => $commandClass) {
            $commandDefinition = $commandHandlerDefinitionParser->parseDefinition($handlerClass, $commandClass);
            if ($commandDefinition->isQueryType()) {
                $commandDefinitions[CommandHandlerDefinition::TYPE_QUERY][] = $commandDefinition;
                continue;
            }

            $commandDefinitions[CommandHandlerDefinition::TYPE_COMMAND][] = $commandDefinition;
        }

        return $commandDefinitions;
    }

    /**
     * @param string $filePath
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    private function confirmExistingFileWillBeLost(string $filePath, InputInterface $input, OutputInterface $output): bool
    {
        if ($this->fs->exists($filePath) && filesize($filePath)) {
            $helper = $this->getHelper('question');
            $confirmation = new ConfirmationQuestion(sprintf(
                '<question>File "%s" is not empty. All data will be lost. Proceed?</question>',
                $filePath
            ));

            return (bool) $helper->ask($input, $output, $confirmation);
        }

        return true;
    }

    /**
     * @param string $filePath
     */
    private function validateFilePath(string $filePath): void
    {
        if (!$filePath || !$this->fs->isAbsolutePath($filePath)) {
            throw new InvalidOptionException(sprintf(
                'Option --%s is required. It should contain absolute path to a destination file',
                self::FILE_PATH_OPTION_NAME
            ));
        }
    }
}
