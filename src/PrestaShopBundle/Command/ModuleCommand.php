<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Command;

use Employee;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\Configuration\ModuleSelfConfigurator;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\ContextBuilderPreparer;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ModuleCommand extends Command
{
    private $allowedActions = [
        'install',
        'uninstall',
        'enable',
        'disable',
        'reset',
        'upgrade',
        'configure',
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
        protected readonly ModuleSelfConfigurator $moduleSelfConfigurator,
        protected readonly ModuleManager $moduleManager,
        protected readonly ContextBuilderPreparer $contextBuilderPreparer,
        protected readonly ConfigurationInterface $configuration,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('prestashop:module')
            ->setDescription('Manage your modules via command line')
            ->addArgument('action', InputArgument::REQUIRED, sprintf('Action to execute (Allowed actions: %s).', implode(' / ', $this->allowedActions)))
            ->addArgument('module name', InputArgument::REQUIRED, 'Module on which the action will be executed')
            ->addArgument('file path', InputArgument::OPTIONAL, 'YML file path for configuration');
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->init($input, $output);

        $moduleName = $input->getArgument('module name');
        $action = $input->getArgument('action');
        $file = $input->getArgument('file path');

        if (!in_array($action, $this->allowedActions)) {
            $this->displayMessage(
                $this->translator->trans(
                    'Unknown module action. It must be one of these values: %actions%',
                    ['%actions%' => implode(' / ', $this->allowedActions)],
                    'Admin.Modules.Notification'
                ),
                'error'
            );

            return 1;
        }

        if ($action === 'configure') {
            $this->executeConfigureModuleAction($moduleName, $file);
        } else {
            $this->executeGenericModuleAction($action, $moduleName);
        }

        return 0;
    }

    protected function executeConfigureModuleAction($moduleName, $file = null)
    {
        $this->moduleSelfConfigurator->module($moduleName);
        if ($file) {
            $this->moduleSelfConfigurator->file($file);
        }

        // Check if validation passed and exit in case of errors
        $errors = $this->moduleSelfConfigurator->validate();
        if (!empty($errors)) {
            // Display errors as a list
            $errors = array_map(function ($val) { return '- ' . $val; }, $errors);
            // And add a default message at the top
            array_unshift($errors, $this->translator->trans(
                'Validation of configuration details failed:',
                [],
                'Admin.Modules.Notification'
            ));
            $this->displayMessage($errors, 'error');

            return;
        }

        // Actual configuration
        $this->moduleSelfConfigurator->configure();
        $this->displayMessage(
            $this->translator->trans('Configuration successfully applied.', [], 'Admin.Modules.Notification'),
            'info'
        );
    }

    protected function executeGenericModuleAction($action, $moduleName)
    {
        if ($this->moduleManager->{$action}($moduleName)) {
            $this->displayMessage(
                $this->translator->trans(
                    '%action% action on module %module% succeeded.',
                    [
                        '%action%' => ucfirst(AdminModuleDataProvider::ACTIONS_TRANSLATION_LABELS[$action]),
                        '%module%' => $moduleName, ],
                    'Admin.Modules.Notification'
                )
            );

            return;
        }

        $error = $this->moduleManager->getError($moduleName);
        $this->displayMessage(
            $this->translator->trans(
                'Cannot %action% module %module%. %error_details%',
                [
                    '%action%' => str_replace('_', ' ', $action),
                    '%module%' => $moduleName,
                    '%error_details%' => $error, ],
                'Admin.Modules.Notification'
            ),
            'error'
        );
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
