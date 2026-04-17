<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\SearchEngine\Command;

use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Exception\SearchEngineException;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\ValueObject\SearchEngineId;

/**
 * Deletes search engines in bulk action.
 */
class BulkDeleteSearchEngineCommand
{
    /**
     * @var SearchEngineId[]
     */
    private $searchEngineIds = [];

    /**
     * @param int[] $searchEngineIds
     *
     * @throws SearchEngineException
     */
    public function __construct(array $searchEngineIds)
    {
        $this->setSearchEngineIds($searchEngineIds);
    }

    /**
     * @return SearchEngineId[]
     */
    public function getSearchEngineIds(): array
    {
        return $this->searchEngineIds;
    }

    /**
     * @param int[] $searchEngineIds
     *
     * @throws SearchEngineException
     */
    private function setSearchEngineIds(array $searchEngineIds): void
    {
        foreach ($searchEngineIds as $searchEngineId) {
            $this->searchEngineIds[] = new SearchEngineId((int) $searchEngineId);
        }
    }
}
