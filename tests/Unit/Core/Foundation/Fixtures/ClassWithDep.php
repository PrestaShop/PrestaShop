<?php

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\Fixtures;

class ClassWithDep
{
    private $dummy;

    public function __construct(Dummy $dummy)
    {
        $this->dummy = $dummy;
    }
}
