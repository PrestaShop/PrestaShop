<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\DataProvider;

use PDO;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineQueryBuilderInterface;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShopBundle\Service\Hook\HookDispatcher;

/**
 * Class DoctrineGridDataProvider is responsible for returning grid data using Doctrine query builders
 */
final class DoctrineGridDataProvider implements GridDataProviderInterface
{
    /**
     * @var DoctrineQueryBuilderInterface
     */
    private $gridQueryBuilder;

    /**
     * @var HookDispatcher
     */
    private $hookDispatcher;

    /**
     * @var string
     */
    private $gridId;

    /**
     * @param DoctrineQueryBuilderInterface $gridQueryBuilder
     * @param HookDispatcher                $hookDispatcher
     * @param string                        $gridId
     */
    public function __construct(
        DoctrineQueryBuilderInterface $gridQueryBuilder,
        HookDispatcher $hookDispatcher,
        $gridId
    ) {
        $this->gridQueryBuilder = $gridQueryBuilder;
        $this->hookDispatcher = $hookDispatcher;
        $this->gridId = $gridId;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $searchQueryBuilder = $this->gridQueryBuilder->getSearchQueryBuilder($searchCriteria);
        $countQueryBuilder = $this->gridQueryBuilder->getCountQueryBuilder($searchCriteria);

        $records = $searchQueryBuilder->execute()->fetchAll();
        $recordsTotal = (int) $countQueryBuilder->execute()->fetch(PDO::FETCH_COLUMN);

        $this->hookDispatcher->dispatchForParameters('modifyGridQueryBuilder', [
            'grid_id' => $this->gridId,
            'search_query_builder' => $searchQueryBuilder,
            'count_query_builder' => $countQueryBuilder,
        ]);

        $records = new RecordCollection($records);

        return new GridData(
            $records,
            $recordsTotal,
            $searchQueryBuilder->getSQL()
        );
    }
}
