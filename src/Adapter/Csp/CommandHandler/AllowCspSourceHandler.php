<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp\CommandHandler;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\AllowCspSourceCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\CommandHandler\AllowCspSourceHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CannotAddCspRuleException;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspLogNotFoundException;
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
    ) {
    }

    public function handle(AllowCspSourceCommand $command): CspRuleId
    {
        $log = $this->cspLogRepository->find($command->getCspLogId());
        // A log outside the caller's shop scope is treated as missing,
        // so a crafted id can't curate another shop's policy.
        if (null === $log || !in_array($log->getShopId(), $this->shopListResolver->resolveShopIds($command->getShopConstraint()), true)) {
            throw new CspLogNotFoundException(sprintf('CSP log entry #%d was not found.', $command->getCspLogId()));
        }

        $existing = $this->cspRuleRepository->findOneByShopDirectiveSource($log->getShopId(), $log->getDirective(), $log->getSource());
        if (null !== $existing) {
            return new CspRuleId($existing->getId());
        }

        $rule = (new CspRule())
            ->setShopId($log->getShopId())
            ->setDirective($log->getDirective())
            ->setSource($log->getSource())
            ->setDateAdd(new DateTimeImmutable());

        try {
            return new CspRuleId($this->cspRuleRepository->add($rule));
        } catch (CannotAddCspRuleException $e) {
            // Two concurrent clicks race on the unique key; the loser returns the rule the winner inserted.
            $winner = $this->cspRuleRepository->findOneByShopDirectiveSource($log->getShopId(), $log->getDirective(), $log->getSource());
            if (null !== $winner) {
                return new CspRuleId($winner->getId());
            }

            throw $e;
        }
    }
}
