<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
