<?php

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC\Fixtures;

class CycleB
{
    public function __construct(CycleA $a)
    {
    }
}
