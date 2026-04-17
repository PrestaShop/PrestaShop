<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Routes;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Routes\RouteValidator;

class RouteValidatorTest extends TestCase
{
    public function testIsRoutePatternReturnsTrueForValidPattern()
    {
        $validator = new RouteValidator();
        $this->assertTrue((bool) $validator->isRoutePattern('category/{id}-{rewrite}'));
    }

    public function testIsRoutePatternReturnsFalseForInvalidPattern()
    {
        $validator = new RouteValidator();
        $this->assertFalse((bool) $validator->isRoutePattern('category/id-rewrite$'));
    }

    public function testDoesRouteContainsRequiredKeywordsCallsIsRouteValid()
    {
        $validator = new RouteValidator();

        $this->assertFalse((bool) $validator->doesRouteContainsRequiredKeywords('category_rule', 'category/{id}'));
    }

    /**
     * @dataProvider routeValidationProvider
     */
    public function testIsRouteValid($routeId, $rule, $expected)
    {
        $validator = new RouteValidator();
        $result = $validator->isRouteValid($routeId, $rule);

        $this->assertEquals($expected, $result);
    }

    public function routeValidationProvider()
    {
        return [
            // Valid route
            [
                'category_rule',
                'category/{id}-{rewrite}',
                [],
            ],
            // Missing keyword
            [
                'category_rule',
                'category/{rewrite}',
                ['missing' => ['id'], 'unknown' => []],
            ],
            // Unknown keyword
            [
                'category_rule',
                'category/{id}-{rewrite}-{foo}',
                ['missing' => [], 'unknown' => ['foo']],
            ],
            // Both missing and unknown
            [
                'category_rule',
                'category/{rewrite}-{foo}',
                ['missing' => ['id'], 'unknown' => ['foo']],
            ],
            // Route id not found
            [
                'not_existing_rule',
                'category/{id}-{rewrite}',
                ['missing' => [], 'unknown' => []],
            ],
        ];
    }
}
