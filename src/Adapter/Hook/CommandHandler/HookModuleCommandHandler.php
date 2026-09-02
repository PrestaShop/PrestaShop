<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Hook\CommandHandler;

use Hook;
use Module;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Hook\Command\HookModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Hook\CommandHandler\HookModuleCommandHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\CannotUpdateHookException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\HookNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\ModuleAlreadyHookedException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\ModuleCannotBeHookedException;
use Shop;
use Validate;

/**
 * @internal
 */
#[AsCommandHandler]
final class HookModuleCommandHandler implements HookModuleCommandHandlerInterface
{
    public function handle(HookModuleCommand $command): void
    {
        $moduleId = $command->getModuleId();
        $hookId = $command->getHookId()->getValue();

        $module = Module::getInstanceById($moduleId);
        if (!$module) {
            throw new CannotUpdateHookException(sprintf('Module with id "%d" cannot be loaded.', $moduleId));
        }

        $hook = new Hook($hookId);
        if ((int) $hook->id !== $hookId) {
            throw new HookNotFoundException(sprintf('Hook with id "%d" was not found.', $hookId));
        }

        if (Hook::getModulesFromHook($hookId, $moduleId)) {
            throw new ModuleAlreadyHookedException(sprintf(
                'Module "%d" is already registered on hook "%d".',
                $moduleId,
                $hookId
            ));
        }

        if (!$module->isHookableOn($hook->name)) {
            throw new ModuleCannotBeHookedException(sprintf(
                'Module "%d" cannot be hooked to hook "%s".',
                $moduleId,
                $hook->name
            ));
        }

        if (!$module->registerHook($hook->name, Shop::getContextListShopID())) {
            throw new CannotUpdateHookException(sprintf(
                'Failed to register module "%d" on hook "%d".',
                $moduleId,
                $hookId
            ));
        }

        $exceptions = $this->normalizeExceptions($command->getExceptions());
        if (!empty($exceptions) && !$module->registerExceptions($hookId, $exceptions, Shop::getContextListShopID())) {
            throw new CannotUpdateHookException(sprintf(
                'Failed to register exceptions for module "%d" on hook "%d".',
                $moduleId,
                $hookId
            ));
        }
    }

    /**
     * Filters out empty entries and validates each filename.
     *
     * @param string[] $exceptions
     *
     * @return string[]
     *
     * @throws CannotUpdateHookException
     */
    private function normalizeExceptions(array $exceptions): array
    {
        $normalized = [];
        foreach ($exceptions as $filename) {
            $filename = trim($filename);
            if ($filename === '') {
                continue;
            }
            if (!Validate::isFileName($filename)) {
                throw new CannotUpdateHookException(sprintf('Invalid exception filename "%s".', $filename));
            }
            $normalized[] = $filename;
        }

        return array_unique($normalized);
    }
}
