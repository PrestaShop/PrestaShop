<?php

class Adapter_EntityMetaDataRetriever
{
    public function getEntityMetaData($className)
    {
        $metaData = new Core_Foundation_Database_EntityMetaData;

        $metaData->setEntityClassName($className);

        if (property_exists($className, 'definition')) {
            // Legacy entity
            $metaData->setTableName($className::$definition['table']);
            $metaData->setPrimaryKeyFieldNames(array($className::$definition['primary']));
        } else {
            throw new Exception(
                sprintf(
                    'Cannot get metadata for entity `%s`.',
                    $className
                )
            );
        }

        return $metaData;
    }
}
