<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Command;

use Employee;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Core\Context\ContextBuilderPreparer;
use PrestaShop\PrestaShop\Core\Module\ModuleCollection;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ModuleListCommand extends Command
{
    public function __construct(
        protected readonly LegacyContext $context,
        protected readonly ContextBuilderPreparer $contextBuilderPreparer,
        protected readonly Configuration $configuration,
        protected readonly ModuleRepository $moduleRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('prestashop:module:list')
            ->setDescription('List shop modules and their versions')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Include uninstalled modules.')
            ->addOption('active', null, InputOption::VALUE_NONE, 'Show only installed and enabled modules.')
            ->addOption('disabled', null, InputOption::VALUE_NONE, 'Show only installed but disabled modules.')
            ->addOption('not-installed', null, InputOption::VALUE_NONE, 'Show only modules present on disk or registered via hook but not installed.')
            ->addOption('simple', null, InputOption::VALUE_NONE, 'Output only technical names, one per line, instead of a table.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->initContext();

        $filterFlags = [
            'all' => (bool) $input->getOption('all'),
            'active' => (bool) $input->getOption('active'),
            'disabled' => (bool) $input->getOption('disabled'),
            'not-installed' => (bool) $input->getOption('not-installed'),
        ];

        $enabledFilters = array_keys(array_filter($filterFlags));
        if (count($enabledFilters) > 1) {
            $output->writeln(sprintf(
                '<error>The --%s options are mutually exclusive; only one may be passed at a time.</error>',
                implode(', --', $enabledFilters)
            ));

            return 1;
        }

        $filter = $enabledFilters[0] ?? 'installed';
        $modules = $this->resolveModules($filter);

        if ($input->getOption('simple')) {
            $names = [];
            foreach ($modules as $module) {
                $names[] = (string) $module->get('name');
            }
            usort($names, 'strcasecmp');
            foreach ($names as $name) {
                $output->writeln($name);
            }

            return 0;
        }

        $rows = [];
        foreach ($modules as $module) {
            $rows[] = [
                (string) $module->get('name'),
                $module->isInstalled() ? (string) $module->get('version') : '-',
                $this->describeModuleStatus($module),
            ];
        }
        usort($rows, static fn (array $a, array $b) => strcasecmp($a[0], $b[0]));

        $table = new Table($output);
        $table->setHeaders(['Name', 'Version', 'Status']);
        $table->setRows($rows);
        $table->render();

        return 0;
    }

    private function initContext(): void
    {
        // We need an employee or some legacy module hooks blow up (see LegacyHookSubscriber).
        // The permission filter on ModuleRepository::filterModulesByPermissions() is
        // bypassed in CLI by PHPCli::isPHPCli(), so a non-existing employee is harmless.
        if (!$this->context->getContext()->employee) {
            $this->context->getContext()->employee = new Employee(42);
        }

        // ModuleRepository keys its cache by language; the language context must be initialised.
        $this->contextBuilderPreparer->prepareLanguageId($this->configuration->get('PS_LANG_DEFAULT'));
    }

    private function resolveModules(string $filter): ModuleCollection
    {
        return match ($filter) {
            'all' => $this->moduleRepository->getList(),
            'not-installed' => $this->moduleRepository->getList()->filter(
                static fn (Module $module) => !$module->isInstalled()
            ),
            'active' => $this->moduleRepository->getInstalledModules()->filter(
                static fn (Module $module) => $module->isActive()
            ),
            'disabled' => $this->moduleRepository->getInstalledModules()->filter(
                static fn (Module $module) => !$module->isActive()
            ),
            default => $this->moduleRepository->getInstalledModules(),
        };
    }

    private function describeModuleStatus(Module $module): string
    {
        if (!$module->isInstalled()) {
            return 'Not installed';
        }

        return $module->isActive() ? 'Enabled' : 'Disabled';
    }
}
