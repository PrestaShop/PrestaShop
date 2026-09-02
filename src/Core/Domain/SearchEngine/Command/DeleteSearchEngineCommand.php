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
 * Deletes search engine.
 */
class DeleteSearchEngineCommand
{
    /**
     * @var SearchEngineId
     */
    private $searchEngineId;

    /**
     * @param int $searchEngineId
     *
     * @throws SearchEngineException
     */
    public function __construct(int $searchEngineId)
    {
        $this->searchEngineId = new SearchEngineId($searchEngineId);
    }

    /**
     * @return SearchEngineId
     */
    public function getSearchEngineId(): SearchEngineId
    {
        return $this->searchEngineId;
    }
}
