<?php

namespace PrestaShop\PrestaShop\Core\Table\Factory;

use PrestaShop\PrestaShop\Core\Table\Definition\TableDefinitionInterface;
use PrestaShop\PrestaShop\Core\Table\Table;
use Symfony\Component\HttpFoundation\Request;

interface TableFactoryInterface
{
    /**
     * Create new table from it's definition
     *
     * @param TableDefinitionInterface $tableDefinition
     * @param Request $request
     *
     * @return Table
     */
    public function createFromDefinition(TableDefinitionInterface $tableDefinition, Request $request);
}
