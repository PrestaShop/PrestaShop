<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Command;

use PrestaShop\PrestaShop\Adapter\Debug\DebugMode;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Configuration\Command\SwitchDebugModeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * CLI command for getting and setting debug mode setting
 */
class DebugCommand extends Command
{
    public const STATUS_OK = 0;
    public const STATUS_ERROR = 1;

    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var DebugMode
     */
    private $debugConfiguration;

    public function __construct(CommandBusInterface $commandBus, DebugMode $debugConfiguration)
    {
        parent::__construct();
        $this->commandBus = $commandBus;
        $this->debugConfiguration = $debugConfiguration;
    }

    protected function configure()
    {
        $this
            ->setName('prestashop:debug')
            ->setDescription('Get or set debug mode')
            ->addArgument('value', InputArgument::OPTIONAL, 'Value for debug mode, on/off, true/false, 1/0. If left out will just print the current state')
        ;
    }

    protected function printDebugState()
    {
        $this->io->success(sprintf('Debug mode is: %s', $this->debugConfiguration->isDebugModeEnabled() ? 'ON' : 'OFF'));
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        // no new value, just print out the current setting value
        $inputValue = $input->getArgument('value');
        if ($inputValue === null) {
            $this->printDebugState();

            return self::STATUS_OK;
        }

        // parse incoming to truthy
        $newValue = filter_var($inputValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($newValue === null) {
            $this->io->error(
                [
                    'Input cannot be determined as neither "true" or "false" by php "filter_var"',
                    'please check for valid values from: https://www.php.net/manual/en/filter.filters.validate.php section FILTER_VALIDATE_BOOLEAN',
                ]
            );

            return self::STATUS_ERROR;
        }

        $this->commandBus->handle(new SwitchDebugModeCommand($newValue));
        $this->printDebugState();

        return self::STATUS_OK;
    }
}
