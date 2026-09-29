<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Csp\Command;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/** Promotes a collected violation to the allow-list (the grid "Allow" action); details come from the referenced log row. */
final class AllowCspSourceCommand
{
    public function __construct(
        private readonly int $cspLogId,
        private readonly ShopConstraint $shopConstraint,
    ) {
    }

    public function getCspLogId(): int
    {
        return $this->cspLogId;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
