<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp\CommandHandler;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Adapter\Csp\CspRulesSnapshotInterface;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\AllowCspSourceCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\CommandHandler\AllowCspSourceHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CannotAddCspRuleException;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspLogNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspRuleId;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use PrestaShopBundle\Entity\CspRule;
use PrestaShopBundle\Entity\Repository\CspLogRepository;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;

/**
 * Promotes a collected violation to the allow-list; idempotent, so a double click returns the existing rule.
 *
 * @internal
 */
#[AsCommandHandler]
final class AllowCspSourceHandler implements AllowCspSourceHandlerInterface
{
    public function __construct(
        private readonly CspLogRepository $cspLogRepository,
        private readonly CspRuleRepository $cspRuleRepository,
        private readonly ShopListResolverInterface $shopListResolver,
        private readonly CspRulesSnapshotInterface $rulesSnapshot,
    ) {
    }

    public function handle(AllowCspSourceCommand $command): CspRuleId
    {
        $log = $this->cspLogRepository->find($command->getCspLogId());
        // The back office is the single global surface (shop id 0); the storefront resolves to the shops
        // in scope. A log outside the caller's surface is treated as missing, so a crafted id can't curate
        // another surface's policy. The context column is checked explicitly rather than relying on the
        // shop-id-0 convention to tell the surfaces apart.
        $shopIds = CspContext::ADMIN === $command->getContext() ? [0] : $this->shopListResolver->resolveShopIds($command->getShopConstraint());
        if (null === $log
            || $log->getContext() !== $command->getContext()->value
            || !in_array($log->getShopId(), $shopIds, true)
        ) {
            throw new CspLogNotFoundException(sprintf('CSP log entry #%d was not found.', $command->getCspLogId()));
        }

        // The new rule inherits the surface (front/admin) of the violation it was promoted from.
        $context = CspContext::from($log->getContext());

        $existing = $this->cspRuleRepository->findOneByShopDirectiveSource($context, $log->getShopId(), $log->getDirective(), $log->getSource());
        if (null !== $existing) {
            $this->afterAllowed($context, $log->getShopId(), $log->getDirective(), $log->getSource());

            return new CspRuleId($existing->getId());
        }

        $rule = (new CspRule())
            ->setShopId($log->getShopId())
            ->setContext($log->getContext())
            ->setDirective($log->getDirective())
            ->setSource($log->getSource())
            ->setDateAdd(new DateTimeImmutable());

        try {
            $ruleId = new CspRuleId($this->cspRuleRepository->add($rule));
            $this->afterAllowed($context, $log->getShopId(), $log->getDirective(), $log->getSource());

            return $ruleId;
        } catch (CannotAddCspRuleException $e) {
            // Two concurrent clicks race on the unique key; the loser returns the rule the winner inserted.
            $winner = $this->cspRuleRepository->findOneByShopDirectiveSource($context, $log->getShopId(), $log->getDirective(), $log->getSource());
            if (null !== $winner) {
                $this->afterAllowed($context, $log->getShopId(), $log->getDirective(), $log->getSource());

                return new CspRuleId($winner->getId());
            }

            throw $e;
        }
    }

    private function afterAllowed(CspContext $context, int $shopId, string $directive, string $source): void
    {
        // The source is now on the allow-list: the grid hides its collected rows (NOT EXISTS), so they
        // only consume the row cap. Delete them; new reports bring them back if the rule is revoked.
        $this->cspLogRepository->deleteByShopDirectiveSource($context, $shopId, $directive, $source);

        // A storefront rule changes the shop's cached rules snapshot; the back office is not snapshotted.
        if (CspContext::FRONT === $context) {
            $this->rulesSnapshot->refresh($shopId);
        }
    }
}
