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
 * Edits given search engine with provided data.
 */
class EditSearchEngineCommand
{
    /**
     * @var SearchEngineId
     */
    private $searchEngineId;

    /**
     * @var string|null
     */
    private $server;

    /**
     * @var string|null
     */
    private $queryKey;

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

    /**
     * @return string|null
     */
    public function getServer(): ?string
    {
        return $this->server;
    }

    /**
     * @param string|null $server
     *
     * @return self
     */
    public function setServer(?string $server): self
    {
        $this->server = $server;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getQueryKey(): ?string
    {
        return $this->queryKey;
    }

    /**
     * @param string|null $queryKey
     *
     * @return self
     */
    public function setQueryKey(?string $queryKey): self
    {
        $this->queryKey = $queryKey;

        return $this;
    }
}
