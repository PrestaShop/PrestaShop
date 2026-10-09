<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Command;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContext;
use PrestaShop\PrestaShop\Core\Context\ContextBuilderPreparer;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

abstract class AbstractModuleActionCommand extends AbstractModuleCommand
{
    public function __construct(
        TranslatorInterface $translator,
        LegacyContext $context,
        ContextBuilderPreparer $contextBuilderPreparer,
        Configuration $configuration,
        ShopContext $shopContext,
        protected readonly ModuleManager $moduleManager,
    ) {
        parent::__construct($translator, $context, $contextBuilderPreparer, $configuration, $shopContext);
    }

    abstract protected function getAction(): string;

    protected function configure(): void
    {
        $action = $this->getAction();

        $this
            ->setName(sprintf('prestashop:module:%s', $action))
            ->setDescription(sprintf('%s one or more modules', ucfirst($action)))
            ->addArgument(
                'modules',
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
        if (!$this->initializeContext($input, $output)) {
            return Command::INVALID;
        }

        $skipOverrides = (bool) $input->getOption('skip-overrides');
        $moduleNames = $input->getArgument('modules');
        $action = $this->getAction();
        $hasErrors = false;

        if ($skipOverrides) {
            $disableModuleOriginalValue = $this->configuration->get('PS_DISABLE_MODULE_OVERRIDES');
            $this->configuration->setTemporary('PS_DISABLE_MODULE_OVERRIDES', 1);
        }

        try {
            foreach ($moduleNames as $moduleName) {
                try {
                    // A failing module must not prevent the remaining modules
                    // in a bulk action from being processed.
                    if (!$this->executeModuleAction($action, $moduleName)) {
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

    private function executeModuleAction(string $action, string $moduleName): bool
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
}
