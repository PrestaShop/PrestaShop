<?php

class Core_Foundation_Database_EntityMetaData
{
    private $tableName;
    private $primaryKeyFieldnames;

    public function setTableName($name)
    {
        $this->tableName = $name;
        return $this;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function setPrimaryKeyFieldNames(array $primaryKeyFieldnames)
    {
        $this->primaryKeyFieldnames = $primaryKeyFieldnames;
        return $this;
    }

    public function getPrimaryKeyFieldnames()
    {
        return $this->primaryKeyFieldnames;
    }

    public function setEntityClassName($entityClassName)
    {
        $this->entityClassName = $entityClassName;
        return $this;
    }

    public function getEntityClassName()
    {
        return $this->entityClassName;
    }
}
