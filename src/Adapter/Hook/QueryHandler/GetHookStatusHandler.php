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
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHookStatus;
use PrestaShop\PrestaShop\Core\Domain\Hook\QueryHandler\GetHookStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Hook\QueryResult\HookStatus;

/**
 * @internal
 */
#[AsQueryHandler]
final class GetHookStatusHandler implements GetHookStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetHookStatus $query)
    {
        $hookId = $query->getId()->getValue();
        $hook = new Hook($hookId);

        if ($hook->id !== $hookId) {
            throw new HookNotFoundException(sprintf('Hook with id "%d" was not found.', $hookId));
        }

        return new HookStatus($hook->id, (bool) $hook->active);
    }
}
