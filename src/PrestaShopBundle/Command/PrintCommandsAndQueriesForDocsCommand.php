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

use PrestaShop\PrestaShop\Core\CommandBus\Parser\CommandHandlerDefinition;
use PrestaShop\PrestaShop\Core\CommandBus\Parser\CommandHandlerDefinitionParser;
use PrestaShop\PrestaShop\Core\Util\String\StringModifierInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;

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
     * Option name for providing destination directory path
     */
    private const DESTINATION_DIR_OPTION_NAME = 'dir';

    /**
     * Option name for forcing command (remove all confirmations)
     */
    private const FORCE_OPTION_NAME = 'force';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Environment
     */
    private $twigEnv;

    /**
     * @var StringModifierInterface
     */
    private $stringModifier;

    /**
     * @param Filesystem $filesystem
     * @param Environment $twigEnv
     * @param StringModifierInterface $stringModifier
     */
    public function __construct(
        Filesystem $filesystem,
        Environment $twigEnv,
        StringModifierInterface $stringModifier
    ) {
        parent::__construct();
        $this->filesystem = $filesystem;
        $this->twigEnv = $twigEnv;
        $this->stringModifier = $stringModifier;
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setDescription(
                'Prints available CQRS commands and queries to a file prepared for documentation' . PHP_EOL . PHP_EOL .
                'Example: php ./bin/console prestashop:print-docs:commands-and-queries --dir=/path/to/doc_project/src/content/1.7/development/architecture/domain/references'
            )
            ->addOption(
                self::DESTINATION_DIR_OPTION_NAME,
                null,
                InputOption::VALUE_REQUIRED,
                'Path to file into which all commands and queries should be printed'
            )
            ->addOption(
                self::FORCE_OPTION_NAME,
                null,
                InputOption::VALUE_NONE,
                'Forces command to be executed without confirmations'
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
        $destinationDir = $this->getDestinationDir($input);

        if (!$this->confirmExistingFileWillBeLost($destinationDir, $input, $output)) {
            $output->writeln('<comment>Cancelled</comment>');

            return null;
        }

        $this->filesystem->remove($destinationDir);

        $definitions = $this->getCommandHandlerDefinitions();
        ksort($definitions);

        foreach ($definitions as $domain => $definitionsByType) {
            $content = $this->twigEnv->render('src/PrestaShopBundle/Command/views/cqrs-commands-list.md.twig', [
                'domain' => $domain,
                'definitionsByType' => $definitionsByType,
            ]);

            $this->filesystem->dumpFile($this->getDestinationFilePath($destinationDir, $domain), $content);
        }

        $indexFileContent = $this->twigEnv->render('src/PrestaShopBundle/Command/views/cqrs-commands-index.md.twig');
        $this->filesystem->dumpFile(sprintf('%s/_index.md', $destinationDir), $indexFileContent);
        $output->writeln(sprintf('<info>dumped commands & queries to %s</info>', $destinationDir));

        return 0;
    }

    /**
     * @param string $targetDir
     * @param string $domain
     *
     * @return string
     */
    private function getDestinationFilePath(string $targetDir, string $domain): string
    {
        return sprintf(
            '%s/%s.md',
            $targetDir,
            $this->stringModifier->convertCamelCaseToKebabCase($domain)
        );
    }

    /**
     * @return array<string, array<string, array<int, CommandHandlerDefinition>>>
     */
    private function getCommandHandlerDefinitions(): array
    {
        $handlerDefinitions = $this->getContainer()->getParameter('prestashop.commands_and_queries');
        /** @var CommandHandlerDefinitionParser $commandHandlerDefinitionParser */
        $commandHandlerDefinitionParser = $this->getContainer()->get('prestashop.core.provider.command_handler_definition_parser');

        $commandDefinitionsByDomain = [];
        foreach ($handlerDefinitions as $handlerClass => $commandClass) {
            $commandDefinition = $commandHandlerDefinitionParser->parseDefinition($handlerClass, $commandClass);
            $commandDefinitionsByDomain[$commandDefinition->getDomain()][$commandDefinition->getType()][] = $commandDefinition;
        }

        return $commandDefinitionsByDomain;
    }

    /**
     * @param string $targetDir
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    private function confirmExistingFileWillBeLost(string $targetDir, InputInterface $input, OutputInterface $output): bool
    {
        $force = $input->getOption(self::FORCE_OPTION_NAME);

        if ($force || !$this->filesystem->exists($targetDir)) {
            return true;
        }

        $helper = $this->getHelper('question');
        $confirmation = new ConfirmationQuestion(sprintf(
            '<question>All data in directory "%s" will be lost. Proceed?</question>',
            $targetDir
        ));

        return (bool) $helper->ask($input, $output, $confirmation);
    }

    /**
     * @param InputInterface $input
     *
     * @return string
     */
    private function getDestinationDir(InputInterface $input): string
    {
        $destinationPath = $input->getOption(self::DESTINATION_DIR_OPTION_NAME);

        if (!$destinationPath || !$this->filesystem->isAbsolutePath($destinationPath)) {
            throw new InvalidOptionException(sprintf(
                'Option --%s is required. It should contain absolute path to a destination directory',
                self::DESTINATION_DIR_OPTION_NAME
            ));
        }

        if ($this->filesystem->exists($destinationPath) && !is_dir($destinationPath)) {
            throw new InvalidOptionException(sprintf(
                '"%s" is not a directory',
                self::DESTINATION_DIR_OPTION_NAME
            ));
        }

        return $destinationPath;
    }
}
