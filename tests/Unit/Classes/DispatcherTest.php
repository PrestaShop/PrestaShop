<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
