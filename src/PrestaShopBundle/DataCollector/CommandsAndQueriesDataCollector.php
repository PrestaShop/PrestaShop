<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\DataCollector;

use PrestaShop\PrestaShop\Core\CommandBus\ExecutedCommandRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

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
    public function collect(Request $request, Response $response, \Exception $exception = null)
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
