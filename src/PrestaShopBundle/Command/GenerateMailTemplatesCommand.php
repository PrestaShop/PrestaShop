<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Command;

use Employee;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command\GenerateThemeMailTemplatesCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateMailTemplatesCommand extends Command
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var LegacyContext
     */
    private $legacyContext;

    public function __construct(CommandBusInterface $commandBus, LegacyContext $legacyContext)
    {
        parent::__construct();
        $this->commandBus = $commandBus;
        $this->legacyContext = $legacyContext;
    }

    protected function configure()
    {
        $this
            ->setName('prestashop:mail:generate')
            ->setDescription('Generate mail templates for a specified theme')
            ->addArgument('theme', InputArgument::REQUIRED, 'Theme to use for mail templates.')
            ->addArgument('locale', InputArgument::REQUIRED, 'Which locale to use for the templates.')
            ->addArgument('coreOutputFolder', InputArgument::OPTIONAL, 'Output folder to export core templates.')
            ->addArgument('modulesOutputFolder', InputArgument::OPTIONAL, 'Output folder to export modules templates (by default same as core).')
            ->addOption('overwrite', 'o', InputOption::VALUE_OPTIONAL, 'Overwrite existing templates', false)
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $themeName = $input->getArgument('theme');
        $coreOutputFolder = $input->getArgument('coreOutputFolder');
        if (!empty($coreOutputFolder) && file_exists($coreOutputFolder)) {
            $coreOutputFolder = realpath($coreOutputFolder);
        }
        $modulesOutputFolder = $input->getArgument('modulesOutputFolder');
        if (!empty($modulesOutputFolder) && file_exists($modulesOutputFolder)) {
            $modulesOutputFolder = realpath($modulesOutputFolder);
        } else {
            $modulesOutputFolder = $coreOutputFolder;
        }
        $overwrite = false !== $input->getOption('overwrite');

        $this->initContext();

        $locale = $input->getArgument('locale');

        $output->writeln(sprintf('Exporting mail with theme %s for language %s', $themeName, $locale));

        /** @var GenerateThemeMailTemplatesCommand $generateCommand */
        $generateCommand = new GenerateThemeMailTemplatesCommand(
            $themeName,
            $locale,
            $overwrite,
            $coreOutputFolder ?: '',
            $modulesOutputFolder ?: ''
        );
        $this->commandBus->handle($generateCommand);

        return self::SUCCESS;
    }

    /**
     * Initialize PrestaShop Context
     */
    private function initContext()
    {
        // We need to have an employee or the module hooks don't work
        // see LegacyHookSubscriber
        if (!$this->legacyContext->getContext()->employee) {
            // Even a non existing employee is fine
            $this->legacyContext->getContext()->employee = new Employee(42);
        }
    }
}
