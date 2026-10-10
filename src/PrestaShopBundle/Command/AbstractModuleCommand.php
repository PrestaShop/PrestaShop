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
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContext;
use PrestaShop\PrestaShop\Core\Context\ContextBuilderPreparer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractModuleCommand extends Command
{
    protected OutputInterface $output;

    public function __construct(
        protected readonly TranslatorInterface $translator,
        protected readonly LegacyContext $context,
        protected readonly ContextBuilderPreparer $contextBuilderPreparer,
        protected readonly Configuration $configuration,
        protected readonly ShopContext $shopContext,
        #[Autowire(service: 'prestashop.adapter.legacy_context_loader')]
        protected readonly LegacyContextLoader $legacyContextLoader,
    ) {
        parent::__construct();
    }

    protected function initializeContext(InputInterface $input, OutputInterface $output): bool
    {
        $this->output = $output;

        if ($this->hasShopContextOptionWithoutValue($input)) {
            $this->displayMessage(
                $this->translator->trans(
                    'The --id_shop and --id_shop_group options require a value.',
                    [],
                    'Admin.Modules.Notification'
                ),
                'error'
            );

            return false;
        }

        // Keep the explicit context selected by the console listener.
        // New module commands default to all shops only when none was requested.
        if ($this->hasExplicitShopContext($input)) {
            if (!$this->supportsExplicitShopContext()) {
                $this->displayMessage(
                    $this->translator->trans(
                        'This command has global effects and does not support --id_shop or --id_shop_group.',
                        [],
                        'Admin.Modules.Notification'
                    ),
                    'error'
                );

                return false;
            }
        } else {
            $this->shopContext->setAllContext(0);
        }

        // LegacyHookSubscriber only checks that an employee object is present.
        if (!$this->context->getContext()->employee) {
            $this->legacyContextLoader->loadEmployeeContext();
        }

        // We must initialize the language context because ModuleRepository depends on it for its cache key
        $this->contextBuilderPreparer->prepareLanguageId((int) $this->configuration->get('PS_LANG_DEFAULT'));

        return true;
    }

    // Each command must explicitly declare whether its action can be isolated to a shop context.
    abstract protected function supportsExplicitShopContext(): bool;

    protected function displayMessage(string|array $message, string $type = 'info'): void
    {
        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper('formatter');

        $this->output->writeln(
            $formatter->formatBlock($message, $type, true)
        );
    }

    private function hasExplicitShopContext(InputInterface $input): bool
    {
        // Global options are supplied by PrestaShopApplication.
        // Direct command tests do not merge the application definition.
        return ($input->hasOption('id_shop') && null !== $input->getOption('id_shop'))
            || ($input->hasOption('id_shop_group') && null !== $input->getOption('id_shop_group'));
    }

    private function hasShopContextOptionWithoutValue(InputInterface $input): bool
    {
        foreach (['id_shop', 'id_shop_group'] as $option) {
            if (
                $input->hasOption($option)
                && $input->hasParameterOption('--' . $option)
                && (null === $input->getOption($option) || '' === $input->getOption($option))
            ) {
                return true;
            }
        }

        return false;
    }
}
