<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Hook\QueryHandler;

use Module;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\CannotUpdateHookException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetPossibleHooksForModule;
use PrestaShop\PrestaShop\Core\Domain\Hook\QueryHandler\GetPossibleHooksForModuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Hook\QueryResult\HookableInfo;

/**
 * @internal
 */
#[AsQueryHandler]
final class GetPossibleHooksForModuleHandler implements GetPossibleHooksForModuleHandlerInterface
{
    /**
     * @return HookableInfo[]
     */
    public function handle(GetPossibleHooksForModule $query): array
    {
        $moduleId = $query->getModuleId();
        $module = Module::getInstanceById($moduleId);

        if (!$module) {
            throw new CannotUpdateHookException(sprintf('Module with id "%d" cannot be loaded.', $moduleId));
        }

        $result = [];
        foreach ($module->getPossibleHooksList() as $hook) {
            $result[] = new HookableInfo(
                (int) $hook['id_hook'],
                (string) $hook['name'],
                (string) $hook['title'],
                (bool) $hook['registered']
            );
        }

        return $result;
    }
}
