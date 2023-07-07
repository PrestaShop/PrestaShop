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

namespace PrestaShop\PrestaShop\Core\CommandBus;

use PrestaShop\PrestaShop\Core\CommandBus\Parser\CommandTypeParser;
use PrestaShopBundle\CommandBus\MessengerCommandBusAdapter;

/**
 * Stores information about executed commands/queries
 */
final class ExecutedCommandRegistry
{
    private const BACKTRACE_LIMIT = 10;

    /**
     * @var array
     */
    private $registry = [
        'commands' => [],
        'queries' => [],
    ];

    /**
     * @var CommandTypeParser
     */
    private $commandTypeParser;

    /**
     * @param CommandTypeParser $commandTypeParser
     */
    public function __construct(CommandTypeParser $commandTypeParser)
    {
        $this->commandTypeParser = $commandTypeParser;
    }

    /**
     * @param object $command
     * @param object $handler
     */
    public function register($command, $handler): void
    {
        $commandClass = $command::class;
        $handlerClass = $handler::class;

        $type = $this->commandTypeParser->parse($commandClass);

        $trace = $this->getTrace();

        switch ($type) {
            case 'Command':
                $this->registry['commands'][] = [
                    'command' => $commandClass,
                    'command_handler' => $handlerClass,
                    'trace' => $trace,
                ];
                break;
            case 'Query':
                $this->registry['queries'][] = [
                    'query' => $commandClass,
                    'query_handler' => $handlerClass,
                    'trace' => $trace,
                ];
                break;
        }
    }

    /**
     * @return array
     */
    public function getExecutedCommands(): array
    {
        return $this->registry['commands'];
    }

    /**
     * @return array
     */
    public function getExecutedQueries(): array
    {
        return $this->registry['queries'];
    }

    /**
     * Returns the file and line that invoked the handle method
     *
     * @return array
     */
    private function getTrace(): array
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, self::BACKTRACE_LIMIT);

        foreach ($trace as $step) {
            if ($step['class'] === MessengerCommandBusAdapter::class
                && $step['function'] === 'handle'
            ) {
                return [
                    'file' => $step['file'],
                    'line' => $step['line'],
                ];
            }
        }

        return [
            'file' => 'Unknown',
            'line' => 0,
        ];
    }
}
