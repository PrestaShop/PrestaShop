<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\RevokeCspSourceCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\CommandHandler\RevokeCspSourceHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspRuleNotFoundException;
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
    ) {
    }

    public function handle(RevokeCspSourceCommand $command): void
    {
        $ruleId = $command->getCspRuleId()->getValue();
        $rule = $this->repository->getById($ruleId);

        // A rule outside the caller's shop scope is treated as missing,
        // so a crafted id can't revoke another shop's rule.
        if (!in_array($rule->getShopId(), $this->shopListResolver->resolveShopIds($command->getShopConstraint()), true)) {
            throw new CspRuleNotFoundException(sprintf('CSP rule #%d was not found.', $ruleId));
        }

        $this->repository->delete($rule);
    }
}
