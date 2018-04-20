<?php

namespace PrestaShop\PrestaShop\Core\Table\DataProvider;

use PrestaShopBundle\Entity\Repository\RequestSqlRepository;
use PrestaShopBundle\Service\Hook\HookDispatcher;

/**
 * Class RequestSqlTableDataProvider provides data for Request SQL table
 */
final class RequestSqlTableDataProvider implements TableDataProviderInterface
{
    /**
     * @var HookDispatcher
     */
    private $dispatcher;

    /**
     * @var RequestSqlRepository
     */
    private $repository;

    public function __construct(
        HookDispatcher $dispatcher,
        RequestSqlRepository $repository
    ) {
        $this->dispatcher = $dispatcher;
        $this->repository = $repository;
    }

    public function getRows(array $filters)
    {
        $requestSqlsQuery = $this->repository->getFindByFiltersQuery($filters);

        $this->dispatcher->dispatchForParameters('modifyRequestSqlTableQuery', [
            'query' => $requestSqlsQuery,
            'filters' => $filters,
        ]);

        $requestSqls = $requestSqlsQuery->execute()->fetchAll(\PDO::FETCH_ASSOC);

        return $requestSqls;
    }

    public function getRowsTotal()
    {
        return $this->repository->getCount();
    }
}
