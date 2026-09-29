<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Csp\Command;

use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspRuleId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/** Removes one source from the curated allow-list (the grid "Revoke" action). */
final class RevokeCspSourceCommand
{
    public function __construct(
        private readonly int $cspRuleId,
        private readonly ShopConstraint $shopConstraint,
    ) {
    }

    public function getCspRuleId(): CspRuleId
    {
        return new CspRuleId($this->cspRuleId);
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
