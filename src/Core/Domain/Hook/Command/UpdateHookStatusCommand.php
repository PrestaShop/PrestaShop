<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Hook\Command;

use PrestaShop\PrestaShop\Core\Domain\Hook\ValueObject\HookId;

/**
 * Class UpdateHookStatusCommand update a given hook status
 */
class UpdateHookStatusCommand
{
    private HookId $id;

    /**
     * New hook status
     */
    private bool $active;

    /**
     * UpdateHookStatusCommand constructor.
     *
     * @param int $id
     * @param bool $active
     */
    public function __construct(int $id, bool $active)
    {
        $this->id = new HookId($id);
        $this->active = $active;
    }

    /**
     * @return HookId
     */
    public function getHookId(): HookId
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }
}
