<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Core\CommandBus\Middleware;

use League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor;
use League\Tactician\Handler\Locator\HandlerLocator;
use League\Tactician\Middleware;
use PrestaShop\PrestaShop\Core\CommandBus\ExecutedCommandRegistry;
use PrestaShop\PrestaShop\Core\EnvironmentInterface;

/**
 * Registers every command that was executed when system is in debug mode.
 */
final class CommandRegisterMiddleware implements Middleware
{
    /**
     * @var HandlerLocator
     */
    private $handlerLocator;

    /**
     * @var CommandNameExtractor
     */
    private $commandNameExtractor;

    /**
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * @var ExecutedCommandRegistry
     */
    private $executedCommandRegistry;

    /**
     * @param EnvironmentInterface $environment
     * @param HandlerLocator $handlerLocator
     * @param CommandNameExtractor $commandNameExtractor
     * @param ExecutedCommandRegistry $executedCommandRegistry
     */
    public function __construct(
        EnvironmentInterface $environment,
        HandlerLocator $handlerLocator,
        CommandNameExtractor $commandNameExtractor,
        ExecutedCommandRegistry $executedCommandRegistry
    ) {
        $this->handlerLocator = $handlerLocator;
        $this->commandNameExtractor = $commandNameExtractor;
        $this->environment = $environment;
        $this->executedCommandRegistry = $executedCommandRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($command, callable $next)
    {
        if ($this->environment->isDebug()) {
            $commandName = $this->commandNameExtractor->extract($command);
            $handler = $this->handlerLocator->getHandlerForCommand($commandName);

            $this->executedCommandRegistry->register($command, $handler);
        }

        return $next($command);
    }
}
