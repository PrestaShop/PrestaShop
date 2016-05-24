<?php

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures;

class CycleA
{
    public function __construct(CycleB $b)
    {
    }
}
