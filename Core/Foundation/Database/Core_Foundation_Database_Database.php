<?php

interface Core_Foundation_Database_Database
{
    public function select($sqlString);
    public function escape($unsafeData);
}
