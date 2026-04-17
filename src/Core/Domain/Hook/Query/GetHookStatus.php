<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Hook\Query;

use PrestaShop\PrestaShop\Core\Domain\Hook\ValueObject\HookId;

/**
 * Get current status (enabled/disabled) for a given hook
 */
class GetHookStatus
{
    private HookId $id;

    /**
     * GetHookStatus constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = new HookId($id);
    }

    /**
     * @return HookId
     */
    public function getId(): HookId
    {
        return $this->id;
    }
}
