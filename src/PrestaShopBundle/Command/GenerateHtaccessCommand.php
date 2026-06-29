<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Command;

use Exception;
use PrestaShop\PrestaShop\Adapter\File\HtaccessFileGenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to generate the .htaccess file
 *
 * Usage:
 *   bin/console prestashop:htaccess:generate [--force]
 *
 * Options:
 *   --force (-f): Force overwrite even if file exists
 *
 * Examples:
 *   bin/console prestashop:htaccess:generate
 *   bin/console prestashop:htaccess:generate --force
 */
#[AsCommand(
    name: 'prestashop:htaccess:generate',
    description: 'Generate the .htaccess file'
)]
class GenerateHtaccessCommand extends Command
{
    public function __construct(private HtaccessFileGenerator $htaccessFileGenerator)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force overwrite even if file exists');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $force = $input->getOption('force');
        $path = _PS_ROOT_DIR_ . '/.htaccess';

        if (file_exists($path) && !$force) {
            $output->writeln('<comment>.htaccess already exists. Use --force to overwrite.</comment>');

            return Command::SUCCESS;
        }

        try {
            $this->htaccessFileGenerator->generateFile();
            $output->writeln('<info>.htaccess successfully generated at ' . $path . '</info>');

            return Command::SUCCESS;
        } catch (Exception $e) {
            $output->writeln('<error>Failed to generate .htaccess: ' . $e->getMessage() . '</error>');
            if ($output->isVerbose()) {
                $output->writeln('<error>Stack trace: ' . $e->getTraceAsString() . '</error>');
            }

            return Command::FAILURE;
        }
    }
}
