<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\SearchEngine;

use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Exception\SearchEngineException;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Exception\SearchEngineNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\ValueObject\SearchEngineId;
use PrestaShopException;
use SearchEngine;

abstract class AbstractSearchEngineHandler
{
    /**
     * Gets legacy search engine.
     *
     * @param SearchEngineId $searchEngineId
     *
     * @return SearchEngine
     *
     * @throws SearchEngineException
     */
    protected function getSearchEngine(SearchEngineId $searchEngineId): SearchEngine
    {
        try {
            $searchEngine = new SearchEngine($searchEngineId->getValue());
        } catch (PrestaShopException $e) {
            throw new SearchEngineException('Failed to retrieve new search engine', 0, $e);
        }

        if ($searchEngine->id !== $searchEngineId->getValue()) {
            throw new SearchEngineNotFoundException(sprintf('Search engine with id "%d" not found', $searchEngineId->getValue()));
        }

        return $searchEngine;
    }
}
