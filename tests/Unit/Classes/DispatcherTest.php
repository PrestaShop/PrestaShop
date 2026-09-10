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

    /**
     * A customised route rule may deliberately omit a keyword that the DEFAULT route still declares -
     * removing {id} from the category schema in SEO & URLs is a supported option. That parameter is
     * consumed by the route, so it must not be appended to the query string.
     *
     * @see https://github.com/PrestaShop/PrestaShop/issues/42440
     *
     * @dataProvider createUrlKeywordProvider
     */
    public function testCreateUrlDropsKeywordsOfTheDefaultRoute($useRoutes, $rule, $params, $expected)
    {
        $dispatcher = DispatcherCore::getInstance();
        $reflection = new ReflectionClass($dispatcher);

        $defaultRoutes = [
            'category_rule' => [
                'controller' => 'category',
                'rule' => '{id}-{rewrite}',
                'keywords' => [
                    'id' => ['regexp' => '[0-9]+', 'param' => 'id_category'],
                    'rewrite' => ['regexp' => '[_a-zA-Z0-9\pL\pS-]*'],
                    'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                ],
            ],
        ];

        $defaultRoutesProperty = $reflection->getProperty('default_routes');
        $defaultRoutesProperty->setAccessible(true);
        $defaultRoutesProperty->setValue($dispatcher, $defaultRoutes);

        // Only the keywords the customised rule actually uses stay in the computed route, which is
        // what loadRoutes() produces for a merchant-defined schema.
        $keywords = [];
        foreach ($defaultRoutes['category_rule']['keywords'] as $keyword => $data) {
            if (strpos($rule, '{' . $keyword . '}') !== false) {
                $keywords[$keyword] = $data;
            }
        }

        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routesProperty->setValue($dispatcher, [
            1 => [
                1 => [
                    'category_rule' => $dispatcher->computeRoute($rule, 'category', $keywords),
                ],
            ],
        ]);

        $useRoutesProperty = $reflection->getProperty('use_routes');
        $useRoutesProperty->setAccessible(true);
        $useRoutesProperty->setValue($dispatcher, $useRoutes);

        $this->assertSame($expected, $dispatcher->createUrl('category_rule', 1, $params, false, '', 1));
    }

    public function createUrlKeywordProvider()
    {
        return [
            // Native schema: {id} is in the rule, so it is substituted and never appended.
            'friendly, default rule' => [
                true,
                '{id}-{rewrite}',
                ['id' => 3, 'rewrite' => 'clothes'],
                '3-clothes',
            ],
            // Customised schema without {id}: it is still a default-route keyword, so it is dropped
            // rather than appended as ?id=3.
            'friendly, id removed from the rule' => [
                true,
                '{rewrite}',
                ['id' => 3, 'rewrite' => 'clothes'],
                'clothes',
            ],
            // A parameter that is not a keyword of either rule is genuinely extra and must survive.
            'friendly, unrelated parameter is kept' => [
                true,
                '{rewrite}',
                ['id' => 3, 'rewrite' => 'clothes', 'search_query' => 'shoes'],
                'clothes?search_query=shoes',
            ],
            // A keyword the DEFAULT route declares but the rule does not use (meta_title is declared
            // by category_rule yet absent from '{id}-{rewrite}') is also route data rather than an
            // extra parameter, so it is dropped as well. Link::getCategoryLink() only passes it when
            // hasKeyword() says the computed route uses it, so core's own links are unaffected; this
            // pins the behaviour for callers that build the URL directly.
            'friendly, default-route keyword absent from the rule' => [
                true,
                '{id}-{rewrite}',
                ['id' => 3, 'rewrite' => 'clothes', 'meta_title' => 'my-title'],
                '3-clothes',
            ],
            // #42426: Link::getProductLink() always passes id_product_attribute, null when there is no
            // combination. Appended as an extra parameter it produced an EMPTY query string that was
            // still prefixed with '?', and FrontController::canonicalRedirection() then compared
            // 'slug-p-1?' with the requested 'slug-p-1' and redirected forever. The rule is the same for
            // every route, so it is pinned here on category_rule.
            'friendly, default-route keyword passed as null leaves no dangling question mark' => [
                true,
                '{id}-{rewrite}',
                ['id' => 3, 'rewrite' => 'clothes', 'meta_title' => null],
                '3-clothes',
            ],
            // The same rule applies to the non friendly URL branch.
            'non friendly, id removed from the rule' => [
                false,
                '{rewrite}',
                ['id' => 3, 'rewrite' => 'clothes'],
                'index.php?controller=category',
            ],
        ];
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
