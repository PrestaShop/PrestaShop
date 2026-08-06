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
use PrestaShop\PrestaShop\Core\Domain\Hook\Command\EditHookedModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Hook\CommandHandler\EditHookedModuleCommandHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\CannotUpdateHookException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\HookNotFoundException;
use Shop;
use Validate;

/**
 * @internal
 */
#[AsCommandHandler]
final class EditHookedModuleCommandHandler implements EditHookedModuleCommandHandlerInterface
{
    public function handle(EditHookedModuleCommand $command): void
    {
        $moduleId = $command->getModuleId();
        $hookId = $command->getHookId()->getValue();
        $newHookId = $command->getNewHookId()->getValue();

        $module = Module::getInstanceById($moduleId);
        if (!$module) {
            throw new CannotUpdateHookException(sprintf('Module with id "%d" cannot be loaded.', $moduleId));
        }

        $newHook = new Hook($newHookId);
        if ((int) $newHook->id !== $newHookId) {
            throw new HookNotFoundException(sprintf('Hook with id "%d" was not found.', $newHookId));
        }

        $shopIds = Shop::getContextListShopID();

        if ($newHookId !== $hookId) {
            if (!$module->registerHook($newHook->name, $shopIds)) {
                throw new CannotUpdateHookException(sprintf(
                    'Failed to register module "%d" on hook "%d".',
                    $moduleId,
                    $newHookId
                ));
            }

            if (!$module->unregisterHook($hookId, $shopIds) || !$module->unregisterExceptions($hookId, $shopIds)) {
                throw new CannotUpdateHookException(sprintf(
                    'Failed to unregister module "%d" from hook "%d".',
                    $moduleId,
                    $hookId
                ));
            }
        }

        $exceptions = $this->normalizeExceptions($command->getExceptions());
        if (!$module->editExceptions($newHookId, [$exceptions])) {
            throw new CannotUpdateHookException(sprintf(
                'Failed to update exceptions for module "%d" on hook "%d".',
                $moduleId,
                $newHookId
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
