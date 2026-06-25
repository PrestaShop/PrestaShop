<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Hook\Command;

use PrestaShop\PrestaShop\Core\Domain\Hook\ValueObject\HookId;

/**
 * Hooks a module to a hook, with optional exception pages.
 */
class HookModuleCommand
{
    private int $moduleId;
    private HookId $hookId;
    private array $exceptions;

    /**
     * @param int $moduleId
     * @param int $hookId
     * @param array $exceptions Filenames of pages where the module must NOT be displayed
     */
    public function __construct(int $moduleId, int $hookId, array $exceptions = [])
    {
        $this->moduleId = $moduleId;
        $this->hookId = new HookId($hookId);
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

    /**
     * @return string[]
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }
}
