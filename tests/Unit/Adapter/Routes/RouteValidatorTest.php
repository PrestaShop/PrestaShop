<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
