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

use Exception;
use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Hook\Extractor\HookExtractor;
use PrestaShop\PrestaShop\Core\Hook\Provider\GridDefinitionHookByServiceIdsProvider;
use SimpleXMLElement;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'prestashop:update:hooks-documentation',
    description: 'Extract Hooks Documentation files',
)]
final class GenerateHooksDocumentationCommand extends Command
{
    private array $dynamicHookDetails = [
        GridDefinitionHookByServiceIdsProvider::GRID_DEFINITION_HOOK_SUFFIX => [
            'file' => 'src/Core/Grid/Definition/Factory/AbstractGridDefinitionFactory.php',
            'full_implementation' => '$this->hookDispatcher->dispatchWithParameters(\'action\' . Container::camelize($definition->getId()) . \'GridDefinitionModifier\', [
    \'definition\' => $definition,
])',
        ],
        GridDefinitionHookByServiceIdsProvider::GRID_QUERY_BUILDER_HOOK_SUFFIX => [
            'file' => 'src/Core/Grid/Data/Factory/DoctrineGridDataFactory.php',
            'full_implementation' => '$this->hookDispatcher->dispatchWithParameters(\'action\' . Container::camelize($this->gridId) . \'GridQueryBuilderModifier\', [
    \'search_query_builder\' => $searchQueryBuilder,
    \'count_query_builder\' => $countQueryBuilder,
    \'search_criteria\' => $searchCriteria,
])',
        ],
        GridDefinitionHookByServiceIdsProvider::GRID_DATA_HOOK_SUFFIX => [
            'file' => 'src/Core/Grid/GridFactory.php',
            'full_implementation' => '$this->hookDispatcher->dispatchWithParameters(\'action\' . Container::camelize($definition->getId()) . \'GridDataModifier\', [
    \'data\' => &$data,
])',
        ],
        GridDefinitionHookByServiceIdsProvider::GRID_FILTER_FORM_SUFFIX => [
            'file' => 'src/Core/Grid/Filter/GridFilterFormFactory.php',
            'full_implementation' => '$this->hookDispatcher->dispatchWithParameters(\'action\' . Container::camelize($definition->getId()) . \'GridFilterFormModifier\', [
    \'filter_form_builder\' => $formBuilder,
])',
        ],
        GridDefinitionHookByServiceIdsProvider::GRID_PRESENTER_SUFFIX => [
            'file' => 'src/Core/Grid/Presenter/GridPresenter.php',
            'full_implementation' => '$this->hookDispatcher->dispatchWithParameters(\'action\' . Container::camelize($definition->getId()) . \'GridPresenterModifier\', [
    \'presented_grid\' => &$presentedGrid,
])',
        ],
    ];

    public function __construct(
        private readonly HookExtractor $hookExtractor,
        private string $hookFile
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Extract Hooks Documentation files')
            ->addArgument(
                'output-dir',
                InputArgument::REQUIRED,
                'Directory containing the generated markdown files (ex: /home/user/devdocs-site/src/content/9/modules/concepts/hooks/list-of-hooks)',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputDir = $input->getArgument('output-dir');
        if (!is_dir($outputDir) || !is_writable($outputDir)) {
            throw new InvalidArgumentException(sprintf('The directory "%s" does not exist or is not writable.', $outputDir));
        }

        $hooks = $this->hookExtractor->findHooks();
        $formatedHooks = [];
        $formatedHooksKeys = [];
        foreach ($hooks as $hook) {
            $formatedHooks[$hook['hook']] = $hook;
            $formatedHooksKeys[] = $hook['hook'];
        }

        if (!file_exists($this->hookFile)) {
            throw new Exception(sprintf('File %s has not been found', $this->hookFile));
        }

        $hookFileContent = simplexml_load_file($this->hookFile);

        $xmlHooks = $this->parseHooks($hookFileContent->entities);

        foreach ($xmlHooks as $key => $xmlHook) {
            if (in_array($xmlHook['hook'], $formatedHooksKeys)) {
                $xmlHooks[$key]['type'] = $formatedHooks[$xmlHook['hook']]['type'];
                $xmlHooks[$key]['file'] = $formatedHooks[$xmlHook['hook']]['file'];
                $xmlHooks[$key]['aliases'] = $formatedHooks[$xmlHook['hook']]['aliases'] ?? [];
                $xmlHooks[$key]['used_parameters'] = $formatedHooks[$xmlHook['hook']]['used_parameters'] ?? [];
                $xmlHooks[$key]['full_implementation'] = $formatedHooks[$xmlHook['hook']]['full_implementation'];
                $xmlHooks[$key]['locations'] = $formatedHooks[$xmlHook['hook']]['locations'] ?? [];
                $xmlHooks[$key]['dynamic'] = false;
            } elseif ($matchingDynamicHook = $this->getMatchingDynamicHook($xmlHook['hook'])) {
                $xmlHooks[$key]['type'] = $this->defineHookType($xmlHook['hook']);
                $xmlHooks[$key]['file'] = $this->dynamicHookDetails[$matchingDynamicHook]['file'];
                $xmlHooks[$key]['aliases'] = [];
                $xmlHooks[$key]['used_parameters'] = [];
                $xmlHooks[$key]['full_implementation'] = $this->dynamicHookDetails[$matchingDynamicHook]['full_implementation'];
                $xmlHooks[$key]['locations'] = ['back office'];
                $xmlHooks[$key]['dynamic'] = true;
            } else {
                $xmlHooks[$key]['type'] = $this->defineHookType($xmlHook['hook']);
                $xmlHooks[$key]['file'] = '';
                $xmlHooks[$key]['aliases'] = [];
                $xmlHooks[$key]['used_parameters'] = [];
                $xmlHooks[$key]['full_implementation'] = '';
                $xmlHooks[$key]['locations'] = ['back office'];
                $xmlHooks[$key]['dynamic'] = false;
            }
        }

        $this->generateMarkdownFiles($xmlHooks, $outputDir, $output);

        return Command::SUCCESS;
    }

    protected function getMatchingDynamicHook(string $hookName): ?string
    {
        $matchingHooks = array_filter(array_keys($this->dynamicHookDetails), fn ($str) => stripos($hookName, $str) !== false);

        return !empty($matchingHooks) ? reset($matchingHooks) : null;
    }

    public function generateMarkdownFiles(array $hooks, string $mdDir, OutputInterface $output): void
    {
        $outputDir = $mdDir;
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }
        $outputDir = rtrim($outputDir, '/') . '/';

        $githubBaseUrl = 'https://github.com/PrestaShop/PrestaShop/blob/9.0.x/';
        $generatedHooks = 0;
        foreach ($hooks as $hook) {
            $hookName = $hook['hook'];
            $fileName = $hookName . '.md';
            $filePath = $outputDir . $fileName;

            // If documentation already exists we don't generate it because it may have more details in the documentation than we
            // can provide here, unless it's a dynamic hook then we update the automatic doc
            if (file_exists($filePath) && !$hook['dynamic']) {
                continue;
            }

            // If the hook was not detected in the code base (and no default file was set because it's a dynamic one)
            // then it probably no longer exists except in the hook.xml file We don't know yet if the XML file should
            // be cleaned from it But at least it's safe to assume there is no need to document it
            if (empty($hook['file'])) {
                continue;
            }

            $title = $hookName;
            $hookTitle = "'" . $this->escapeHookDetail($hook['title']) . "'";
            $hookDescription = "'" . $this->escapeHookDetail($hook['description']) . "'";
            $files = [
                [
                    'url' => $githubBaseUrl . $hook['file'],
                    'file' => $hook['file'],
                ],
            ];
            $locations = $hook['locations'];
            $type = $hook['type'];
            $hookAliases = isset($hook['aliases']) ? implode(', ', $hook['aliases']) : '';
            $arrayReturn = $hook['used_parameters']['array_return'] ?? 'false';
            $checkExceptions = $hook['used_parameters']['check_exceptions'] ?? 'false';
            $chain = $hook['used_parameters']['chain'] ?? 'false';
            $origin = 'core';

            $fullImplementation = $hook['full_implementation'];
            $fullImplementation = str_replace('`', '\\`', $fullImplementation);

            $content = <<<EOT
        ---
        Title: $title
        hidden: true
        hookTitle: $hookTitle
        files:
        EOT;

            foreach ($files as $file) {
                $fileUrl = $file['url'];
                $fileFilePath = $file['file'];
                $content .= "\n    -\n        url: '$fileUrl'\n        file: $fileFilePath";
            }

            $locationsYaml = '';
            foreach ($locations as $location) {
                $locationsYaml .= "\n    - '$location'";
            }

            $content .= <<<EOT

        locations:$locationsYaml
        type: $type
        hookAliases: $hookAliases
        array_return: $arrayReturn
        check_exceptions: $checkExceptions
        chain: $chain
        origin: $origin
        description: $hookDescription

        ---

        {{% hookDescriptor %}}

        EOT;

            if (!empty($fullImplementation)) {
                $content .= <<<EOT

        ## Call of the Hook in the origin file

        ```php
        $fullImplementation;
        ```

        EOT;
            }

            // Write the content to the markdown file
            file_put_contents($filePath, $content);
            ++$generatedHooks;
        }

        $output->writeln('<info> ' . $generatedHooks . ' hooks generated into ' . $mdDir . '</info>');
    }

    private function escapeHookDetail(string $hookDetail): string
    {
        $hookDetail = str_replace("'", "''", $hookDetail);
        $hookDetail = str_replace(["\n", "\r\n"], ' ', $hookDetail);

        return $hookDetail;
    }

    private function parseHooks(SimpleXMLElement $xml): array
    {
        $hooks = [];

        foreach ($xml->hook as $hook) {
            $hooks[] = [
                'hook' => (string) $hook->name,
                'title' => (string) $hook->title,
                'description' => (string) $hook->description,
            ];
        }

        return $hooks;
    }

    private function defineHookType(string $hookName): string
    {
        if (str_starts_with($hookName, 'action')) {
            return 'action';
        } elseif (str_starts_with($hookName, 'display')) {
            return 'display';
        } else {
            return 'notDefined';
        }
    }
}
