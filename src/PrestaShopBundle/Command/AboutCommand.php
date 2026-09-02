<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Command;

use PrestaShop\PrestaShop\Adapter\Configuration;
use Symfony\Bundle\FrameworkBundle\Command\AboutCommand as BaseAboutCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * This command displays information about the current PrestaShop installation.
 * It extends the default Symfony about command to include project-specific details.
 */
#[AsCommand(name: 'about', description: 'Display information about the current project')]
class AboutCommand extends BaseAboutCommand
{
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        parent::__construct();
        $this->configuration = $configuration;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $io = new SymfonyStyle($input, $output);

        $rows = [
            ['<info>PrestaShop</info>'],
            new TableSeparator(),
            ['Version', _PS_VERSION_],
            ['Debug mode', _PS_MODE_DEV_ ? 'true' : 'false'],
            ['Smarty Cache', $this->configuration->get('PS_SMARTY_CACHE') ? 'true' : 'false'],
        ];

        $io->table([], $rows);

        return self::SUCCESS;
    }
}
