<?php

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures;

class ClassWithDepAndDefault
{
    private $dummy;
    private $something;

    public function __construct(Dummy $dummy, $something = 4)
    {
        $this->dummy = $dummy;
        $this->something = $something;
    }
}
