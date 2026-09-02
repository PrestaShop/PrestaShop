<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\SearchEngine\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\SearchEngine\ValueObject\SearchEngineId;

/**
 * Transfers editable search engine data.
 */
class SearchEngineForEditing
{
    /**
     * @var SearchEngineId
     */
    private $searchEngineId;

    /**
     * @var string
     */
    private $server;

    /**
     * @var string
     */
    private $queryKey;

    /**
     * @param SearchEngineId $searchEngineId
     * @param string $server
     * @param string $queryKey
     */
    public function __construct(SearchEngineId $searchEngineId, string $server, string $queryKey)
    {
        $this->searchEngineId = $searchEngineId;
        $this->server = $server;
        $this->queryKey = $queryKey;
    }

    /**
     * @return SearchEngineId
     */
    public function getSearchEngineId(): SearchEngineId
    {
        return $this->searchEngineId;
    }

    /**
     * @return string
     */
    public function getServer(): string
    {
        return $this->server;
    }

    /**
     * @return string
     */
    public function getQueryKey(): string
    {
        return $this->queryKey;
    }
}
