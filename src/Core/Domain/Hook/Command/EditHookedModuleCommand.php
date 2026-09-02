<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Hook\Command;

use PrestaShop\PrestaShop\Core\Domain\Hook\ValueObject\HookId;

/**
 * Edits an existing module-hook registration: moves to a new hook and/or updates exception pages.
 */
class EditHookedModuleCommand
{
    private int $moduleId;
    private HookId $hookId;
    private HookId $newHookId;
    private array $exceptions;

    /**
     * @param int $moduleId
     * @param int $hookId Current hook the module is registered on
     * @param int $newHookId Target hook (may be the same as $hookId to update exceptions only)
     * @param array $exceptions Filenames of pages where the module must NOT be displayed
     */
    public function __construct(int $moduleId, int $hookId, int $newHookId, array $exceptions = [])
    {
        $this->moduleId = $moduleId;
        $this->hookId = new HookId($hookId);
        $this->newHookId = new HookId($newHookId);
        $this->exceptions = $exceptions;
    }

    public function getModuleId(): int
    {
        return $this->moduleId;
    }

    public function getHookId(): HookId
    {
        return $this->hookId;
    }

    public function getNewHookId(): HookId
    {
        return $this->newHookId;
    }

    /**
     * @return string[]
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }
}
