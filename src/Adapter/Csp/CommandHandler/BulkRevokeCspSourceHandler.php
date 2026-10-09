<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Csp\CspRulesSnapshotInterface;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\BulkRevokeCspSourceCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\CommandHandler\BulkRevokeCspSourceHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CannotBulkRevokeCspRuleException;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspException;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspRuleId;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;

/**
 * Revokes several rules in one action, continuing past individual failures and reporting them at the end.
 *
 * @internal
 */
#[AsCommandHandler]
final class BulkRevokeCspSourceHandler extends AbstractBulkCommandHandler implements BulkRevokeCspSourceHandlerInterface
{
    public function __construct(
        private readonly CspRuleRepository $repository,
        private readonly ShopListResolverInterface $shopListResolver,
        private readonly CspRulesSnapshotInterface $rulesSnapshot,
    ) {
    }

    public function handle(BulkRevokeCspSourceCommand $command): void
    {
        $this->handleBulkAction($command->getCspRuleIds(), CspException::class, $command);
    }

    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        $ruleId = (new CspRuleId((int) $id))->getValue();
        $rule = $this->repository->getById($ruleId);

        // A rule outside the caller's surface (global back office = shop id 0, storefront = shops in
        // scope) is treated as missing, so a crafted id can't reach another surface's rules. The context
        // column is checked explicitly rather than relying on the shop-id-0 convention.
        $shopIds = CspContext::ADMIN === $command->getContext() ? [0] : $this->shopListResolver->resolveShopIds($command->getShopConstraint());
        if ($rule->getContext() !== $command->getContext()->value || !in_array($rule->getShopId(), $shopIds, true)) {
            throw new CspRuleNotFoundException(sprintf('CSP rule #%d was not found.', $ruleId));
        }

        $this->repository->delete($rule);

        // A storefront rule changes the shop's rules snapshot; the back office is not snapshotted.
        if (CspContext::FRONT->value === $rule->getContext()) {
            $this->rulesSnapshot->refresh($rule->getShopId());
        }
    }

    protected function supports($id): bool
    {
        return is_int($id);
    }

    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new CannotBulkRevokeCspRuleException($caughtExceptions, 'Failed to revoke some of the selected CSP rules.');
    }
}
