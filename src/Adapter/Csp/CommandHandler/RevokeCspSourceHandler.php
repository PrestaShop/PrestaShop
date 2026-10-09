<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Csp\CspPolicyCacheInterface;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\RevokeCspSourceCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\CommandHandler\RevokeCspSourceHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;

/**
 * @internal
 */
#[AsCommandHandler]
final class RevokeCspSourceHandler implements RevokeCspSourceHandlerInterface
{
    public function __construct(
        private readonly CspRuleRepository $repository,
        private readonly ShopListResolverInterface $shopListResolver,
        private readonly CspPolicyCacheInterface $policyCache,
    ) {
    }

    public function handle(RevokeCspSourceCommand $command): void
    {
        $ruleId = $command->getCspRuleId()->getValue();
        $rule = $this->repository->getById($ruleId);

        // A rule outside the caller's surface (global back office = shop id 0, storefront = shops in
        // scope) is treated as missing, so a crafted id can't revoke another surface's rule. The context
        // column is checked explicitly rather than relying on the shop-id-0 convention.
        $shopIds = CspContext::ADMIN === $command->getContext() ? [0] : $this->shopListResolver->resolveShopIds($command->getShopConstraint());
        if ($rule->getContext() !== $command->getContext()->value || !in_array($rule->getShopId(), $shopIds, true)) {
            throw new CspRuleNotFoundException(sprintf('CSP rule #%d was not found.', $ruleId));
        }

        $this->repository->delete($rule);

        // A storefront rule changes the cached policy for that shop; the back office is not cached.
        if (CspContext::FRONT->value === $rule->getContext()) {
            $this->policyCache->invalidate($rule->getShopId());
        }
    }
}
