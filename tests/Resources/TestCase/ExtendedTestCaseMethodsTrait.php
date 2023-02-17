<?php

namespace Tests\Resources\TestCase;

use PHPUnit\Framework\Constraint\IsEqual;

trait ExtendedTestCaseMethodsTrait
{
    public function assertEqualsWithEpsilon($expected, $actual): bool
    {
        return abs($actual - $expected) < 0.0000000001;
    }
}
