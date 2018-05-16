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

use PrestaShop\PrestaShop\Core\Grid\Row\RowCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShopBundle\Service\Hook\HookDispatcher;

/**
 * Class GridDataProvider is responsible for returing grid data
 */
final class GridDataProvider implements GridDataProviderInterface
{
    /**
     * @var GridQueryBuilderInterface
     */
    private $gridQueryBuilder;

    /**
     * @var HookDispatcher
     */
    private $hookDispatcher;

    public function __construct(
        GridQueryBuilderInterface $gridQueryBuilder,
        HookDispatcher $hookDispatcher
    ) {
        $this->gridQueryBuilder = $gridQueryBuilder;
        $this->hookDispatcher = $hookDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $searchQuery = $this->gridQueryBuilder->getSearchQueryBuilder($searchCriteria);
        $countQuery = $this->gridQueryBuilder->getCountQueryBuilder($searchCriteria);

        $this->hookDispatcher->dispatchForParameters('modifyQuery', [
            'search_query' => $searchQuery,
            'count_query' => $countQuery,
        ]);

        $rows = $searchQuery->execute()->fetchAll();
        $rowsTotal = (int) $countQuery->execute()->fetch(\PDO::FETCH_COLUMN);

        $rows = new RowCollection($rows);

        return new GridData(
            $rows,
            $rowsTotal,
            $searchQuery->getSQL()
        );
    }
}
