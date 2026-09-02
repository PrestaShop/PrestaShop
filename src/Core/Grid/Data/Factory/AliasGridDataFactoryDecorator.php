<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Adapter\Alias\Repository\AliasRepository;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/** Class decorates data from alias grid data factory by adding aliases for search terms. */
final class AliasGridDataFactoryDecorator implements GridDataFactoryInterface
{
    public function __construct(
        private GridDataFactoryInterface $aliasGridDataFactory,
        private AliasRepository $aliasRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria): GridData
    {
        $aliasData = $this->aliasGridDataFactory->getData($searchCriteria);

        $aliasRecords = $this->applyModifications($aliasData->getRecords());

        return new GridData(
            $aliasRecords,
            $aliasData->getRecordsTotal(),
            $aliasData->getQuery(),
        );
    }

    private function applyModifications(RecordCollectionInterface $records): RecordCollection
    {
        // Get search terms finded by main query
        $searchTermsList = array_column($records->all(), 'search');

        // Get all aliases related to all search terms retreive before.
        $aliasesDb = $this->aliasRepository->getAliasesBySearchTerms($searchTermsList);

        // Format aliases
        $aliasesByTerms = [];
        foreach ($aliasesDb as $alias) {
            $aliasesByTerms[$alias['search']][] = [
                'id_alias' => $alias['id_alias'],
                'alias' => $alias['alias'],
                'active' => $alias['active'],
            ];
        }

        // Then, we build an array that by used by the grid views
        // (each line must be an search term, and with a list of aliases)
        $searchTermsRecords = [];
        foreach ($searchTermsList as $searchTerm) {
            $searchTermsRecords[] = [
                'search' => $searchTerm,
                'aliases' => $aliasesByTerms[$searchTerm] ?? [],
            ];
        }

        return new RecordCollection($searchTermsRecords);
    }
}
