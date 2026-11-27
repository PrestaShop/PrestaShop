<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Command;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\LegacyContextLoader;
use PrestaShop\PrestaShop\Adapter\Module\Configuration\ModuleSelfConfigurator;
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContext;
use PrestaShop\PrestaShop\Core\Context\ContextBuilderPreparer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

final class ConfigureModuleCommand extends AbstractModuleCommand
{
    public function __construct(
        TranslatorInterface $translator,
        LegacyContext $context,
        ContextBuilderPreparer $contextBuilderPreparer,
        Configuration $configuration,
        ShopContext $shopContext,
        #[Autowire(service: 'prestashop.adapter.legacy_context_loader')]
        LegacyContextLoader $legacyContextLoader,
        private readonly ModuleSelfConfigurator $moduleSelfConfigurator,
    ) {
        parent::__construct($translator, $context, $contextBuilderPreparer, $configuration, $shopContext, $legacyContextLoader);
    }

    protected function configure(): void
    {
        $this
            ->setName('prestashop:module:configure')
            ->setDescription('Configure one or more modules')
            ->addArgument(
                'modules',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'Modules to configure'
            )
            ->addOption(
                'config-file',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Configuration file. For multiple modules use "module:/path/to/config.yml".'
            );
    }

    protected function supportsExplicitShopContext(): bool
    {
        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->initializeContext($input, $output)) {
            return Command::INVALID;
        }

        $moduleNames = $input->getArgument('modules');
        $configFiles = $this->parseConfigFiles($moduleNames, $input->getOption('config-file'));

        if ($configFiles === null) {
            return Command::INVALID;
        }

        $hasErrors = false;
        foreach ($moduleNames as $moduleName) {
            try {
                if (!$this->configureModule($moduleName, $configFiles[$moduleName] ?? null)) {
                    $hasErrors = true;
                }
            } catch (Throwable $e) {
                $hasErrors = true;
                $this->displayMessage(
                    $this->translator->trans(
                        'Cannot configure module %module%. %error_details%',
                        [
                            '%module%' => $moduleName,
                            '%error_details%' => $e->getMessage(),
                        ],
                        'Admin.Modules.Notification'
                    ),
                    'error'
                );
            }
        }

        return $hasErrors ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * @param string[] $moduleNames
     * @param string[] $configFileOptions
     *
     * @return array<string, string>|null
     */
    private function parseConfigFiles(array $moduleNames, array $configFileOptions): ?array
    {
        if (empty($configFileOptions)) {
            return [];
        }

        // Keep the convenient single-module syntax:
        // prestashop:module:configure module --config-file=/path/to/config.yml
        if (
            count($moduleNames) === 1
            && count($configFileOptions) === 1
            && !str_starts_with($configFileOptions[0], $moduleNames[0] . ':')
        ) {
            return [$moduleNames[0] => $configFileOptions[0]];
        }

        $configFiles = [];

        foreach ($configFileOptions as $configFileOption) {
            $separatorPosition = strpos($configFileOption, ':');
            if ($separatorPosition === false) {
                $this->displayMessage(
                    $this->translator->trans(
                        'When configuring multiple modules, use --config-file=module:/path/to/config.yml.',
                        [],
                        'Admin.Modules.Notification'
                    ),
                    'error'
                );

                return null;
            }

            $moduleName = substr($configFileOption, 0, $separatorPosition);
            $filePath = substr($configFileOption, $separatorPosition + 1);
            if ($moduleName === '' || $filePath === '') {
                $this->displayMessage(
                    $this->translator->trans(
                        'Invalid --config-file value. Expected module:/path/to/config.yml.',
                        [],
                        'Admin.Modules.Notification'
                    ),
                    'error'
                );

                return null;
            }

            if (!in_array($moduleName, $moduleNames, true)) {
                $this->displayMessage(
                    $this->translator->trans(
                        'Configuration file provided for unknown module "%module%".',
                        ['%module%' => $moduleName],
                        'Admin.Modules.Notification'
                    ),
                    'error'
                );

                return null;
            }

            if (isset($configFiles[$moduleName])) {
                $this->displayMessage(
                    $this->translator->trans(
                        'A configuration file has already been provided for module "%module%".',
                        ['%module%' => $moduleName],
                        'Admin.Modules.Notification'
                    ),
                    'error'
                );

                return null;
            }

            $configFiles[$moduleName] = $filePath;
        }

        return $configFiles;
    }

    private function configureModule(string $moduleName, ?string $filePath): bool
    {
        // ModuleSelfConfigurator keeps the selected module and config file as internal state.
        // Use a fresh clone for each module so a config file discovered or assigned for one
        // module cannot leak into the next one in a bulk operation.
        $moduleSelfConfigurator = clone $this->moduleSelfConfigurator;
        $moduleSelfConfigurator->module($moduleName);
        if ($filePath !== null) {
            $moduleSelfConfigurator->file($filePath);
        }

        $errors = $moduleSelfConfigurator->validate();

        if (!empty($errors)) {
            $errors = array_map(static function ($error) {
                return '- ' . $error;
            }, $errors);
            array_unshift(
                $errors,
                $this->translator->trans(
                    'Validation of configuration details failed for module %module%:',
                    ['%module%' => $moduleName],
                    'Admin.Modules.Notification'
                )
            );

            $this->displayMessage($errors, 'error');

            return false;
        }

        if (!$moduleSelfConfigurator->configure()) {
            $this->displayMessage(
                $this->translator->trans(
                    'Cannot configure module %module%.',
                    ['%module%' => $moduleName],
                    'Admin.Modules.Notification'
                ),
                'error'
            );

            return false;
        }

        $this->displayMessage(
            $this->translator->trans(
                'Configuration successfully applied to module %module%.',
                ['%module%' => $moduleName],
                'Admin.Modules.Notification'
            ),
            'info'
        );

        return true;
    }
}
