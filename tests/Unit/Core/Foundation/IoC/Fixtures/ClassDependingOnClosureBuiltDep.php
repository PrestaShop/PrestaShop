<?php

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures;

class ClassDependingOnClosureBuiltDep
{
    private $dep;

    public function __construct(DepBuiltByClosure $dep)
    {
        $this->dep = $dep;
    }

    public function getDep()
    {
        return $this->dep;
    }
}
