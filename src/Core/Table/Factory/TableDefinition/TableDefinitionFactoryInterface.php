<?php

namespace PrestaShop\PrestaShop\Core\Table\Factory\TableDefinition;

use PrestaShop\PrestaShop\Core\Table\Definition\TableDefinitionInterface;

interface TableDefinitionFactoryInterface
{
    /**
     * Create new table definition
     *
     * @return TableDefinitionInterface
     */
    public function createNew();
}
