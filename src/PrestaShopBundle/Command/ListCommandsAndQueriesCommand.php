<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Command;

use ApiPlatform\Metadata\Resource\Factory\AttributesResourceNameCollectionFactory;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use Exception;
use PrestaShop\PrestaShop\Core\CommandBus\Parser\CommandDefinition;
use PrestaShop\PrestaShop\Core\CommandBus\Parser\CommandDefinitionParser;
use PrestaShopBundle\ApiPlatform\Scopes\ApiResourceScopesExtractorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Lists all commands and queries definitions
 */
class ListCommandsAndQueriesCommand extends Command
{
    private bool $isFormatSimple;

    /**
     * @var ResourceMetadataCollection[]
     */
    private array $apiResourcesList;

    public function __construct(
        private CommandDefinitionParser $commandDefinitionParser,
        private array $commandAndQueries,
        private ResourceNameCollectionFactoryInterface $resourceNameCollectionFactory,
        private ResourceMetadataCollectionFactoryInterface $resourceMetadataFactory,
        private ApiResourceScopesExtractorInterface $apiResourceScopesExtractor,
        private string $moduleDir
    ) {
        parent::__construct();
        $this->isFormatSimple = false;
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
            ->addOption(
                'hasApiEndpoint',
                '',
                InputOption::VALUE_OPTIONAL,
                'Filter CQRS with (1) or without (0) an endpoint'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->apiResourcesList = $this->getResourceList();
        $this->handleOptions($input);

        $outputStyle = new OutputFormatterStyle('blue', null);
        $output->getFormatter()->setStyle('blue', $outputStyle);

        $optionHasApiEndpoint = $input->getOption('hasApiEndpoint') !== null ? (bool) $input->getOption('hasApiEndpoint') : null;

        foreach ($this->commandAndQueries as $key => $commandName) {
            $commandDefinition = $this->commandDefinitionParser->parseDefinition($commandName);
            $cqrsEndpointURI = $this->getCQRSEndpointURI($commandDefinition);

            if ($optionHasApiEndpoint !== null) {
                if (($optionHasApiEndpoint && empty($cqrsEndpointURI)) || (!$optionHasApiEndpoint && !empty($cqrsEndpointURI))) {
                    continue;
                }
            }

            if ($this->isFormatSimple) {
                $output->writeln('<info>' . $commandDefinition->getClassName() . (!empty($cqrsEndpointURI) ? ' OK' : ' NOT OK') . '</info>');
            } else {
                $output->writeln(++$key . '.');
                $output->writeln('<blue>Class: </blue><info>' . $commandDefinition->getClassName() . '</info>');
                $output->writeln('<blue>Type: </blue><info>' . $commandDefinition->getCommandType() . '</info>');
                if (!empty($cqrsEndpointURI)) {
                    $output->writeln('<blue>API: </blue><info>' . $cqrsEndpointURI . '</info>');
                }
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
     * This method rebuild the resources from the api platform attributes including modules.
     *
     * @return ResourceMetadataCollection[]
     */
    private function getResourceList(): array
    {
        $resourceMetadataCollection = [];

        // Get core resources (original working logic)
        foreach ($this->resourceNameCollectionFactory->create() as $resourceClass) {
            try {
                $resourceMetadataCollection[] = $this->resourceMetadataFactory->create($resourceClass);
            } catch (Exception $e) {
                // Skip resources that can't be loaded
                continue;
            }
        }

        // Get resource classes from modules
        $moduleApiResourceScopes = array_filter(
            $this->apiResourceScopesExtractor->getAllApiResourceScopes(),
            fn ($scope) => !$scope->fromCore()
        );

        foreach ($moduleApiResourceScopes as $apiResourceScope) {
            $moduleName = $apiResourceScope->getModuleName();
            $moduleResourcePaths = $this->getModuleResourcePaths($moduleName);

            if (empty($moduleResourcePaths)) {
                continue;
            }

            try {
                $moduleResourceExtractor = new AttributesResourceNameCollectionFactory($moduleResourcePaths);
                foreach ($moduleResourceExtractor->create() as $resourceClass) {
                    try {
                        $resourceMetadataCollection[] = $this->resourceMetadataFactory->create($resourceClass);
                    } catch (Exception $e) {
                        // Skip individual resources that can't be loaded (e.g., missing dependencies)
                        continue;
                    }
                }
            } catch (Exception $e) {
                // Skip entire module if resource factory fails (e.g., invalid path)
                continue;
            }
        }

        return $resourceMetadataCollection;
    }

    private function getModuleResourcePaths(string $moduleName): array
    {
        $paths = [];
        $modulePath = $this->moduleDir . $moduleName;

        // Folder containing ApiPlatform resources classes
        $moduleResourcesPath = sprintf('%s/src/ApiPlatform/Resources', $modulePath);
        if (file_exists($moduleResourcesPath)) {
            $paths[] = $moduleResourcesPath;
        }

        return $paths;
    }

    /**
     * This method takes the filtered list of routes, and checks it against the list of CQRS.
     * The CQRS that have a route with the correct method are implemented, so we return the URI of the endpoint.
     */
    private function getCQRSEndpointURI(CommandDefinition $commandDefinition): string
    {
        $cqrsClassName = $commandDefinition->getClassName();
        $matchingEndpoints = [];

        foreach ($this->apiResourcesList as $apiResources) {
            foreach ($apiResources as $resource) {
                $apiResourceOperations = $resource->getOperations();
                foreach ($apiResourceOperations as $operation) {
                    $extraProperties = $operation->getExtraProperties();

                    // Check if this operation uses the same CQRS class
                    $operationCQRSClass = null;
                    if (!empty($extraProperties['CQRSQuery'])) {
                        $operationCQRSClass = $extraProperties['CQRSQuery'];
                    } elseif (!empty($extraProperties['CQRSCommand'])) {
                        $operationCQRSClass = $extraProperties['CQRSCommand'];
                    }

                    if ($operationCQRSClass === $cqrsClassName
                        && $this->doesMethodsMatchType($operation->getMethod(), $commandDefinition->getCommandType())) {
                        $matchingEndpoints[] = [
                            'method' => $operation->getMethod(),
                            'uri' => $operation->getUriTemplate(),
                        ];
                    }
                }
            }
        }

        if (empty($matchingEndpoints)) {
            return '';
        }

        if (count($matchingEndpoints) === 1) {
            return $matchingEndpoints[0]['method'] . ' ' . $matchingEndpoints[0]['uri'];
        }

        // Multiple endpoints - format as method:uri pairs
        $formattedEndpoints = array_map(
            fn ($endpoint) => $endpoint['method'] . ' ' . $endpoint['uri'],
            $matchingEndpoints
        );

        return implode(', ', array_unique($formattedEndpoints));
    }

    private function doesMethodsMatchType(string $method, string $commandType): bool
    {
        switch ($commandType) {
            case 'Command':
                return in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE']);
            case 'Query':
                return $method === 'GET';
            default:
                return false;
        }
    }
}
