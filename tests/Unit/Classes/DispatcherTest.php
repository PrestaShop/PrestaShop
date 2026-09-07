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
     * @dataProvider createUrlProvider
     */
    public function testCreateUrl(string $routeName, string $rule, array $params, bool $useRoutes, string $expectedUrl): void
    {
        // Create an isolated dispatcher without loading shop configuration
        $reflection = new ReflectionClass(DispatcherCore::class);
        $dispatcher = $reflection->newInstanceWithoutConstructor();
        $property = $reflection->getProperty('use_routes');
        $property->setAccessible(true);
        $property->setValue($dispatcher, $useRoutes);

        // Compile the supplied rule with product keywords, including for a module route
        $route = $dispatcher->computeRoute($rule, 'product', $dispatcher->default_routes['product_rule']['keywords']);
        $property = $reflection->getProperty('routes');
        $property->setAccessible(true);
        $property->setValue($dispatcher, [1 => [1 => [$routeName => $route]]]);

        // Preserve the anchor after the path and any encoded query parameters
        $this->assertSame($expectedUrl . '#details', $dispatcher->createUrl($routeName, 1, $params, false, '#details', 1));
    }

    /**
     * Provide native and module routes with omitted keywords and extra parameters.
     */
    public static function createUrlProvider(): array
    {
        return [
            // Keywords omitted from custom rules must not become query parameters
            'omitted product ID' => [
                'product_rule', '{rewrite}', ['id' => 3, 'rewrite' => 'item'], true, 'item',
            ],
            'omitted null combination' => [
                'product_rule', '{rewrite}-p-{id}', ['id' => 3, 'rewrite' => 'item', 'id_product_attribute' => null], true, 'item-p-3',
            ],
            'omitted non-null combination' => [
                'product_rule', '{rewrite}-p-{id}', ['id' => 3, 'rewrite' => 'item', 'id_product_attribute' => 7], true, 'item-p-3',
            ],
            'omitted optional keyword' => [
                'product_rule', '{rewrite}-p-{id}', ['id' => 3, 'rewrite' => 'item', 'ean13' => '1234567890123'], true, 'item-p-3',
            ],

            // Required keywords still populate classic URLs while unused keywords are excluded
            'classic URL' => [
                'product_rule', '{rewrite}-p-{id}', ['id' => 3, 'rewrite' => 'item', 'id_product_attribute' => 7, 'page' => 2], false,
                'index.php?page=2&rewrite=item&id_product=3&controller=product',
            ],

            // Encoding empty extra parameters must not append a question mark
            'null extra parameter' => [
                'product_rule', '{rewrite}-p-{id}', ['id' => 3, 'rewrite' => 'item', 'extra' => null], true, 'item-p-3',
            ],
            'empty array extra parameter' => [
                'product_rule', '{rewrite}-p-{id}', ['id' => 3, 'rewrite' => 'item', 'extra' => []], true, 'item-p-3',
            ],

            // Valid extra parameters, including zero, false and empty strings, retain their meaning
            'mixed extra parameters' => [
                'product_rule', '{rewrite}-p-{id}', ['id' => 3, 'rewrite' => 'item', 'extra' => null, 'page' => 0, 'flag' => false, 'search' => ''], true,
                'item-p-3?page=0&flag=0&search=',
            ],

            // A module route without a default definition keeps its additional parameters
            'module extra parameter' => [
                'module-custom-product', '{rewrite}-p-{id}', ['id' => 3, 'rewrite' => 'item', 'id_product_attribute' => 7], true,
                'item-p-3?id_product_attribute=7',
            ],
            'module null parameter' => [
                'module-custom-product', '{rewrite}-p-{id}', ['id' => 3, 'rewrite' => 'item', 'extra' => null], true, 'item-p-3',
            ],
        ];
    }

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
