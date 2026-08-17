<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\GridView\CommandHandler;

use PrestaShop\PrestaShop\Adapter\GridView\GridViewProvider;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\GridView\Command\EditGridViewCommand;
use PrestaShop\PrestaShop\Core\Domain\GridView\CommandHandler\EditGridViewHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\View\GridViewCounter;
use PrestaShop\PrestaShop\Core\Grid\View\GridViewDataSanitizer;
use PrestaShopBundle\Entity\Repository\AdminGridViewRepository;
use Psr\Cache\CacheItemPoolInterface;

#[AsCommandHandler]
final class EditGridViewHandler implements EditGridViewHandlerInterface
{
    public function __construct(
        private readonly AdminGridViewRepository $gridViewRepository,
        private readonly GridViewProvider $gridViewProvider,
        private readonly GridViewDataSanitizer $dataSanitizer,
        private readonly CacheItemPoolInterface $cache,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(EditGridViewCommand $command): void
    {
        $gridView = $this->gridViewProvider->getOwnedGridView($command->getGridViewId());

        if (null !== $command->getName()) {
            $gridView->setName($command->getName());
        }

        if (null !== $command->isShared()) {
            $gridView->setShared($command->isShared());
        }

        if (null !== $command->getDynamicDateRules()) {
            $searchCriteria = json_decode($gridView->getFilters(), true) ?: [];
            $gridView->setDynamicDateRules(
                $this->dataSanitizer->sanitizeDateRules($command->getDynamicDateRules(), $searchCriteria)
            );
        }

        $this->gridViewRepository->save($gridView);
        $this->cache->deleteItem(GridViewCounter::CACHE_KEY_PREFIX . $gridView->getId());
    }
}
