<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;

/**
 * Stores the storefront allow-list snapshot as a per-shop configuration value (PS_CSP_FRONT_RULES_<shop>,
 * at the all-shops scope — the shop id is in the key). See {@see CspRulesSnapshotInterface}.
 */
final class CspRulesSnapshot implements CspRulesSnapshotInterface
{
    public function __construct(
        private readonly CspRuleRepository $ruleRepository,
        private readonly ShopConfigurationInterface $configuration,
    ) {
    }

    public function get(int $shopId): array
    {
        $raw = $this->configuration->get($this->key($shopId), '', ShopConstraint::allShops());
        if (!is_string($raw) || '' === $raw) {
            return $this->build($shopId);
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : $this->build($shopId);
    }

    public function refresh(int $shopId): void
    {
        $this->build($shopId);
    }

    /**
     * @return array<string, list<string>>
     */
    private function build(int $shopId): array
    {
        $snapshot = [];
        foreach ($this->ruleRepository->getRulesByShop(CspContext::FRONT, $shopId) as $rule) {
            $snapshot[$rule['directive']][] = $rule['source'];
        }

        $this->configuration->set($this->key($shopId), (string) json_encode($snapshot), ShopConstraint::allShops());

        return $snapshot;
    }

    private function key(int $shopId): string
    {
        return 'PS_CSP_FRONT_RULES_' . $shopId;
    }
}
