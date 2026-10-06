<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Csp\CspViolationRecorder;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\ClearCspLogCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\CommandHandler\ClearCspLogHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;

/**
 * @internal
 */
#[AsCommandHandler]
final class ClearCspLogHandler implements ClearCspLogHandlerInterface
{
    public function __construct(
        private readonly CspViolationRecorder $recorder,
        private readonly ShopListResolverInterface $shopListResolver,
    ) {
    }

    public function handle(ClearCspLogCommand $command): void
    {
        // The back office is one global surface (shop id 0); the storefront clears every shop the
        // constraint covers (one, a group, or all shops).
        $shopIds = CspContext::ADMIN === $command->getContext() ? [0] : $this->shopListResolver->resolveShopIds($command->getShopConstraint());
        foreach ($shopIds as $shopId) {
            $this->recorder->clear($command->getContext(), $shopId);
        }
    }
}
