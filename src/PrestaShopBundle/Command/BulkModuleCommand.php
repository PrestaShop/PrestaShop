<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BulkModuleCommand extends ModuleCommand
{
    private $allowedActions = [
        'install',
        'uninstall',
        'enable',
        'disable',
        'reset',
        'upgrade',
        'delete',
    ];

    protected function configure()
    {
        $this
            ->setName('prestashop:module:bulk')
            ->setDescription('Manage multiple modules via command line')
            ->addArgument(
                'action',
                InputArgument::REQUIRED,
                sprintf('Action to execute (Allowed actions: %s).', implode(' / ', $this->allowedActions))
            )
            ->addArgument(
                'module names',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'Modules on which the action will be executed'
            )
            ->addOption(
                'skip-overrides',
                null,
                InputOption::VALUE_NONE,
                'Skip installing/uninstalling module overrides'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->init($input, $output);

        $skipOverrides = (bool) $input->getOption('skip-overrides');

        $moduleNames = $input->getArgument('module names');
        $action = $input->getArgument('action');

        if (!in_array($action, $this->allowedActions, true)) {
            $this->displayMessage(
                $this->translator->trans(
                    'Unknown module action. It must be one of these values: %actions%',
                    ['%actions%' => implode(' / ', $this->allowedActions)],
                    'Admin.Modules.Notification'
                ),
                'error'
            );

            return Command::FAILURE;
        }

        if ($skipOverrides) {
            $disableModuleOriginaleValue = $this->configuration->get('PS_DISABLE_MODULE_OVERRIDES');
            $this->configuration->setTemporary('PS_DISABLE_MODULE_OVERRIDES', 1);
        }

        try {
            foreach ($moduleNames as $moduleName) {
                $this->executeGenericModuleAction($action, $moduleName);
            }
        } finally {
            if ($skipOverrides) {
                $this->configuration->setTemporary('PS_DISABLE_MODULE_OVERRIDES', $disableModuleOriginaleValue);
            }
        }

        return Command::SUCCESS;
    }
}
