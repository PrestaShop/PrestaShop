<?php

namespace Tests\Resources\TestCase;

use PHPUnit\Framework\Assert;

trait ExtendedTestCaseMethodsTrait
{
    public function assertEqualsWithEpsilon($expected, $actual, $message = '')
    {
        $success = false;

        // @see https://github.com/sebastianbergmann/phpunit/issues/4966#issuecomment-1367081755 for `0.0000000001`
        if (abs($expected - $actual) < 0.0000000001) {
            $success = true;
        }

        Assert::assertTrue($success);
    }
}
