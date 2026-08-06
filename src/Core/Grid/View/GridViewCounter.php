<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\View;

use PrestaShop\PrestaShop\Core\Grid\GridFactoryProvider;
use PrestaShopBundle\Entity\AdminGridView;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class GridViewCounter
{
    public const CACHE_KEY_PREFIX = 'grid_view_count_';
    public const CACHE_TTL = 60;

    /**
     * @param GridFactoryProvider $gridFactoryProvider
     * @param GridViewSearchCriteriaFactory $searchCriteriaFactory
     * @param LoggerInterface $logger
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(
        private readonly GridFactoryProvider $gridFactoryProvider,
        private readonly GridViewSearchCriteriaFactory $searchCriteriaFactory,
        private readonly LoggerInterface $logger,
        private readonly CacheItemPoolInterface $cache,
    ) {
    }

    /**
     * @param AdminGridView[] $gridViews
     *
     * @return array<int, int|null>
     */
    public function countRecords(array $gridViews): array
    {
        $counts = [];

        foreach ($gridViews as $gridView) {
            $counts[$gridView->getId()] = $this->countViewRecords($gridView);
        }

        return $counts;
    }

    /**
     * @param AdminGridView $gridView
     *
     * @return int|null
     */
    private function countViewRecords(AdminGridView $gridView): ?int
    {
        $gridFactory = $this->gridFactoryProvider->getFactory($gridView->getGridConfiguration()->getGridId());

        if (null === $gridFactory) {
            return null;
        }

        $cacheItem = $this->cache->getItem(self::CACHE_KEY_PREFIX . $gridView->getId());
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        try {
            $recordsTotal = $gridFactory
                ->getGrid($this->searchCriteriaFactory->build($gridView, ['limit' => 1, 'offset' => 0]))
                ->getData()
                ->getRecordsTotal()
            ;

            $cacheItem->set($recordsTotal)->expiresAfter(self::CACHE_TTL);
            $this->cache->save($cacheItem);

            return $recordsTotal;
        } catch (Throwable $e) {
            $this->logger->warning('Unable to count the records of grid view #{gridViewId}: {message}', [
                'gridViewId' => $gridView->getId(),
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return null;
        }
    }
}
