<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\GridView\CommandHandler;

use PrestaShop\PrestaShop\Adapter\GridView\GridViewProvider;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\GridView\Command\DeleteGridViewCommand;
use PrestaShop\PrestaShop\Core\Domain\GridView\CommandHandler\DeleteGridViewHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\View\GridViewCounter;
use PrestaShopBundle\Entity\Repository\AdminGridViewRepository;
use Psr\Cache\CacheItemPoolInterface;

#[AsCommandHandler]
final class DeleteGridViewHandler implements DeleteGridViewHandlerInterface
{
    public function __construct(
        private readonly AdminGridViewRepository $gridViewRepository,
        private readonly GridViewProvider $gridViewProvider,
        private readonly CacheItemPoolInterface $cache,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteGridViewCommand $command): void
    {
        $gridView = $this->gridViewProvider->getOwnedGridView($command->getGridViewId());
        $gridViewId = $gridView->getId();

        $this->gridViewRepository->remove($gridView);
        $this->cache->deleteItem(GridViewCounter::CACHE_KEY_PREFIX . $gridViewId);
    }
}
