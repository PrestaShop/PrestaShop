<?php

namespace Tests\Resources\TestCase;

use PHPUnit\Framework\Assert;

trait ExtendedTestCaseMethodsTrait
{
    private function compareWithEpsilon($expected, $actual)
    {
        $success = false;

        if (abs($expected - $actual) < 0.0000000001) {
            $success = true;
        }

        Assert::assertTrue($success);
    }

    public function assertEqualsWithEpsilon($expected, $actual, $message = '')
    {
        if (!is_array($expected)) {
            $expectedArray[] = $expected;
        } else {
            $expectedArray = $expected;
        }

        if (!is_array($actual)) {
            $actualArray[] = $actual;
        } else {
            $actualArray = $actual;
        }

        foreach ($expectedArray as $key => $item) {
            $this->compareWithEpsilon($item, $actualArray[$key]);
        }
    }
}
