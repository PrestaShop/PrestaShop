<?php

class Adapter_Database implements Core_Foundation_Database_Database
{
    public function select($sqlString)
    {
        return Db::getInstance()->executeS($sqlString);
    }

    public function escape($unsafeData)
    {
        $html_ok = true;
        return Db::getInstance()->escape($unsafeData, $html_ok);
    }
}
