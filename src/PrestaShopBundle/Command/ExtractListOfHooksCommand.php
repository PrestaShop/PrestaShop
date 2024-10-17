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

use PrestaShop\PrestaShop\Core\Hook\Extractor\HookExtractor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'prestashop:extract:hooks',
    description: 'Extract Hooks Documentation files',
)]
final class ExtractListOfHooksCommand extends Command
{
    public function __construct(
        #[Autowire(service: 'prestashop.core.hook.extractor.hook_extractor')]
        private HookExtractor $hookExtractor,
    ) {
        parent::__construct();
        $this->hookExtractor = $hookExtractor;
    }

    // protected function configure()
    // {
    //     $this
    //         ->addOption(
    //             'source',
    //             null,
    //             InputOption::VALUE_OPTIONAL,
    //             'URL or Path to PrestaShop source code',
    //             '.'
    //         )
    //         ->addOption(
    //             'version',
    //             null,
    //             InputOption::VALUE_OPTIONAL,
    //             'Previously used to determine GitHub URL path, I will try to determine it automatically',
    //             'develop'
    //         )
    //         ->addOption(
    //             'output-dir',
    //             null,
    //             InputOption::VALUE_OPTIONAL,
    //             'Directory to output the generated markdown files, by default it will be docs/hooks/',
    //             'test'
    //         );
    // }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $hooks = $this->hookExtractor->findHooks(_PS_ROOT_DIR_);

        $this->generateMarkdownFiles($hooks, $output);

        return Command::SUCCESS;
    }

    // @TODO: Should this be part of this command?
    public function generateMarkdownFiles(array $hooks, OutputInterface $output): void
    {
        $outputDir = _PS_ROOT_DIR_ . '/docs/hooks/';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }
        // @TODO
        $githubBaseUrl = 'https://github.com/PrestaShop/PrestaShop/blob/develop/';
        foreach ($hooks as $hook) {
            $hookName = $hook['hook'];
            $fileName = $hookName . '.md';
            $filePath = $outputDir . $fileName;

            $title = $hookName;
            $hookTitle = !empty($hook['title']) ? "'" . $hook['title'] . "'" : '';
            $hookDescription = "'" . ($hook['description'] ?? '') . "'";
            $files = [
                [
                    'url' => $githubBaseUrl . $hook['file'],
                    'file' => $hook['file'],
                ],
            ];
            $locations = $hook['locations'];
            $type = $hook['type'];
            $hookAliases = isset($hook['aliases']) ? implode(', ', $hook['aliases']) : '';
            $arrayReturn = isset($hook['used_parameters']['array_return']) ? $hook['used_parameters']['array_return'] : 'false';
            $checkExceptions = isset($hook['used_parameters']['check_exceptions']) ? $hook['used_parameters']['check_exceptions'] : 'false';
            $chain = isset($hook['used_parameters']['chain']) ? $hook['used_parameters']['chain'] : 'false';
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

        ## Call of the Hook in the origin file

        ```php
        $fullImplementation;
        ```

        EOT;

            // Write the content to the markdown file
            file_put_contents($filePath, $content);
        }
    }
}
