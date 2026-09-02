<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Hook\QueryHandler;

use Hook;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\HookNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHook;
use PrestaShop\PrestaShop\Core\Domain\Hook\QueryHandler\GetHookHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Hook\QueryResult\Hook as HookQueryResult;

#[AsQueryHandler]
final class GetHookHandler implements GetHookHandlerInterface
{
    public function handle(GetHook $query)
    {
        $hookId = $query->getId()->getValue();
        $hook = new Hook($hookId);

        if ($hook->id !== $hookId) {
            throw new HookNotFoundException(sprintf('Hook with id "%d" was not found.', $hookId));
        }

        return new HookQueryResult(
            $hook->id,
            (bool) $hook->active,
            $hook->name,
            $hook->title,
            $hook->description
        );
    }
}
