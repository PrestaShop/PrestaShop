<?php

namespace Tests\Resources\TestCase;

use PHPUnit\Framework\Assert;

trait ExtendedTestCaseMethodsTrait
{
    private function compareWithEpsilon($expected, $actual)
    {
        $success = false;

        // see https://github.com/sebastianbergmann/phpunit/issues/4966#issuecomment-1367081755
        if (abs($expected - $actual) < 10 ** -ini_get('precision')) {
            $success = true;
        }

        Assert::assertTrue($success);
    }

    public function assertEqualsWithEpsilon($expected, $actual, $message = '')
    {
        if (!is_array($expected)) {
            $expectedArray[]['a'] = $expected; // we recreate the structure of the array as in Tools::spreadAmount()
        } else {
            $expectedArray = $expected;
        }

        if (!is_array($actual)) {
            $actualArray[]['a'] = $actual; // we recreate the structure of the array as in Tools::spreadAmount()
        } else {
            $actualArray = $actual;
        }

        foreach ($expectedArray as $key => $item) {
            $this->compareWithEpsilon($item['a'], $actualArray[$key]['a']);
        }
    }
}
