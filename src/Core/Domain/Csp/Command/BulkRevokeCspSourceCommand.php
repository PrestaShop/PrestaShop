<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Csp\Command;

use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/** Removes several sources from the curated allow-list in one action (the grid bulk "Revoke"). */
final class BulkRevokeCspSourceCommand
{
    /**
     * @var int[]
     */
    private readonly array $cspRuleIds;

    /**
     * @param int[] $cspRuleIds
     */
    public function __construct(
        array $cspRuleIds,
        private readonly ShopConstraint $shopConstraint,
        private readonly CspContext $context = CspContext::FRONT,
    ) {
        $this->cspRuleIds = array_map('intval', $cspRuleIds);
    }

    public function getContext(): CspContext
    {
        return $this->context;
    }

    /**
     * @return int[]
     */
    public function getCspRuleIds(): array
    {
        return $this->cspRuleIds;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
