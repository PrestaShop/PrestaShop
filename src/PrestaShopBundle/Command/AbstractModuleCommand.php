<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Command;

use Employee;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Context\ContextBuilderPreparer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractModuleCommand extends Command
{
    protected OutputInterface $output;

    public function __construct(
        protected readonly TranslatorInterface $translator,
        protected readonly LegacyContext $context,
        protected readonly ContextBuilderPreparer $contextBuilderPreparer,
        protected readonly Configuration $configuration,
    ) {
        parent::__construct();
    }

    protected function initializeContext(InputInterface $input, OutputInterface $output): void
    {
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

    protected function displayMessage($message, string $type = 'info'): void
    {
        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper('formatter');

        $this->output->writeln(
            $formatter->formatBlock($message, $type, true)
        );
    }
}
