<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\SearchEngine\QueryHandler;

use PrestaShop\PrestaShop\Adapter\SearchEngine\AbstractSearchEngineHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Exception\SearchEngineException;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Query\GetSearchEngineForEditing;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\QueryHandler\GetSearchEngineForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\QueryResult\SearchEngineForEditing;

/**
 * Handles query what gets search engine for editing.
 */
#[AsQueryHandler]
final class GetSearchEngineForEditingHandler extends AbstractSearchEngineHandler implements GetSearchEngineForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SearchEngineException
     */
    public function handle(GetSearchEngineForEditing $query): SearchEngineForEditing
    {
        $searchEngine = $this->getSearchEngine($query->getSearchEngineId());

        return new SearchEngineForEditing(
            $query->getSearchEngineId(),
            $searchEngine->server,
            $searchEngine->getvar
        );
    }
}
