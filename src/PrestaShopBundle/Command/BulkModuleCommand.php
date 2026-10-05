<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Command;

use Employee;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Core\Context\ContextBuilderPreparer;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

class BulkModuleCommand extends Command
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

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    public function __construct(
        protected readonly TranslatorInterface $translator,
        protected readonly LegacyContext $context,
        protected readonly ModuleManager $moduleManager,
        protected readonly ContextBuilderPreparer $contextBuilderPreparer,
        protected readonly Configuration $configuration,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('prestashop:module:bulk')
            ->setDescription('Manage multiple modules via command line')
            ->addArgument('action', InputArgument::REQUIRED, sprintf('Action to execute (Allowed actions: %s).', implode(' / ', $this->allowedActions)))
            ->addArgument('module names', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Modules on which the action will be executed')
            ->addOption('skip-overrides', null, InputOption::VALUE_NONE, 'Skip installing/uninstalling module overrides');
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
            $disableModuleOriginalValue = $this->configuration->get('PS_DISABLE_MODULE_OVERRIDES');
            $this->configuration->setTemporary('PS_DISABLE_MODULE_OVERRIDES', 1);
        }

        $hasErrors = false;

        try {
            foreach ($moduleNames as $moduleName) {
                try {
                    if (!$this->executeGenericModuleAction($action, $moduleName)) {
                        $hasErrors = true;
                    }
                } catch (Throwable $e) {
                    $hasErrors = true;
                    $this->displayMessage(
                        $this->translator->trans(
                            'Cannot %action% module %module%. %error_details%',
                            [
                                '%action%' => str_replace('_', ' ', $action),
                                '%module%' => $moduleName,
                                '%error_details%' => $e->getMessage(),
                            ],
                            'Admin.Modules.Notification'
                        ),
                        'error'
                    );
                }
            }
        } finally {
            if ($skipOverrides) {
                $this->configuration->setTemporary('PS_DISABLE_MODULE_OVERRIDES', $disableModuleOriginalValue);
            }
        }

        return $hasErrors ? Command::FAILURE : Command::SUCCESS;
    }

    protected function init(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        // We need to have an employee or the module hooks don't work
        // see LegacyHookSubscriber
        if (!$this->context->getContext()->employee) {
            // Even a non existing employee is fine
            $this->context->getContext()->employee = new Employee(42);
        }

        // We must initialize the language context because ModuleRepository depends on it for its cache key
        $this->contextBuilderPreparer->prepareLanguageId($this->configuration->get('PS_LANG_DEFAULT'));
    }

    protected function executeGenericModuleAction($action, $moduleName): bool
    {
        if ($this->moduleManager->{$action}($moduleName)) {
            $this->displayMessage(
                $this->translator->trans(
                    '%action% action on module %module% succeeded.',
                    [
                        '%action%' => ucfirst(AdminModuleDataProvider::ACTIONS_TRANSLATION_LABELS[$action]),
                        '%module%' => $moduleName,
                    ],
                    'Admin.Modules.Notification'
                )
            );

            return true;
        }

        $error = $this->moduleManager->getError($moduleName);
        $this->displayMessage(
            $this->translator->trans(
                'Cannot %action% module %module%. %error_details%',
                [
                    '%action%' => str_replace('_', ' ', $action),
                    '%module%' => $moduleName,
                    '%error_details%' => $error,
                ],
                'Admin.Modules.Notification'
            ),
            'error'
        );

        return false;
    }

    protected function displayMessage($message, $type = 'info')
    {
        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper('formatter');

        $this->output->writeln(
            $formatter->formatBlock($message, $type, true)
        );
    }
}
