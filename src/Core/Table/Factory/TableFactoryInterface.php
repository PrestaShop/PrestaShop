<?php

namespace PrestaShop\PrestaShop\Core\Table\Factory;

use PrestaShop\PrestaShop\Core\Table\DataProvider\TableDataProviderInterface;
use PrestaShop\PrestaShop\Core\Table\Definition\TableDefinitionInterface;
use PrestaShop\PrestaShop\Core\Table\Table;
use Symfony\Component\HttpFoundation\Request;

interface TableFactoryInterface
{
    /**
     * @param TableDefinitionInterface $tableDefinition
     *
     * @return Table
     */
    public function createFromDefinition(TableDefinitionInterface $tableDefinition);
}
