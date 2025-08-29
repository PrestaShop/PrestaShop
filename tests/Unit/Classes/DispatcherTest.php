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

namespace Tests\Unit\Classes;

use DispatcherCore;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class DispatcherTest extends TestCase
{
    /**
     * @dataProvider validateRouteProvider
     */
    public function testValidateRoute($routeId, $rule, $defaultRoutes, $expectedResult, $expectedErrors)
    {
        $dispatcher = DispatcherCore::getInstance();

        // Inject default_routes property
        $reflection = new ReflectionClass($dispatcher);
        $property = $reflection->getProperty('default_routes');
        $property->setAccessible(true);
        $property->setValue($dispatcher, $defaultRoutes);

        $errors = [];
        $result = $dispatcher->validateRoute($routeId, $rule, $errors);

        $this->assertSame($expectedResult, $result);
        $this->assertEquals($expectedErrors, $errors);
    }

    public function validateRouteProvider()
    {
        return [
            // Valid route: all keywords present, none unknown
            [
                'category_rule',
                'category/{id}-{rewrite}',
                [
                    'category_rule' => [
                        'controller' => 'category',
                        'rule' => 'category/{id}-{rewrite}',
                        'keywords' => [
                            'id' => ['regexp' => '[0-9]+', 'param' => 'id_category'],
                            'rewrite' => ['regexp' => '[_a-zA-Z0-9-]*'],
                        ],
                    ],
                ],
                true,
                ['missing' => [], 'unknown' => []],
            ],
            // Missing keyword
            [
                'category_rule',
                'category/{rewrite}',
                [
                    'category_rule' => [
                        'controller' => 'category',
                        'rule' => 'category/{id}-{rewrite}',
                        'keywords' => [
                            'id' => ['regexp' => '[0-9]+', 'param' => 'id_category'],
                            'rewrite' => ['regexp' => '[_a-zA-Z0-9-]*'],
                        ],
                    ],
                ],
                false,
                ['missing' => ['id'], 'unknown' => []],
            ],
            // Unknown keyword
            [
                'category_rule',
                'category/{id}-{rewrite}-{foo}',
                [
                    'category_rule' => [
                        'controller' => 'category',
                        'rule' => 'category/{id}-{rewrite}',
                        'keywords' => [
                            'id' => ['regexp' => '[0-9]+', 'param' => 'id_category'],
                            'rewrite' => ['regexp' => '[_a-zA-Z0-9-]*'],
                        ],
                    ],
                ],
                false,
                ['missing' => [], 'unknown' => ['foo']],
            ],
            // Both missing and unknown
            [
                'category_rule',
                'category/{rewrite}-{foo}',
                [
                    'category_rule' => [
                        'controller' => 'category',
                        'rule' => 'category/{id}-{rewrite}',
                        'keywords' => [
                            'id' => ['regexp' => '[0-9]+', 'param' => 'id_category'],
                            'rewrite' => ['regexp' => '[_a-zA-Z0-9-]*'],
                        ],
                    ],
                ],
                false,
                ['missing' => ['id'], 'unknown' => ['foo']],
            ],
            // Route id not found
            [
                'not_existing_rule',
                'category/{id}-{rewrite}',
                [
                    'category_rule' => [
                        'controller' => 'category',
                        'rule' => 'category/{id}-{rewrite}',
                        'keywords' => [
                            'id' => ['regexp' => '[0-9]+', 'param' => 'id_category'],
                            'rewrite' => ['regexp' => '[_a-zA-Z0-9-]*'],
                        ],
                    ],
                ],
                false,
                ['missing' => [], 'unknown' => []],
            ],
        ];
    }
}
