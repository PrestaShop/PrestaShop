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

use Doctrine\DBAL\Driver\Connection;
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
     * @var Connection
     */
    private $connection;

    /**
     * @param LogRepository $logRepository
     * @param HookDispatcher $hookDispatcher
     * @param Connection $connection
     */
    public function __construct(
        LogRepository $logRepository,
        HookDispatcher $hookDispatcher,
        Connection $connection
    ) {
        $this->logRepository = $logRepository;
        $this->hookDispatcher = $hookDispatcher;
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getRows(SearchCriteriaInterface  $searchCriteria)
    {
        $logSqlQuery = $this->getQuery($searchCriteria);

        $stmt = $this->connection->prepare($logSqlQuery);
        $stmt->execute();

        $logs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $logs;
    }

    /**
     * {@inheritdoc}
     */
    public function getRowsTotal()
    {
        return count($this->logRepository->findAll());
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery(SearchCriteriaInterface $searchCriteria)
    {
        $logQueryBuilder = $this->logRepository->getAllWithEmployeeInformationQuery([
            'offset' => $searchCriteria->getOffset(),
            'limit' => $searchCriteria->getLimit(),
            'filters' => $searchCriteria->getFilters(),
            'orderBy' => $searchCriteria->getOrderBy(),
            'sortOrder' => $searchCriteria->getOrderWay(),
        ]);

        $this->hookDispatcher->dispatchForParameters('modifyLogGridQueryBuilder', [
            'query_builder' => $logQueryBuilder,
            'search_criteria' => $searchCriteria,
        ]);

        return $logQueryBuilder->getSQL();
    }
}
