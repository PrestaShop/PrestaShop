<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Query\GetSearchEngineForEditing;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\QueryResult\SearchEngineForEditing;

/**
 * Provides data for search engine add/edit form.
 */
final class SearchEngineFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param CommandBusInterface $queryBus
     */
    public function __construct(CommandBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($searchEngineId): array
    {
        /** @var SearchEngineForEditing $editableSearchEngine */
        $editableSearchEngine = $this->queryBus->handle(new GetSearchEngineForEditing($searchEngineId));

        return [
            'server' => $editableSearchEngine->getServer(),
            'query_key' => $editableSearchEngine->getQueryKey(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        return [];
    }
}
