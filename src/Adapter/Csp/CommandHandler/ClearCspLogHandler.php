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
        // Clear every shop the constraint covers (one, a group, or all shops).
        foreach ($this->shopListResolver->resolveShopIds($command->getShopConstraint()) as $shopId) {
            $this->recorder->clear($shopId);
        }
    }
}
