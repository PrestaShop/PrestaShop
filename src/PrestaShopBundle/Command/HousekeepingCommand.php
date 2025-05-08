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

declare(strict_types=1);

namespace PrestaShopBundle\Command;

use DateTime;
use Exception;
use PrestaShop\PrestaShop\Adapter\Language\LanguageDataProvider;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShopLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class HousekeepingCommand extends Command
{
    // return values
    public const STATUS_OK = 0;
    public const STATUS_ERROR = -1;
    public const STATUS_INVALID_ACTION = -2;

    private $allowedActions = [
        'all',
        'logs',
        'connections',
        'guests',
    ];

    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var string|null
     */
    private $action;

    public function __construct(
        ShopConfigurationInterface $configuration,
        LanguageDataProvider $languageDataProvider,
    ) {
        parent::__construct();
        /*
        $this->configuration = $configuration;
        $this->languageDataProvider = $languageDataProvider;
        */
    }

    protected function configure(): void
    {
        $this
            ->setName('prestashop:housekeeping')
            ->setDescription('Housekeeping tasks, like removing old logs, etc.')
            ->addArgument('action', InputArgument::REQUIRED, sprintf('Action to execute (Allowed actions: %s).', implode(' / ', $this->allowedActions)))
        ;
    }

    protected function init(): void
    {
        // check our action
        $action = $this->input->getArgument('action');
        if (!in_array($action, $this->allowedActions)) {
            $msg = sprintf('Unknown action. It must be one of these values: %s', implode(' / ', $this->allowedActions));
            throw new Exception($msg, self::STATUS_INVALID_ACTION);
        }

        $this->action = $action;
    }

    /**
     * Main execute. Calls the method defined by action
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->input = $input;

        try {
            $this->init();
            $this->{$this->action}();
        } catch (Exception $e) {
            $this->io->error($e->getMessage());

            return $e->getCode();
        }

        return self::STATUS_OK;
    }

    /**
     * Remove old logs
     */
    public function logs(): void
    {
        PrestaShopLogger::eraseLogsBefore(
            (new DateTime())->modify('-1 month')
        );
        PrestaShopLogger::addLog(
            'Housekeeping cleared logs older than 1 month',
            PrestaShopLogger::LOG_SEVERITY_LEVEL_INFORMATIVE,
        );
    }

    public function all(): void
    {
        $this->logs();
        /*
        $this->connections();
        $this->guests();
        */
    }
}
