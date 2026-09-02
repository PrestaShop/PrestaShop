<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DataCollector;

use PrestaShop\PrestaShop\Core\CommandBus\ExecutedCommandRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Throwable;

/**
 * Collects data about dispatched Commands/Queries during request
 */
final class CommandsAndQueriesDataCollector extends DataCollector
{
    /**
     * @var ExecutedCommandRegistry
     */
    private $executedCommandRegistry;

    /**
     * @param ExecutedCommandRegistry $executedCommandRegistry
     */
    public function __construct(ExecutedCommandRegistry $executedCommandRegistry)
    {
        $this->executedCommandRegistry = $executedCommandRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, ?Throwable $exception = null)
    {
        $this->data = [
            'executed_commands' => $this->executedCommandRegistry->getExecutedCommands(),
            'executed_queries' => $this->executedCommandRegistry->getExecutedQueries(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ps.commands_and_queries_collector';
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->data = [];
    }

    /**
     * @return array
     */
    public function getExecutedCommands()
    {
        return $this->data['executed_commands'];
    }

    /**
     * @return array
     */
    public function getExecutedQueries()
    {
        return $this->data['executed_queries'];
    }
}
