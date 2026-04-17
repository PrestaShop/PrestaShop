<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Hook\Query;

use PrestaShop\PrestaShop\Core\Domain\Hook\ValueObject\HookId;

class GetHook
{
    private HookId $id;

    public function __construct(int $id)
    {
        $this->id = new HookId($id);
    }

    public function getId(): HookId
    {
        return $this->id;
    }
}
