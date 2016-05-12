<?php

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures;

class ClassWithDep
{
    private $dummy;

    public function __construct(Dummy $dummy)
    {
        $this->dummy = $dummy;
    }
}
