<?php

declare(strict_types=1);
class DummyAdminController extends DummyAdminControllerCore
{
    /*
    * module: pscsx32412
    * date: 2023-08-10 11:19:47
    * version: 1
    */
    public function checkAccess()
    {
        return false;
    }

    /*
    * module: pscsx3241
    * date: 2023-08-10 11:19:48
    * version: 1
    */
    protected function buildContainer()
    {
        return null;
    }
}
