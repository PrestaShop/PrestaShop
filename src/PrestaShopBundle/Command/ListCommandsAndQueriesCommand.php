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

use PrestaShop\PrestaShop\Core\CommandBus\Parser\CommandDefinition;
use PrestaShop\PrestaShop\Core\CommandBus\Parser\CommandDefinitionParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Lists all commands and queries definitions
 */
class ListCommandsAndQueriesCommand extends Command
{
    private CommandDefinitionParser $commandDefinitionParser;

    /**
     * @var string[]
     */
    private array $commandAndQueries;

    private bool $isFormatSimple;

    /**
     * @var Route[]
     */
    private array $apiResourcesList;

    private RouterInterface $router;

    public function __construct(
        CommandDefinitionParser $commandDefinitionParser,
        array $commandAndQueries,
        RouterInterface $router
    ) {
        parent::__construct();
        $this->commandDefinitionParser = $commandDefinitionParser;
        $this->commandAndQueries = $commandAndQueries;
        $this->isFormatSimple = false;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('prestashop:list:commands-and-queries')
            ->setDescription('Lists available CQRS commands and queries')
            ->addOption(
                'domain',
                'd',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'Filter available CQRS by domain.'
            )
            ->addOption(
                'format',
                'f',
                InputOption::VALUE_REQUIRED,
                'Outputs either a regular or simplified format.',
                'regular'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->apiResourcesList = $this->getResourceList();
        $this->handleOptions($input);

        $outputStyle = new OutputFormatterStyle('blue', null);
        $output->getFormatter()->setStyle('blue', $outputStyle);

        foreach ($this->commandAndQueries as $key => $commandName) {
            $commandDefinition = $this->commandDefinitionParser->parseDefinition($commandName);
            $CQRSEndpointURI = $this->getCQRSEndpointURI($commandDefinition);

            if ($this->isFormatSimple) {
                $output->writeln('<info>' . $commandDefinition->getClassName() . (!empty($CQRSEndpointURI) ? ' OK' : ' NOT OK') . '</info>');
            } else {
                $output->writeln(++$key . '.');
                $output->writeln('<blue>Class: </blue><info>' . $commandDefinition->getClassName() . '</info>');
                $output->writeln('<blue>Type: </blue><info>' . $commandDefinition->getCommandType() . '</info>');
                $output->writeln('<blue>API: </blue><info>' . $CQRSEndpointURI . '</info>');
                $output->writeln('<comment>' . $commandDefinition->getDescription() . '</comment>');
                $output->writeln('');
            }
        }

        return 0;
    }

    private function handleOptions(InputInterface $input): void
    {
        if ($input->getOption('domain') !== []) {
            $this->filterCQRS($input->getOption('domain'));
        }

        if ($input->getOption('format') === 'simple') {
            $this->isFormatSimple = true;
        }
    }

    /**
     * @param string[] $filters
     */
    private function filterCQRS(array $filters): void
    {
        $this->commandAndQueries = array_filter($this->commandAndQueries, function (string $currentCQRS) use ($filters) {
            foreach ($filters as $filter) {
                // We append a backslash behind the filter to find only exact matches
                if (str_contains($currentCQRS, $filter . '\\') !== false) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * This method takes the list of all routes on the project and filters out all the api-platform endpoints.
     *
     * @return Route[]
     */
    private function getResourceList(): array
    {
        return array_filter($this->router->getRouteCollection()->all(), function ($value, $key) {
            if (preg_match('/^_api_/', $key) === 1) {
                return $value;
            }
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * This method takes the filtered list of routes, and checks it against the list of CQRS.
     * The CQRS that have a route with the correct method are implemented, so we return the URI of the endpoint.
     */
    private function getCQRSEndpointURI(CommandDefinition $commandDefinition): string
    {
        foreach ($this->apiResourcesList as $resource) {
            $apiResourceClass = explode('\\', $resource->getDefault('_api_resource_class'));
            if (preg_match('/' . str_replace('/', '', end($apiResourceClass)) . '/i', $commandDefinition->getClassName()) === 1
                && $this->doesMethodsMatchType($resource->getMethods(), $commandDefinition->getCommandType())
            ) {
                return $resource->getPath();
            }
        }

        return '';
    }

    /**
     * @param string[] $methods
     */
    private function doesMethodsMatchType(array $methods, string $commandType): bool
    {
        switch ($commandType) {
            case 'Command':
                return in_array('POST', $methods) || in_array('PUT', $methods) || in_array('PATCH', $methods);
            case 'Query':
                return in_array('GET', $methods) || in_array('DELETE', $methods);
            default:
                return false;
        }
    }
}
