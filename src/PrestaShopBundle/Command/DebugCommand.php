<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Command;

use Employee;
use PrestaShop\PrestaShop\Adapter\Debug\DebugMode;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Configuration\Command\SwitchDebugModeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CLI command for getting and setting debug mode setting
 */
class DebugCommand extends Command
{
    private const STATUS_OK = 0;
    private const STATUS_ERR = 1;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var FormatterHelper
     */
    protected $formatter;

    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var DebugMode
     */
    private $debugConfiguration;

    /**
     * @var LegacyContext
     */
    private $legacyContext;

    public function __construct(CommandBusInterface $commandBus, DebugMode $debugConfiguration, LegacyContext $legacyContext)
    {
        parent::__construct();
        $this->commandBus = $commandBus;
        $this->debugConfiguration = $debugConfiguration;
        $this->legacyContext = $legacyContext;
    }

    protected function configure()
    {
        $this
            ->setName('prestashop:debug')
            ->setDescription('Get or set debug mode')
            ->addArgument('value', InputArgument::OPTIONAL, 'Value for debug mode, on/off, true/false, 1/0. If left out will just print the current state')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input, $output);

        // no new value, just print out the current setting value
        $inval = $input->getArgument('value');
        if (is_null($inval)) {
            $output->writeln(sprintf('Debug mode is %s', $this->debugConfiguration->isDebugModeEnabled() ? 'on' : 'off'));

            return self::STATUS_OK;
        }

        // parse incoming to truthy
        $newval = filter_var($inval, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if (is_null($newval)) {
            $this->displayMessage('Value is not a valid truthy value', 'error');

            return self::STATUS_ERR;
        }

        $this->commandBus->handle(new SwitchDebugModeCommand($newval));
        $output->writeln(sprintf('Debug mode is now %s', $this->debugConfiguration->isDebugModeEnabled() ? 'on' : 'off'));

        return self::STATUS_OK;
    }

    protected function init(InputInterface $input, OutputInterface $output): void
    {
        $this->formatter = $this->getHelper('formatter');
        $this->input = $input;
        $this->output = $output;
        //We need to have an employee or the module hooks don't work
        //see LegacyHookSubscriber
        if (!$this->legacyContext->getContext()->employee) {
            //Even a non existing employee is fine
            $this->legacyContext->getContext()->employee = new Employee(42);
        }
    }

    protected function displayMessage(string $message, string $type = 'info'): void
    {
        $this->output->writeln(
            $this->formatter->formatBlock($message, $type, true)
        );
    }
}
