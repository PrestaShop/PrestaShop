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

use PrestaShop\PrestaShop\Core\Grid\Filtering\CriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShopBundle\Entity\Repository\LogRepository;
use PrestaShopBundle\Service\Hook\HookDispatcher;

/**
 * Class LogsGridDataProvider is responsible for providing data for Logs grid
 */
final class LogGridDataProvider implements GridDataProviderInterface
{
    /**
     * @var LogRepository
     */
    private $logRepository;

    /**
     * @var HookDispatcher
     */
    private $hookDispatcher;

    /**
     * @param LogRepository $logRepository
     * @param HookDispatcher $hookDispatcher
     */
    public function __construct(
        LogRepository $logRepository,
        HookDispatcher $hookDispatcher
    ) {
        $this->logRepository = $logRepository;
        $this->hookDispatcher = $hookDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getRows(SearchCriteriaInterface  $searchCriteria)
    {
        $logQuery = $this->logRepository->getAllWithEmployeeInformationQuery([
            'offset' => $searchCriteria->getOffset(),
            'limit' => $searchCriteria->getLimit(),
            'filters' => $searchCriteria->getFilters(),
            'orderBy' => $searchCriteria->getOrderBy(),
            'sortOrder' => $searchCriteria->getOrderWay(),
        ]);

        $this->hookDispatcher->dispatchForParameters('modifyLogGridQuery', [
            'query' => $logQuery,
            'search_criteria' => $searchCriteria,
        ]);

        $logs = $logQuery->execute()->fetchAll(\PDO::FETCH_ASSOC);

        return $logs;
    }

    /**
     * {@inheritdoc}
     */
    public function getRowsTotal()
    {
        return count($this->logRepository->findAll());
    }
}
