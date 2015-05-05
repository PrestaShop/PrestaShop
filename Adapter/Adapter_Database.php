<?php

class Adapter_Database implements Core_Foundation_Database_Database
{
    public function select($sqlString)
    {
        return Db::getInstance()->executeS($sqlString);
    }

    public function escape($unsafeData)
    {
        return Db::getInstance()->escape($unsafeData);
    }
}
