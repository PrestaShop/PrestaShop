<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\CommandBus;

use PrestaShop\PrestaShop\Core\CommandBus\Parser\CommandTypeParser;

/**
 * Stores information about executed commands/queries
 */
final class ExecutedCommandRegistry
{
    private const BACKTRACE_LIMIT = 15;

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
            if ($step['function'] === 'handle'
                && is_a($step['class'], CommandBusInterface::class, true)
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
